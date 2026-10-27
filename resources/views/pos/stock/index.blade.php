<x-layouts.admin title="Stok" group="Inventori">
    <x-ui.page-header title="Stok" description="Stok kini dan penyesuaian manual." />
    <div class="card" data-pos-server-table>
        <div class="p-6 pb-4"><h2 class="card-title">Daftar stok</h2><p class="card-desc mt-1.5">Cari produk dan sesuaikan jumlah stok.</p></div>
        <x-ui.filter-bar :action="route('pos.manage.stock.index')" :reset-url="route('pos.manage.stock.index')" placeholder="Cari nama atau SKU" embedded live />
        <div data-pos-results>
            @if ($products->isEmpty()) <div class="border-t"><x-ui.table-state :state="request()->filled('q') ? 'filtered' : 'empty'" title="Tidak ada produk" /></div>
            @else <div class="overflow-x-auto border-t"><table class="table table-striped"><thead><tr>
                @foreach (['name' => 'Produk', 'stock_qty' => 'Stok', 'min_stock' => 'Minimum'] as $column => $label)<th aria-sort="{{ request('sort', 'name') === $column ? (request('direction', 'asc') === 'asc' ? 'ascending' : 'descending') : 'none' }}"><a href="{{ request()->fullUrlWithQuery(['sort' => $column, 'direction' => request('sort', 'name') === $column && request('direction', 'asc') === 'asc' ? 'desc' : 'asc', 'page' => 1]) }}" class="inline-flex items-center gap-1">{{ $label }}<x-ui.icon name="chevron-down" class="h-3 w-3" /></a></th>@endforeach
                <th>Status</th><th scope="col">Aksi</th></tr></thead><tbody>
                @foreach ($products as $product)<tr><td><div class="flex items-center gap-3"><span class="avatar avatar-soft avatar-square"><x-ui.icon name="package" /></span><div class="leading-tight"><p class="font-medium">{{ $product->name }}</p><p class="text-xs text-muted-foreground">{{ $product->sku }}</p></div></div></td><td class="tabular-nums">{{ $product->stock_qty }} {{ $product->unit->symbol }}</td><td class="tabular-nums">{{ $product->min_stock }}</td><td><x-ui.badge :variant="$product->stock_qty === 0 ? 'danger' : ($product->min_stock > 0 && $product->stock_qty <= $product->min_stock ? 'warning' : 'success')" dot>{{ $product->stock_qty === 0 ? 'Habis' : ($product->min_stock > 0 && $product->stock_qty <= $product->min_stock ? 'Menipis' : 'Aman') }}</x-ui.badge></td><td><x-ui.button variant="warning" size="sm" data-modal-open="#adjust-{{ $product->id }}"><x-ui.icon name="edit" />Sesuaikan</x-ui.button></td></tr>@endforeach
            </tbody></table></div>@endif
            <x-ui.pagination :paginator="$products" />
            @foreach ($products as $product)
                <x-ui.modal :id="'adjust-'.$product->id" :title="'Sesuaikan '.$product->name" description="Setiap perubahan dicatat dalam riwayat stok.">
                    <form method="POST" action="{{ route('pos.manage.stock.adjust', $product) }}" class="space-y-4">@csrf
                        <x-ui.field label="Jenis" name="type" :required="true"><x-ui.select name="type" placeholder="Pilih jenis penyesuaian" :options="['in' => 'Stok masuk', 'out' => 'Stok keluar', 'correction' => 'Koreksi stok akhir']" required /></x-ui.field>
                        <x-ui.field label="Jumlah" name="quantity" :required="true"><x-ui.input name="quantity" type="number" min="0" placeholder="Jumlah barang" required data-grouped-number /></x-ui.field>
                        <x-ui.field label="Alasan" name="reason" :required="true"><x-ui.textarea name="reason" placeholder="Jelaskan alasan penyesuaian stok" required /></x-ui.field>
                        <div class="flex justify-end gap-2"><x-ui.button variant="outline" data-modal-close><x-ui.icon name="x" />Batal</x-ui.button><x-ui.button type="submit"><x-ui.icon name="check" />Simpan</x-ui.button></div>
                    </form>
                </x-ui.modal>
            @endforeach
        </div>
    </div>
</x-layouts.admin>
