<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreSetting extends Model
{
    protected $attributes = [
        'store_name' => 'Griyo POS',
        'tax_rate' => 0,
        'draft_expiry_hours' => 24,
        'max_active_drafts' => 20,
        'timezone' => 'Asia/Jakarta',
    ];

    protected $fillable = ['store_name', 'phone', 'address', 'receipt_footer', 'tax_rate', 'draft_expiry_hours', 'max_active_drafts', 'timezone'];

    protected function casts(): array
    {
        return ['tax_rate' => 'decimal:2', 'draft_expiry_hours' => 'integer', 'max_active_drafts' => 'integer'];
    }

    public static function current(): self
    {
        return self::query()->firstOrCreate(['id' => 1], ['store_name' => 'Griyo POS']);
    }
}
