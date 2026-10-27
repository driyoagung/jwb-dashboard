<?php

namespace App\Services;

use App\Models\Product;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StockService
{
    public function __construct(private readonly PosNotifier $notifier) {}

    public function adjust(Product $product, User $actor, string $type, int $quantity, string $reason): StockMovement
    {
        return DB::transaction(function () use ($product, $actor, $type, $quantity, $reason): StockMovement {
            $locked = Product::query()->lockForUpdate()->findOrFail($product->id);
            $before = $locked->stock_qty;
            $after = match ($type) {
                'in' => $before + $quantity,
                'out' => $before - $quantity,
                'correction' => $quantity,
                default => throw ValidationException::withMessages(['type' => 'Jenis penyesuaian tidak valid.']),
            };
            if ($after < 0 || $after > 2000000000) {
                throw ValidationException::withMessages(['quantity' => 'Stok akhir tidak valid.']);
            }
            $locked->stock_qty = $after;
            $locked->save();

            $movement = StockMovement::create([
                'product_id' => $locked->id, 'user_id' => $actor->id, 'type' => $type,
                'delta_qty' => $after - $before, 'before_qty' => $before,
                'after_qty' => $after, 'reason' => $reason,
            ]);
            $movement->setRelation('product', $locked);
            $this->notifier->stock($movement);

            return $movement;
        }, 3);
    }
}
