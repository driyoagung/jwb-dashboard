<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReferenceRecordIndexRequest;
use App\Http\Requests\StoreReferenceRecordRequest;
use App\Http\Requests\UpdateReferenceRecordRequest;
use App\Models\ReferenceRecord;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ReferenceRecordController extends Controller
{
    public function index(ReferenceRecordIndexRequest $request): View
    {
        $filters = $request->validated();

        $records = $request->user()->referenceRecords()
            ->when($filters['q'] ?? null, fn ($query, $search) => $query->where('title', 'like', '%'.$search.'%'))
            ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('reference.records.index', compact('records'));
    }

    public function create(): View
    {
        Gate::authorize('create', ReferenceRecord::class);

        return view('reference.records.form', ['record' => null]);
    }

    public function store(StoreReferenceRecordRequest $request): RedirectResponse
    {
        $record = $request->user()->referenceRecords()->create($request->validated());

        return redirect()->route('reference.records.show', $record)
            ->with('status', 'Entri berhasil dibuat.');
    }

    public function show(ReferenceRecord $record): View
    {
        Gate::authorize('view', $record);

        return view('reference.records.show', compact('record'));
    }

    public function edit(ReferenceRecord $record): View
    {
        Gate::authorize('update', $record);

        return view('reference.records.form', compact('record'));
    }

    public function update(UpdateReferenceRecordRequest $request, ReferenceRecord $record): RedirectResponse
    {
        $record->update($request->validated());

        return redirect()->route('reference.records.show', $record)
            ->with('status', 'Entri berhasil diperbarui.');
    }

    public function destroy(ReferenceRecord $record): RedirectResponse
    {
        Gate::authorize('delete', $record);

        $record->delete();

        return redirect()->route('reference.records.index')
            ->with('status', 'Entri berhasil dihapus.');
    }
}
