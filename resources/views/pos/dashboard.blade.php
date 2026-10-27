<x-layouts.admin title="Dashboard" group="Ringkasan">
    <x-ui.page-header title="Dashboard" description="Ringkasan operasional toko berdasarkan transaksi nyata." />
    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        <x-ui.stat-card title="Omzet hari ini" :value="'Rp '.number_format($revenue, 0, ',', '.')" icon="dollar" delta="Hari ini" note="Transaksi selesai" />
        <x-ui.stat-card title="Transaksi hari ini" :value="$count" icon="cart" delta="Hari ini" note="Penjualan selesai" />
        <x-ui.stat-card title="Draft aktif" :value="$drafts" icon="file-text" delta="Aktif" note="Belum checkout" />
        <x-ui.stat-card title="Stok menipis" :value="$lowCount" icon="package" delta="Produk" note="Di bawah minimum" />
    </div>
    <x-ui.card title="Penjualan 7 hari"><x-ui.chart type="bar" aria="Omzet tujuh hari terakhir" :labels="$labels" :series="[['name' => 'Omzet', 'data' => $values, 'color' => 1]]" prefix="Rp " /></x-ui.card>
    <div class="grid gap-6 lg:grid-cols-2">
        <div class="card"><div class="p-6 pb-4"><h2 class="card-title">Produk terlaris bulan ini</h2></div>
            @if ($topProducts->isEmpty()) <div class="border-t"><x-ui.table-state state="empty" title="Belum ada penjualan" /></div>
            @else <div class="overflow-x-auto border-t"><table class="table table-striped"><thead><tr><th>Produk</th><th>Terjual</th><th>Omzet bruto</th></tr></thead><tbody>@foreach ($topProducts as $item)<tr><td class="font-medium">{{ $item->product_name }}</td><td class="tabular-nums">{{ $item->qty }}</td><td class="tabular-nums">Rp {{ number_format($item->revenue, 0, ',', '.') }}</td></tr>@endforeach</tbody></table></div>@endif
        </div>
        @can('pos.admin')<div class="card"><div class="p-6 pb-4"><h2 class="card-title">Stok menipis</h2></div>
            @if ($lowProducts->isEmpty()) <div class="border-t"><x-ui.table-state state="empty" title="Semua stok aman" /></div>
            @else <div class="overflow-x-auto border-t"><table class="table table-striped"><thead><tr><th>Produk</th><th>Sisa</th><th>Minimum</th></tr></thead><tbody>@foreach ($lowProducts as $product)<tr><td><a class="font-medium" href="{{ route('pos.manage.products.edit', $product) }}">{{ $product->name }}</a></td><td class="tabular-nums">{{ $product->stock_qty }}</td><td class="tabular-nums">{{ $product->min_stock }}</td></tr>@endforeach</tbody></table></div>@endif
        </div>@endcan
    </div>
</x-layouts.admin>
