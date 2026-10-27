<?php

namespace App\Http\Controllers\Pos;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pos\CheckoutRequest;
use App\Models\Customer;
use App\Models\Product;
use App\Models\StoreSetting;
use App\Models\TransactionDraft;
use App\Services\CheckoutService;
use App\Services\MidtransGateway;
use App\Services\MidtransPaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use RuntimeException;

class CheckoutController extends Controller
{
    public function create(Request $request): View
    {
        $filters = $request->validate(['q' => ['nullable', 'string', 'max:100'], 'draft' => ['nullable', 'integer', 'exists:transaction_drafts,id'], 'customer_id' => ['nullable', 'integer', 'exists:customers,id']]);
        $draft = isset($filters['draft']) ? TransactionDraft::findOrFail($filters['draft']) : null;
        if ($draft && ! $request->user()->isAdmin() && $draft->cashier_id !== $request->user()->id) {
            abort(403);
        }
        if ($draft && ($draft->status !== 'active' || $draft->expires_at->isPast())) {
            abort(422, 'Draft tidak aktif atau kedaluwarsa.');
        }
        $products = Product::query()->with('category')->where('is_active', true)->where('stock_qty', '>', 0)
            ->when($filters['q'] ?? null, fn ($query, $q) => $query->where(fn ($query) => $query->where('name', 'like', '%'.$q.'%')->orWhere('sku', 'like', '%'.$q.'%')->orWhere('barcode', $q)))
            ->orderBy('name')->paginate(15)->withQueryString();

        $saved = collect($draft?->payload_json['items'] ?? []);
        $selected = Product::whereIn('id', $saved->pluck('product_id'))->get()->keyBy('id');
        $initialCart = $saved->filter(fn ($item) => isset($selected[$item['product_id']]))->map(fn ($item) => [
            'id' => (int) $item['product_id'], 'name' => $selected[$item['product_id']]->name,
            'price' => $selected[$item['product_id']]->sell_price, 'stock' => $selected[$item['product_id']]->stock_qty,
            'image' => $selected[$item['product_id']]->image_path ? asset('storage/'.$selected[$item['product_id']]->image_path) : null,
            'qty' => (int) $item['qty'],
        ])->values()->all();

        return view('pos.cashier', ['products' => $products, 'customers' => Customer::where('is_active', true)->orderBy('name')->pluck('name', 'id'), 'selectedCustomerId' => $filters['customer_id'] ?? null, 'settings' => StoreSetting::current(), 'key' => (string) Str::uuid(), 'draft' => $draft, 'initialCart' => $initialCart]);
    }

    public function lookupBarcode(Request $request): JsonResponse
    {
        $data = $request->validate(['barcode' => ['required', 'string', 'max:255']]);
        $product = Product::query()->where('barcode', $data['barcode'])->where('is_active', true)->first();
        if (! $product) {
            return response()->json(['message' => 'Barcode tidak ditemukan pada produk aktif.'], 404);
        }

        return response()->json([
            'id' => $product->id,
            'barcode' => $product->barcode,
            'sku' => $product->sku,
            'name' => $product->name,
            'price' => $product->sell_price,
            'stock' => $product->stock_qty,
            'image' => $product->image_path ? asset('storage/'.$product->image_path) : null,
        ])->header('Cache-Control', 'no-store');
    }

    public function store(CheckoutRequest $request, CheckoutService $checkout, MidtransGateway $gateway, MidtransPaymentService $payments): RedirectResponse|JsonResponse
    {
        if ($request->validated('payment_method') === 'midtrans' && ! $gateway->configured()) {
            throw ValidationException::withMessages(['payment_method' => 'MIDTRANS_SERVER_KEY dan MIDTRANS_CLIENT_KEY harus diisi pada .env.']);
        }
        $transaction = $checkout->checkout($request->user(), $request->validated());
        $cartStorageKey = ! empty($request->validated('draft_id')) ? 'pos-draft-'.$request->validated('draft_id').'-'.$request->user()->id : 'pos-cart-'.$request->user()->id;
        if ($transaction->payment_method === 'midtrans' && $transaction->status === 'pending') {
            try {
                $payments->start($transaction);
            } catch (RuntimeException $exception) {
                if ($request->expectsJson()) {
                    return response()->json(['transaction_url' => route('pos.transactions.show', $transaction), 'warning' => $exception->getMessage()]);
                }

                return redirect()->route('pos.transactions.show', $transaction)
                    ->withErrors(['payment' => $exception->getMessage()])
                    ->with('cart_storage_key', $cartStorageKey);
            }
        }

        if ($request->expectsJson()) {
            if ($transaction->status === 'completed') {
                $request->session()->flash('status', $transaction->payment_method === 'credit' ? 'Kasbon berhasil dicatat. Tagihan pelanggan masih terbuka.' : 'Transaksi berhasil diselesaikan.');
            }

            return response()->json([
                'transaction_url' => route('pos.transactions.show', $transaction),
                'check_url' => route('pos.transactions.check-payment', $transaction),
                'snap_token' => $transaction->status === 'pending' ? $transaction->midtrans_snap_token : null,
                'status' => $transaction->status,
            ]);
        }

        return redirect()->route('pos.transactions.show', $transaction)
            ->with('status', match ($transaction->status) {
                'pending' => 'Sesi pembayaran Midtrans dibuat. Selesaikan pembayaran, lalu cek statusnya.',
                'completed' => $transaction->payment_method === 'credit' ? 'Kasbon berhasil dicatat. Tagihan pelanggan masih terbuka.' : 'Transaksi berhasil diselesaikan.',
                default => 'Pembayaran sebelumnya gagal. Buat transaksi baru untuk mencoba lagi.',
            })
            ->with('cart_storage_key', $cartStorageKey);
    }
}
