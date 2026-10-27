<?php

namespace App\Services;

use App\Models\PosNotification;
use App\Models\PosTransaction;
use App\Models\Receivable;
use App\Models\ReceivablePayment;
use App\Models\StockMovement;
use App\Models\TransactionDraft;
use App\Models\User;

class PosNotifier
{
    public function receivablePayment(Receivable $receivable, ReceivablePayment $payment): void
    {
        foreach (User::query()->where('is_active', true)->where(fn ($query) => $query->where('role', 'admin')->orWhere('id', $payment->user_id))->pluck('id') as $userId) {
            PosNotification::create([
                'user_id' => $userId, 'type' => 'credit', 'title' => 'Pembayaran kasbon',
                'message' => $receivable->customer->name.' membayar Rp '.number_format($payment->amount, 0, ',', '.').'.',
                'url' => route('pos.receivables.show', $receivable),
            ]);
        }
    }

    public function sale(PosTransaction $transaction): void
    {
        $users = User::query()->where('is_active', true)
            ->where(fn ($query) => $query->where('role', 'admin')->orWhere('id', $transaction->cashier_id))
            ->pluck('id');
        foreach ($users as $userId) {
            PosNotification::query()->create([
                'user_id' => $userId, 'type' => $transaction->payment_method === 'credit' ? 'credit' : 'sale', 'title' => $transaction->payment_method === 'credit' ? 'Kasbon baru' : 'Transaksi baru',
                'message' => $transaction->transaction_number.' selesai · Rp '.number_format($transaction->grand_total, 0, ',', '.'),
                'url' => route('pos.transactions.show', $transaction),
            ]);
        }
    }

    public function stock(StockMovement $movement): void
    {
        foreach (User::query()->where('role', 'admin')->where('is_active', true)->pluck('id') as $userId) {
            PosNotification::query()->create([
                'user_id' => $userId, 'type' => 'stock', 'title' => 'Stok disesuaikan',
                'message' => $movement->product->name.' · '.($movement->delta_qty > 0 ? '+' : '').$movement->delta_qty.' unit',
                'url' => route('pos.manage.stock.movements'),
            ]);
        }
    }

    public function draft(TransactionDraft $draft, string $action): void
    {
        $users = User::query()->where('is_active', true)
            ->where(fn ($query) => $query->where('role', 'admin')->orWhere('id', $draft->cashier_id))
            ->pluck('id');
        foreach ($users as $userId) {
            PosNotification::query()->create([
                'user_id' => $userId, 'type' => 'draft', 'title' => 'Draft '.$action,
                'message' => 'Draft #'.$draft->id.' '.$action.'.',
                'url' => route('pos.drafts.show', $draft),
            ]);
        }
    }
}
