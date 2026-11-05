<?php

namespace App\Models;

use App\Support\PosFeatures;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PosTransaction extends Model
{
    protected $table = 'transactions';

    protected $fillable = ['transaction_number', 'cashier_id', 'customer_id', 'draft_id', 'status', 'subtotal', 'discount_amount', 'tax_rate', 'tax_amount', 'grand_total', 'cost_total', 'payment_method', 'amount_received', 'change_amount', 'idempotency_key', 'completed_at', 'midtrans_status', 'midtrans_redirect_url', 'midtrans_snap_token'];

    protected function casts(): array
    {
        return ['created_at' => 'immutable_datetime', 'updated_at' => 'immutable_datetime', 'completed_at' => 'immutable_datetime', 'tax_rate' => 'decimal:2', 'cost_total' => 'integer'];
    }

    public static function paymentOptions(): array
    {
        return ['cash' => 'Tunai', 'transfer' => 'Transfer manual', 'qris' => 'QRIS merchant', 'midtrans' => 'Midtrans']
            + (PosFeatures::creditEnabled() ? ['credit' => 'Kasbon'] : []);
    }

    public static function paymentLabel(string $method): string
    {
        return match ($method) {
            'cash' => 'Tunai', 'transfer' => 'Transfer', 'qris' => 'QRIS', 'midtrans' => 'Midtrans',
            'credit' => PosFeatures::creditEnabled() ? 'Kasbon' : 'Kasbon (arsip)',
            'other' => 'Lainnya (data lama)', default => 'Metode tidak dikenal',
        };
    }

    public function getPaymentMethodLabelAttribute(): string
    {
        return self::paymentLabel($this->payment_method);
    }

    public function getGrossProfitAttribute(): ?int
    {
        return $this->cost_total === null ? null : $this->subtotal - $this->discount_amount - $this->cost_total;
    }

    public function cashier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(TransactionItem::class, 'transaction_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function receivable(): HasOne
    {
        return $this->hasOne(Receivable::class, 'transaction_id');
    }
}
