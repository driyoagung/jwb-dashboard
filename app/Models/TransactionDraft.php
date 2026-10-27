<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransactionDraft extends Model
{
    protected $fillable = ['cashier_id', 'status', 'expires_at', 'payload_json'];

    protected function casts(): array
    {
        return ['expires_at' => 'datetime', 'payload_json' => 'array'];
    }

    public function cashier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }
}
