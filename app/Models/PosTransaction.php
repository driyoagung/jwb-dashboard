<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PosTransaction extends Model
{
    protected $table = 'transactions';

    protected $fillable = ['transaction_number', 'cashier_id', 'customer_id', 'draft_id', 'status', 'subtotal', 'discount_amount', 'tax_rate', 'tax_amount', 'grand_total', 'payment_method', 'amount_received', 'change_amount', 'idempotency_key', 'completed_at', 'midtrans_status', 'midtrans_redirect_url', 'midtrans_snap_token'];

    protected function casts(): array
    {
        return ['completed_at' => 'datetime', 'tax_rate' => 'decimal:2'];
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
