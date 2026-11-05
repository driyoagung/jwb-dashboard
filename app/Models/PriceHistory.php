<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PriceHistory extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = ['product_id', 'user_id', 'price_type', 'old_price', 'new_price'];

    protected function casts(): array
    {
        return ['old_price' => 'integer', 'new_price' => 'integer', 'created_at' => 'immutable_datetime'];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
