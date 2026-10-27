<x-layouts.admin title="Produk" group="Master data">
    <x-ui.page-header title="Produk" description="Kelola katalog dan harga jual." />
    <div class="card" data-pos-server-table>
        <div class="flex flex-col gap-3 p-6 pb-4 sm:flex-row sm:items-center sm:justify-between">
            <div><h2 class="card-title">Daftar produk</h2><p class="card-desc mt-1.5">Cari, filter, urutkan, dan kelola produk.</p></div>
            <a href="{{ route('pos.manage.products.create') }}" class="btn btn-primary btn-sm"><x-ui.icon name="plus" />Tambah produk</a>
        </div>
        <x-ui.filter-bar :action="route('pos.manage.products.index')" :reset-url="route('pos.manage.products.index')" placeholder="Nama, SKU, atau barcode" embedded live>
            @if($categories->count() < 5)
                <div class="flex flex-wrap gap-2" role="group" aria-label="Filter kategori" data-pos-filter-group>
                    <input type="hidden" name="category_id" value="{{ request('category_id', '') }}" data-pos-filter-value>
                    @foreach((['' => 'Semua kategori'] + $categories->all()) as $value => $label)
                        <button type="button" class="filter-chip" data-pos-filter-option="{{ $value }}" aria-pressed="{{ (string) request('category_id', '') === (string) $value ? 'true' : 'false' }}">{{ $label }}</button>
                    @endforeach
                </div>
            @else
                <div class="min-w-40"><label class="sr-only" for="category-filter">Kategori</label><x-ui.select id="category-filter" name="category_id" placeholder="Semua kategori" :value="request('category_id')" :options="$categories" :searchable="true" /></div>
            @endif
            <div class="flex flex-wrap gap-2" role="group" aria-label="Filter status produk" data-pos-filter-group>
                <input type="hidden" name="status" value="{{ request('status', '') }}" data-pos-filter-value>
                @foreach (['' => 'Semua', 'active' => 'Aktif', 'inactive' => 'Nonaktif', 'low' => 'Menipis', 'out' => 'Habis'] as $value => $label)
                    <button type="button" class="filter-chip" data-pos-filter-option="{{ $value }}" aria-pressed="{{ request('status', '') === $value ? 'true' : 'false' }}">{{ $label }}</button>
                @endforeach
            </div>
        </x-ui.filter-bar>
        <div data-pos-results>
            @if ($products->isEmpty()) <div class="border-t"><x-ui.table-state :state="request()->hasAny(['q', 'category_id', 'status']) ? 'filtered' : 'empty'" title="Belum ada produk" /></div>
            @else
                <div class="overflow-x-auto border-t"><table class="table table-striped min-w-[760px]"><thead><tr>
                    @foreach (['name' => 'Produk', 'sell_price' => 'Harga', 'stock_qty' => 'Stok'] as $column => $label)
                        @if ($column === 'sell_price') <th>Kategori</th> @endif
                        <th aria-sort="{{ request('sort') === $column ? (request('direction', 'asc') === 'asc' ? 'ascending' : 'descending') : 'none' }}"><a href="{{ request()->fullUrlWithQuery(['sort' => $column, 'direction' => request('sort') === $column && request('direction', 'asc') === 'asc' ? 'desc' : 'asc', 'page' => 1]) }}" class="inline-flex items-center gap-1">{{ $label }}<x-ui.icon name="chevron-down" class="h-3 w-3" /></a></th>
                    @endforeach
                    <th>Status</th><th scope="col">Aksi</th></tr></thead><tbody>
                    @foreach ($products as $product)<tr>
                        <td><div class="flex items-center gap-3">@if ($product->image_path)<img src="{{ asset('storage/'.$product->image_path) }}" alt="" class="h-9 w-9 rounded-md object-cover" loading="lazy">@else<span class="avatar avatar-soft avatar-square"><x-ui.icon name="package" /></span>@endif<div class="leading-tight"><p class="font-medium">{{ $product->name }}</p><p class="text-xs text-muted-foreground">{{ $product->sku }}</p>@if ($product->barcode)<p class="text-xs text-muted-foreground">Barcode: {{ $product->barcode }}</p>@endif</div></div></td>
                        <td>{{ $product->category->name }}</td><td class="tabular-nums">Rp {{ number_format($product->sell_price, 0, ',', '.') }}</td><td class="tabular-nums">{{ $product->stock_qty }} {{ $product->unit->symbol }}</td>
                        <td><x-ui.badge :variant="$product->is_active ? 'success' : 'neutral'" dot>{{ $product->is_active ? 'Aktif' : 'Nonaktif' }}</x-ui.badge></td>
                        <td><div class="flex flex-wrap gap-2 whitespace-nowrap"><a class="btn btn-info btn-sm" href="{{ route('pos.manage.products.edit', $product) }}"><x-ui.icon name="edit" />Ubah</a><x-ui.button variant="destructive" size="sm" data-modal-open="#delete-{{ $product->id }}"><x-ui.icon name="trash" />Hapus</x-ui.button></div></td>
                    </tr>@endforeach
                </tbody></table></div>
            @endif
            <x-ui.pagination :paginator="$products" />
            @foreach ($products as $product)<x-ui.confirm-action :id="'delete-'.$product->id" :title="'Hapus '.$product->name.'?'" description="Produk akan hilang dari katalog dan kasir. Riwayat transaksi serta stok tetap tersimpan." :action="route('pos.manage.products.destroy', $product)" />@endforeach
        </div>
    </div>
</x-layouts.admin>
