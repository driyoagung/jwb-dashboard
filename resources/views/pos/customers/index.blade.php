<x-layouts.admin title="Pelanggan" group="Penjualan">
    <x-ui.page-header title="Pelanggan" description="Kelola pelanggan untuk transaksi kasbon." />
    <div class="card" data-pos-server-table>
        <div class="flex flex-wrap items-center justify-between gap-3 p-6"><div><h2 class="card-title">Daftar pelanggan</h2><p class="card-desc mt-1">Pilih pelanggan saat membuat kasbon.</p></div><a href="{{ route('pos.customers.create') }}" class="btn btn-primary btn-sm"><x-ui.icon name="plus" />Tambah pelanggan</a></div>
        <x-ui.filter-bar :action="route('pos.customers.index')" :reset-url="route('pos.customers.index')" placeholder="Cari nama atau telepon" embedded live>
            <div class="flex flex-wrap gap-2" role="group" aria-label="Filter status pelanggan" data-pos-filter-group>
                <input type="hidden" name="status" value="{{ request('status', '') }}" data-pos-filter-value>
                @foreach (['' => 'Semua', 'active' => 'Aktif', 'inactive' => 'Nonaktif'] as $value => $label)
                    <button type="button" class="filter-chip" data-pos-filter-option="{{ $value }}" aria-pressed="{{ request('status', '') === $value ? 'true' : 'false' }}">{{ $label }}</button>
                @endforeach
            </div>
        </x-ui.filter-bar>
        <div data-pos-results>
        @if ($customers->isEmpty()) <div class="border-t"><x-ui.table-state :state="request()->hasAny(['q', 'status']) ? 'filtered' : 'empty'" title="Belum ada pelanggan" /></div>
        @else <div class="overflow-x-auto border-t"><table class="table table-striped"><thead><tr>@foreach (['name' => 'Nama', 'is_active' => 'Status', 'receivables_count' => 'Kasbon'] as $column => $label)<th aria-sort="{{ request('sort', 'name') === $column ? (request('direction', 'asc') === 'asc' ? 'ascending' : 'descending') : 'none' }}"><a href="{{ request()->fullUrlWithQuery(['sort' => $column, 'direction' => request('sort', 'name') === $column && request('direction', 'asc') === 'asc' ? 'desc' : 'asc', 'page' => 1]) }}" class="inline-flex items-center gap-1">{{ $label }}<x-ui.icon name="chevron-down" class="h-3 w-3" /></a></th>@if ($column === 'name')<th>Telepon</th>@endif @endforeach<th>Aksi</th></tr></thead><tbody>
            @foreach ($customers as $customer)<tr><td class="font-medium">{{ $customer->name }}</td><td>{{ $customer->phone ?: '-' }}</td><td><x-ui.badge :variant="$customer->is_active ? 'success' : 'neutral'" dot>{{ $customer->is_active ? 'Aktif' : 'Nonaktif' }}</x-ui.badge></td><td>{{ $customer->receivables_count }}</td><td><a href="{{ route('pos.customers.edit', $customer) }}" class="btn btn-info btn-sm"><x-ui.icon name="edit" />Ubah</a></td></tr>@endforeach
        </tbody></table></div>@endif
        <x-ui.pagination :paginator="$customers" />
        </div>
    </div>
</x-layouts.admin>
