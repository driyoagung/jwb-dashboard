<?php

namespace App\Http\Controllers\Pos;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pos\UnitRequest;
use App\Models\Unit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UnitController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate(['q' => ['nullable', 'string', 'max:100'], 'sort' => ['nullable', 'in:name,symbol,is_active'], 'direction' => ['nullable', 'in:asc,desc']]);
        $rows = Unit::query()->withCount('products')
            ->when($filters['q'] ?? null, fn ($query, $q) => $query->where('name', 'like', '%'.$q.'%'))
            ->orderBy($filters['sort'] ?? 'name', $filters['direction'] ?? 'asc')->orderBy('id')->paginate(15)->withQueryString();

        return view('pos.master.index', ['rows' => $rows, 'kind' => 'units', 'title' => 'Satuan']);
    }

    public function create(): View
    {
        return view('pos.master.form', ['record' => null, 'kind' => 'units', 'title' => 'Satuan']);
    }

    public function edit(Unit $unit): View
    {
        return view('pos.master.form', ['record' => $unit, 'kind' => 'units', 'title' => 'Satuan']);
    }

    public function store(UnitRequest $request): RedirectResponse
    {
        Unit::create($request->validated());

        return redirect()->route('pos.manage.units.index')->with('status', 'Satuan berhasil ditambahkan.');
    }

    public function update(UnitRequest $request, Unit $unit): RedirectResponse
    {
        $unit->update($request->validated());

        return redirect()->route('pos.manage.units.index')->with('status', 'Satuan berhasil diperbarui.');
    }

    public function destroy(Unit $unit): RedirectResponse
    {
        if ($unit->products()->withTrashed()->exists()) {
            return back()->withErrors(['delete' => 'Satuan sedang dipakai produk. Nonaktifkan saja.']);
        }
        $unit->delete();

        return back()->with('status', 'Satuan dihapus.');
    }
}
