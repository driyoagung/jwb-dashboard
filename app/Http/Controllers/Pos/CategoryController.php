<?php

namespace App\Http\Controllers\Pos;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pos\CategoryRequest;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate(['q' => ['nullable', 'string', 'max:100'], 'sort' => ['nullable', 'in:name,is_active'], 'direction' => ['nullable', 'in:asc,desc']]);
        $rows = Category::query()->withCount('products')
            ->when($filters['q'] ?? null, fn ($query, $q) => $query->where('name', 'like', '%'.$q.'%'))
            ->orderBy($filters['sort'] ?? 'name', $filters['direction'] ?? 'asc')->orderBy('id')->paginate(15)->withQueryString();

        return view('pos.master.index', ['rows' => $rows, 'kind' => 'categories', 'title' => 'Kategori']);
    }

    public function create(): View
    {
        return view('pos.master.form', ['record' => null, 'kind' => 'categories', 'title' => 'Kategori']);
    }

    public function edit(Category $category): View
    {
        return view('pos.master.form', ['record' => $category, 'kind' => 'categories', 'title' => 'Kategori']);
    }

    public function store(CategoryRequest $request): RedirectResponse
    {
        Category::create($request->validated());

        return redirect()->route('pos.manage.categories.index')->with('status', 'Kategori berhasil ditambahkan.');
    }

    public function update(CategoryRequest $request, Category $category): RedirectResponse
    {
        $category->update($request->validated());

        return redirect()->route('pos.manage.categories.index')->with('status', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        if ($category->products()->withTrashed()->exists()) {
            return back()->withErrors(['delete' => 'Kategori sedang dipakai produk. Nonaktifkan saja.']);
        }
        $category->delete();

        return back()->with('status', 'Kategori dihapus.');
    }
}
