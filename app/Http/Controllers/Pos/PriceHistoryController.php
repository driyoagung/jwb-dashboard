<?php

namespace App\Http\Controllers\Pos;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pos\IndexPriceHistoryRequest;
use App\Models\PriceHistory;
use App\Models\StoreSetting;
use Illuminate\View\View;

class PriceHistoryController extends Controller
{
    public function __invoke(IndexPriceHistoryRequest $request): View
    {
        $filters = $request->validated();
        $histories = PriceHistory::with(['product', 'user'])
            ->when($filters['product_id'] ?? null, fn ($query, $id) => $query->where('product_id', $id))
            ->when($filters['price_type'] ?? null, fn ($query, $type) => $query->where('price_type', $type))
            ->when($filters['q'] ?? null, fn ($query, $q) => $query->whereHas('product', fn ($product) => $product->where(fn ($product) => $product->where('name', 'like', '%'.$q.'%')->orWhere('sku', 'like', '%'.$q.'%'))))
            ->orderByDesc('id')->paginate(20)->withQueryString();

        return view('pos.products.price-histories', ['histories' => $histories, 'zone' => StoreSetting::current()->timezone]);
    }
}
