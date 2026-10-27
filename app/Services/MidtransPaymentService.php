<?php

namespace App\Services;

use App\Models\PosTransaction;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\TransactionDraft;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Throwable;

class MidtransPaymentService
{
    public function __construct(private readonly MidtransGateway $gateway, private readonly PosNotifier $notifier) {}

    public function start(PosTransaction $transaction): PosTransaction
    {
        if ($transaction->payment_method !== 'midtrans' || $transaction->status !== 'pending') {
            return $transaction;
        }

        return Cache::lock('midtrans-start-'.$transaction->id, 25)->block(10, function () use ($transaction): PosTransaction {
            $transaction->refresh();
            if ($transaction->status !== 'pending' || $transaction->midtrans_snap_token) {
                return $transaction;
            }

            try {
                $session = $this->gateway->create($transaction->loadMissing('cashier'));
            } catch (Throwable $exception) {
                $transaction->update(['midtrans_status' => 'initiation_error']);
                throw $exception;
            }
            $transaction->update(['midtrans_redirect_url' => $session['url'], 'midtrans_snap_token' => $session['token'], 'midtrans_status' => 'pending']);

            return $transaction;
        });
    }

    public function check(PosTransaction $transaction): PosTransaction
    {
        if ($transaction->payment_method !== 'midtrans' || $transaction->status !== 'pending') {
            return $transaction;
        }

        $status = $this->gateway->status($transaction);
        if ($status === null) {
            return $transaction;
        }
        $this->validateStatus($transaction, $status);
        $remoteStatus = (string) $status['transaction_status'];
        $fraudStatus = (string) ($status['fraud_status'] ?? '');

        if (in_array($remoteStatus, ['settlement', 'capture'], true) && in_array($fraudStatus, ['', 'accept'], true)) {
            $this->complete($transaction, $remoteStatus);
        } elseif (in_array($remoteStatus, ['deny', 'cancel', 'expire', 'failure', 'refund', 'chargeback', 'partial_refund', 'partial_chargeback'], true) || ($remoteStatus === 'capture' && $fraudStatus === 'deny')) {
            $this->release($transaction, $remoteStatus);
        } else {
            $transaction->refresh();
            if ($transaction->status === 'pending') {
                $transaction->update(['midtrans_status' => $remoteStatus]);
            }
        }

        return $transaction->refresh();
    }

    /** @param array<string, mixed> $status */
    private function validateStatus(PosTransaction $transaction, array $status): void
    {
        $gross = trim((string) ($status['gross_amount'] ?? ''));
        $validGross = preg_match('/^(0|[1-9][0-9]*)(?:\.(0{1,2}))?$/', $gross) === 1
            && (int) $gross === (int) $transaction->grand_total;
        if ((string) ($status['order_id'] ?? '') !== $transaction->transaction_number
            || ! $validGross
            || ! is_string($status['transaction_status'] ?? null)) {
            throw new RuntimeException('Respons status Midtrans tidak cocok dengan transaksi ini.');
        }
    }

    private function complete(PosTransaction $transaction, string $remoteStatus): void
    {
        DB::transaction(function () use ($transaction, $remoteStatus): void {
            $locked = PosTransaction::query()->lockForUpdate()->findOrFail($transaction->id);
            if ($locked->status !== 'pending') {
                return;
            }
            $locked->update([
                'status' => 'completed', 'midtrans_status' => $remoteStatus,
                'amount_received' => $locked->grand_total, 'change_amount' => 0, 'completed_at' => now(),
            ]);
            if ($locked->draft_id) {
                TransactionDraft::query()->whereKey($locked->draft_id)->where('status', 'pending_payment')->update(['status' => 'completed']);
            }
            $this->notifier->sale($locked);
        }, 3);
    }

    private function release(PosTransaction $transaction, string $remoteStatus): void
    {
        DB::transaction(function () use ($transaction, $remoteStatus): void {
            $locked = PosTransaction::query()->with('items')->lockForUpdate()->findOrFail($transaction->id);
            if ($locked->status !== 'pending') {
                return;
            }
            $items = $locked->items->sortBy('product_id');
            $products = Product::withTrashed()->whereIn('id', $items->pluck('product_id'))->orderBy('id')->lockForUpdate()->get()->keyBy('id');
            foreach ($items as $item) {
                $product = $products[$item->product_id];
                $before = $product->stock_qty;
                $product->stock_qty = $before + $item->qty;
                $product->save();
                StockMovement::create([
                    'product_id' => $product->id, 'user_id' => $locked->cashier_id, 'transaction_id' => $locked->id,
                    'type' => 'release', 'delta_qty' => $item->qty, 'before_qty' => $before,
                    'after_qty' => $product->stock_qty, 'reason' => 'Pembayaran '.$remoteStatus.' · '.$locked->transaction_number,
                ]);
            }
            $locked->update(['status' => 'failed', 'midtrans_status' => $remoteStatus]);
            $draftId = $locked->draft_id;
            if ($draftId) {
                $draft = TransactionDraft::query()->lockForUpdate()->find($draftId);
                if ($draft?->status === 'pending_payment') {
                    $draft->update(['status' => $draft->expires_at->isFuture() ? 'active' : 'cancelled']);
                }
            }
        }, 3);
    }
}
