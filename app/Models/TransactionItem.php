<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransactionItem extends Model
{
    protected $fillable = ['product_id', 'category_id', 'product_name', 'product_sku', 'unit_name', 'unit_price', 'unit_cost', 'qty', 'line_total'];

    protected function casts(): array
    {
        return ['unit_cost' => 'integer'];
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(PosTransaction::class, 'transaction_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }
}
