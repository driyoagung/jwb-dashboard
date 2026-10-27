<?php

namespace App\Http\Controllers\Pos;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\StockMovement;
use App\Services\StockService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StockController extends Controller
{
    public function index(Request $request): View
    {
        $data = $request->validate(['q' => ['nullable', 'string', 'max:100'], 'sort' => ['nullable', 'in:name,stock_qty,min_stock'], 'direction' => ['nullable', 'in:asc,desc']]);
        $products = Product::query()->with('unit')
            ->when($data['q'] ?? null, fn ($query, $q) => $query->where('name', 'like', '%'.$q.'%')->orWhere('sku', 'like', '%'.$q.'%'))
            ->orderBy($data['sort'] ?? 'name', $data['direction'] ?? 'asc')->orderBy('id')->paginate(15)->withQueryString();

        return view('pos.stock.index', compact('products'));
    }

    public function adjust(Request $request, Product $product, StockService $stock): RedirectResponse
    {
        $data = $request->validate([
            'type' => ['required', 'in:in,out,correction'],
            'quantity' => ['required', 'integer', 'min:0', 'max:2000000000'],
            'reason' => ['required', 'string', 'max:1000'],
        ]);
        $stock->adjust($product, $request->user(), $data['type'], (int) $data['quantity'], $data['reason']);

        return back()->with('status', 'Stok berhasil disesuaikan.');
    }

    public function movements(Request $request): View
    {
        $data = $request->validate(['q' => ['nullable', 'string', 'max:100']]);
        $movements = StockMovement::query()->with(['product', 'user', 'transaction'])
            ->when($data['q'] ?? null, fn ($query, $q) => $query->whereHas('product', fn ($query) => $query->where('name', 'like', '%'.$q.'%')->orWhere('sku', 'like', '%'.$q.'%')))
            ->orderByDesc('id')->paginate(20)->withQueryString();

        return view('pos.stock.movements', compact('movements'));
    }
}
