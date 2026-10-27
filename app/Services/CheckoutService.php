<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\PosTransaction;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\StoreSetting;
use App\Models\TransactionDraft;
use App\Models\User;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CheckoutService
{
    public function __construct(private readonly PosNotifier $notifier) {}

    /** @param array<string, mixed> $data */
    public function checkout(User $actor, array $data): PosTransaction
    {
        $existing = PosTransaction::where('idempotency_key', $data['idempotency_key'])->first();
        if ($existing) {
            return $this->existingFor($existing, $actor);
        }

        try {
            return DB::transaction(function () use ($actor, $data): PosTransaction {
                $draft = null;
                if (! empty($data['draft_id'])) {
                    $draft = TransactionDraft::query()->lockForUpdate()->findOrFail($data['draft_id']);
                    if (! $actor->isAdmin() && $draft->cashier_id !== $actor->id) {
                        abort(403);
                    }
                    if ($draft->status !== 'active' || $draft->expires_at->isPast()) {
                        throw ValidationException::withMessages(['draft' => 'Draft sudah tidak aktif atau kedaluwarsa.']);
                    }
                    $saved = collect($draft->payload_json['items'] ?? [])->mapWithKeys(fn ($item) => [(int) $item['product_id'] => (int) $item['qty']])->all();
                    $submitted = collect($data['items'])->mapWithKeys(fn ($item) => [(int) $item['product_id'] => (int) $item['qty']])->all();
                    if ($saved !== $submitted) {
                        throw ValidationException::withMessages(['draft' => 'Isi draft berubah. Simpan ulang sebelum checkout.']);
                    }
                }

                $qtyById = collect($data['items'])->mapWithKeys(fn ($item) => [(int) $item['product_id'] => (int) $item['qty']])->sortKeys()->all();
                $products = Product::query()->with('unit')->whereIn('id', array_keys($qtyById))->orderBy('id')->lockForUpdate()->get()->keyBy('id');
                if ($products->count() !== count($qtyById)) {
                    throw ValidationException::withMessages(['items' => 'Produk tidak ditemukan.']);
                }

                $subtotal = 0;
                foreach ($qtyById as $id => $qty) {
                    $product = $products[$id];
                    if (! $product->is_active || $product->stock_qty < $qty) {
                        throw ValidationException::withMessages(['items' => "Produk {$product->name} nonaktif atau stok tidak cukup."]);
                    }
                    $subtotal += $product->sell_price * $qty;
                }
                $discount = (int) ($data['discount_amount'] ?? 0);
                if ($discount > $subtotal) {
                    throw ValidationException::withMessages(['discount_amount' => 'Diskon melebihi subtotal.']);
                }
                $settings = StoreSetting::current();
                $basis = (int) round((float) $settings->tax_rate * 100);
                $tax = intdiv(($subtotal - $discount) * $basis + 5000, 10000);
                $total = $subtotal - $discount + $tax;
                $isMidtrans = $data['payment_method'] === 'midtrans';
                $isCredit = $data['payment_method'] === 'credit';
                $paid = (int) ($data['amount_received'] ?? 0);
                if ($isMidtrans && $total <= 0) {
                    throw ValidationException::withMessages(['payment_method' => 'Total pembayaran Midtrans harus lebih dari nol.']);
                }
                if ($isCredit) {
                    if ($total <= 0 || ! Customer::query()->whereKey($data['customer_id'] ?? null)->where('is_active', true)->lockForUpdate()->exists()) {
                        throw ValidationException::withMessages(['customer_id' => 'Pilih pelanggan aktif dan pastikan total kasbon lebih dari nol.']);
                    }
                } elseif (! $isMidtrans && (($data['payment_method'] === 'cash' && $paid < $total) || ($data['payment_method'] !== 'cash' && $paid !== $total))) {
                    throw ValidationException::withMessages(['amount_received' => 'Nominal pembayaran tidak sesuai total.']);
                }

                if ($draft) {
                    foreach ($qtyById as $id => $qty) {
                        $snapshot = collect($draft->payload_json['items'])->firstWhere('product_id', $id);
                        if ($snapshot && (int) ($snapshot['unit_price'] ?? -1) !== $products[$id]->sell_price && ! ($data['acknowledge_changes'] ?? false)) {
                            throw ValidationException::withMessages(['draft' => 'Harga produk berubah. Tinjau ulang draft dan konfirmasi perubahan.']);
                        }
                    }
                }

                $transaction = PosTransaction::create([
                    'transaction_number' => 'POS-'.now($settings->timezone)->format('Ymd').'-'.Str::upper(Str::ulid()),
                    'cashier_id' => $actor->id, 'customer_id' => $isCredit ? $data['customer_id'] : null, 'draft_id' => $draft?->id, 'status' => $isMidtrans ? 'pending' : 'completed', 'subtotal' => $subtotal,
                    'discount_amount' => $discount, 'tax_rate' => $settings->tax_rate, 'tax_amount' => $tax,
                    'grand_total' => $total, 'payment_method' => $data['payment_method'],
                    'amount_received' => ($isMidtrans || $isCredit) ? 0 : $paid, 'change_amount' => ($isMidtrans || $isCredit) ? 0 : $paid - $total,
                    'idempotency_key' => $data['idempotency_key'], 'completed_at' => $isMidtrans ? null : now(),
                    'midtrans_status' => $isMidtrans ? 'pending' : null,
                ]);

                if ($isCredit) {
                    $transaction->receivable()->create([
                        'customer_id' => $data['customer_id'], 'total_amount' => $total,
                        'due_date' => $data['due_date'] ?? null, 'note' => $data['credit_note'] ?? null,
                        'status' => 'open',
                    ]);
                }

                foreach ($qtyById as $id => $qty) {
                    $product = $products[$id];
                    $before = $product->stock_qty;
                    $transaction->items()->create([
                        'product_id' => $id, 'category_id' => $product->category_id, 'product_name' => $product->name, 'product_sku' => $product->sku,
                        'unit_name' => $product->unit->symbol, 'unit_price' => $product->sell_price,
                        'qty' => $qty, 'line_total' => $product->sell_price * $qty,
                    ]);
                    $product->stock_qty = $before - $qty;
                    $product->save();
                    StockMovement::create([
                        'product_id' => $id, 'user_id' => $actor->id, 'transaction_id' => $transaction->id,
                        'type' => $isMidtrans ? 'reservation' : 'sale', 'delta_qty' => -$qty, 'before_qty' => $before,
                        'after_qty' => $before - $qty, 'reason' => $transaction->transaction_number,
                    ]);
                }
                if ($draft) {
                    $draft->update(['status' => $isMidtrans ? 'pending_payment' : 'completed']);
                }

                if (! $isMidtrans) {
                    $this->notifier->sale($transaction);
                }

                return $transaction;
            }, 3);
        } catch (UniqueConstraintViolationException $exception) {
            $existing = PosTransaction::where('idempotency_key', $data['idempotency_key'])->first();
            if ($existing) {
                return $this->existingFor($existing, $actor);
            }
            throw $exception;
        }
    }

    private function existingFor(PosTransaction $transaction, User $actor): PosTransaction
    {
        if ($transaction->cashier_id !== $actor->id) {
            abort(403);
        }

        return $transaction;
    }
}
