<?php

namespace App\Http\Controllers\Pos;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pos\ProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Models\Unit;
use App\Services\StockService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use RuntimeException;
use Throwable;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'], 'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'status' => ['nullable', 'in:active,inactive,low,out'], 'sort' => ['nullable', 'in:name,sku,sell_price,stock_qty,created_at'],
            'direction' => ['nullable', 'in:asc,desc'],
        ]);
        $products = Product::query()->with(['category', 'unit'])
            ->when($filters['q'] ?? null, fn ($query, $q) => $query->where(fn ($query) => $query->where('name', 'like', '%'.$q.'%')->orWhere('sku', 'like', '%'.$q.'%')->orWhere('barcode', 'like', '%'.$q.'%')))
            ->when($filters['category_id'] ?? null, fn ($query, $id) => $query->where('category_id', $id))
            ->when($filters['status'] ?? null, function ($query, $status): void {
                match ($status) {
                    'active' => $query->where('is_active', true),
                    'inactive' => $query->where('is_active', false),
                    'out' => $query->where('stock_qty', 0),
                    'low' => $query->where('min_stock', '>', 0)->whereColumn('stock_qty', '<=', 'min_stock'),
                };
            })
            ->orderBy($filters['sort'] ?? 'created_at', $filters['direction'] ?? 'desc')->orderBy('id')
            ->paginate(15)->withQueryString();

        return view('pos.products.index', ['products' => $products, 'categories' => Category::orderBy('name')->pluck('name', 'id')]);
    }

    public function create(): View
    {
        return $this->form(null);
    }

    public function edit(Product $product): View
    {
        return $this->form($product);
    }

    private function form(?Product $product): View
    {
        return view('pos.products.form', [
            'product' => $product,
            'categories' => Category::where('is_active', true)->orderBy('name')->pluck('name', 'id'),
            'units' => Unit::where('is_active', true)->orderBy('name')->pluck('name', 'id'),
        ]);
    }

    public function store(ProductRequest $request, StockService $stock): RedirectResponse
    {
        $data = $request->validated();
        $initial = (int) $data['initial_stock'];
        unset($data['initial_stock'], $data['image'], $data['remove_image']);
        $data['sku'] = 'PRD-'.Str::upper(Str::random(12));
        $imagePath = $request->file('image')?->store('products', 'public');
        if ($imagePath === false) {
            throw new RuntimeException('Gambar produk gagal disimpan.');
        }
        $data['image_path'] = $imagePath;
        try {
            DB::transaction(function () use ($data, $initial, $stock, $request): void {
                $product = Product::create($data);
                if ($initial > 0) {
                    $stock->adjust($product, $request->user(), 'in', $initial, 'Stok awal produk');
                }
            });
        } catch (Throwable $exception) {
            if ($imagePath) {
                Storage::disk('public')->delete($imagePath);
            }
            throw $exception;
        }

        return redirect()->route('pos.manage.products.index')->with('status', 'Produk berhasil ditambahkan.');
    }

    public function update(ProductRequest $request, Product $product): RedirectResponse
    {
        $data = $request->validated();
        unset($data['image'], $data['remove_image']);
        $imagePath = $request->file('image')?->store('products', 'public');
        if ($imagePath === false) {
            throw new RuntimeException('Gambar produk gagal disimpan.');
        }
        $oldImagePath = $product->image_path;
        if ($imagePath) {
            $data['image_path'] = $imagePath;
        } elseif ($request->boolean('remove_image')) {
            $data['image_path'] = null;
        }
        try {
            $product->update($data);
        } catch (Throwable $exception) {
            if ($imagePath) {
                Storage::disk('public')->delete($imagePath);
            }
            throw $exception;
        }
        if ($oldImagePath && $oldImagePath !== $product->image_path) {
            Storage::disk('public')->delete($oldImagePath);
        }

        return redirect()->route('pos.manage.products.index')->with('status', 'Produk berhasil diperbarui.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        if ($product->transactionItems()->exists() || $product->movements()->exists()) {
            DB::transaction(function () use ($product): void {
                $product->update(['is_active' => false, 'barcode' => null]);
                $product->delete();
            });

            return back()->with('status', 'Produk dihapus dari katalog. Riwayat transaksi dan stok tetap tersimpan.');
        }
        $product->forceDelete();
        if ($product->image_path) {
            Storage::disk('public')->delete($product->image_path);
        }

        return back()->with('status', 'Produk dihapus.');
    }
}
