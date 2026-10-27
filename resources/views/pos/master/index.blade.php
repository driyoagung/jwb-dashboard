<x-layouts.admin :title="$title" group="Master data">
    <x-ui.page-header :title="$title" :description="'Kelola '.strtolower($title).' toko.'" />
    <div class="card" data-pos-server-table>
        <div class="flex flex-col gap-3 p-6 pb-4 sm:flex-row sm:items-center sm:justify-between">
            <div><h2 class="card-title">Daftar {{ strtolower($title) }}</h2><p class="card-desc mt-1.5">Cari dan kelola {{ strtolower($title) }} produk.</p></div>
            <a href="{{ route('pos.manage.'.$kind.'.create') }}" class="btn btn-primary btn-sm"><x-ui.icon name="plus" />Tambah {{ strtolower($title) }}</a>
        </div>
        <x-ui.filter-bar :action="route('pos.manage.'.$kind.'.index')" :reset-url="route('pos.manage.'.$kind.'.index')" placeholder="Cari nama" embedded live />
        <div data-pos-results>
            @if ($rows->isEmpty()) <div class="border-t"><x-ui.table-state :state="request()->filled('q') ? 'filtered' : 'empty'" title="Belum ada data" /></div>
            @else
                <div class="overflow-x-auto border-t"><table class="table table-striped"><thead><tr>
                    @foreach (['name' => 'Nama'] + ($kind === 'units' ? ['symbol' => 'Singkatan'] : []) + ['is_active' => 'Status'] as $column => $label)
                        <th aria-sort="{{ request('sort', 'name') === $column ? (request('direction', 'asc') === 'asc' ? 'ascending' : 'descending') : 'none' }}"><a href="{{ request()->fullUrlWithQuery(['sort' => $column, 'direction' => request('sort', 'name') === $column && request('direction', 'asc') === 'asc' ? 'desc' : 'asc', 'page' => 1]) }}" class="inline-flex items-center gap-1">{{ $label }}<x-ui.icon name="chevron-down" class="h-3 w-3" /></a></th>
                    @endforeach
                    <th>Produk</th><th scope="col">Aksi</th></tr></thead><tbody>
                    @foreach ($rows as $row)<tr>
                        <td><div class="flex items-center gap-3"><span class="avatar avatar-soft avatar-square"><x-ui.icon name="{{ $kind === 'units' ? 'layers' : 'grid' }}" /></span><span class="font-medium">{{ $row->name }}</span></div></td>
                        @if ($kind === 'units') <td>{{ $row->symbol }}</td> @endif
                        <td><x-ui.badge :variant="$row->is_active ? 'success' : 'neutral'" dot>{{ $row->is_active ? 'Aktif' : 'Nonaktif' }}</x-ui.badge></td>
                        <td class="tabular-nums">{{ $row->products_count }}</td>
                        <td><div class="flex flex-wrap gap-2 whitespace-nowrap"><a href="{{ route('pos.manage.'.$kind.'.edit', $row) }}" class="btn btn-info btn-sm"><x-ui.icon name="edit" />Ubah</a>@if ($row->products_count === 0)<x-ui.button variant="destructive" size="sm" data-modal-open="#delete-{{ $row->id }}"><x-ui.icon name="trash" />Hapus</x-ui.button>@endif</div></td>
                    </tr>@endforeach
                </tbody></table></div>
            @endif
            <x-ui.pagination :paginator="$rows" />
            @foreach ($rows as $row) @if ($row->products_count === 0)<x-ui.confirm-action :id="'delete-'.$row->id" :title="'Hapus '.$row->name.'?'" description="Data ini akan dihapus." :action="route('pos.manage.'.$kind.'.destroy', $row)" />@endif @endforeach
        </div>
    </div>
</x-layouts.admin>
