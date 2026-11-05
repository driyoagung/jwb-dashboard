<?php

namespace App\Services;

use App\Models\Product;
use App\Models\StoreSetting;
use Illuminate\Support\Collection;

class CartQuoteService
{
    /** @param array<string, mixed> $data */
    public function quote(array $data): array
    {
        $quantities = collect($data['items'])->mapWithKeys(fn ($item) => [(int) $item['product_id'] => (int) $item['qty']])->sortKeys()->all();
        $products = Product::query()->whereIn('id', array_keys($quantities))->get()->keyBy('id');
        $issues = [];
        $items = [];
        foreach ($quantities as $id => $qty) {
            $product = $products->get($id);
            $available = $product && $product->is_active && $product->stock_qty > 0;
            if (! $available) {
                $issues[] = ($product?->name ?? 'Produk #'.$id).' tidak tersedia. Hapus produk ini dari keranjang.';
            } elseif ($qty > $product->stock_qty) {
                $issues[] = "Stok {$product->name} tersisa {$product->stock_qty}; jumlah di keranjang {$qty}.";
            }
            $items[] = [
                'id' => $id, 'name' => $product?->name, 'price' => $product?->sell_price,
                'stock' => $available ? $product->stock_qty : 0, 'available' => (bool) $available,
                'image' => $product?->image_url,
            ];
        }
        $discount = (int) ($data['discount_amount'] ?? 0);
        $totals = $this->totals($products, $quantities, $discount, StoreSetting::current());
        if ($discount > $totals['subtotal']) {
            $issues[] = 'Diskon melebihi subtotal terbaru. Kurangi nominal diskon.';
        }

        return array_merge($totals, ['items' => $items, 'issues' => $issues, 'can_checkout' => $issues === []]);
    }

    /** @param Collection<int, Product> $products
     * @param  array<int, int>  $quantities
     */
    public function totals(Collection $products, array $quantities, int $discount, StoreSetting $settings): array
    {
        ksort($quantities);
        $subtotal = 0;
        $prices = [];
        foreach ($quantities as $id => $qty) {
            $price = $products->get($id)?->sell_price ?? 0;
            $subtotal += $price * $qty;
            $prices[] = [$id, $qty, $price];
        }
        $basis = (int) round((float) $settings->tax_rate * 100);
        $tax = intdiv(max(0, $subtotal - $discount) * $basis + 5000, 10000);

        return [
            'subtotal' => $subtotal, 'tax_rate' => $settings->tax_rate, 'tax_amount' => $tax,
            'grand_total' => max(0, $subtotal - $discount) + $tax,
            'quote_hash' => hash('sha256', json_encode([$prices, $discount, $basis], JSON_THROW_ON_ERROR)),
        ];
    }
}
