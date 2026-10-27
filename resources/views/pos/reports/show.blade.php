<x-layouts.admin :title="$title" group="Laporan">
    <x-ui.page-header :title="$title" description="Data laporan berdasarkan transaksi dan stok tersimpan." />
    <div data-pos-server-table>
    <div class="grid gap-4 md:grid-cols-2" data-pos-results>@foreach ($summary as $label => $value)<x-ui.stat-card :title="$label" :value="$value" icon="chart" delta="Periode" note="Laporan" />@endforeach</div>
    @if ($report === 'sales')<x-ui.card title="Tren penjualan" data-pos-results><x-ui.chart type="bar" aria="Tren omzet harian" :labels="$labels" :series="[['name' => 'Omzet', 'data' => $values, 'color' => 1]]" prefix="Rp " /></x-ui.card>@endif
    @if ($report === 'sales')<div class="card" data-pos-results><div class="p-6 pb-4"><h2 class="card-title">Metode pembayaran</h2></div>
        @if ($paymentBreakdown->isEmpty()) <div class="border-t"><x-ui.table-state state="empty" title="Belum ada pembayaran" /></div>
        @else <div class="overflow-x-auto border-t"><table class="table table-striped"><thead><tr><th>Metode</th><th>Transaksi</th><th>Total</th></tr></thead><tbody>@foreach ($paymentBreakdown as $item)<tr><td><x-ui.badge variant="info" dot>{{ strtoupper($item->payment_method) }}</x-ui.badge></td><td class="tabular-nums">{{ $item->transactions }}</td><td class="tabular-nums">Rp {{ number_format($item->total, 0, ',', '.') }}</td></tr>@endforeach</tbody></table></div>@endif
    </div>@endif
    <div class="card">
        <div class="flex flex-col gap-3 p-6 pb-4 sm:flex-row sm:items-center sm:justify-between" data-pos-results><div><h2 class="card-title">Data {{ $title }}</h2><p class="card-desc mt-1.5">Rincian sesuai filter laporan.</p></div><a class="btn btn-outline btn-sm" href="{{ route('pos.manage.reports.export', ['report' => $report] + $filters) }}"><x-ui.icon name="download" />Ekspor CSV</a></div>
        <div class="border-t px-6 py-3"><form method="GET" action="{{ route('pos.manage.reports.show', $report) }}" class="space-y-4" data-pos-live-filter>
            @if ($report !== 'stock')<x-ui.date-range id="report-range" from-name="from" to-name="to" :from="$filters['from']" :to="$filters['to']" />@endif
            @if ($report === 'products')
                @if($categories->count() < 5)
                    <div class="space-y-1.5"><span class="label">Kategori</span><div class="flex flex-wrap gap-2" role="group" aria-label="Filter kategori" data-pos-filter-group>
                        <input type="hidden" name="category_id" value="{{ $filters['category_id'] ?? '' }}" data-pos-filter-value>
                        @foreach((['' => 'Semua kategori'] + $categories->all()) as $value => $label)
                            <button type="button" class="filter-chip" data-pos-filter-option="{{ $value }}" aria-pressed="{{ (string) ($filters['category_id'] ?? '') === (string) $value ? 'true' : 'false' }}">{{ $label }}</button>
                        @endforeach
                    </div></div>
                @else<x-ui.field label="Kategori" name="category_id"><x-ui.select name="category_id" placeholder="Semua kategori" :value="$filters['category_id'] ?? null" :options="$categories" :searchable="true" /></x-ui.field>@endif
            @endif
            @if ($report === 'stock')<div class="space-y-1.5"><span class="label">Status stok</span><div class="flex flex-wrap gap-2" role="group" aria-label="Filter status stok" data-pos-filter-group>
                <input type="hidden" name="stock_status" value="{{ $filters['stock_status'] ?? 'all' }}" data-pos-filter-value>
                @foreach (['all' => 'Semua', 'safe' => 'Aman', 'low' => 'Menipis', 'out' => 'Habis'] as $value => $label)
                    <button type="button" class="filter-chip" data-pos-filter-option="{{ $value }}" aria-pressed="{{ ($filters['stock_status'] ?? 'all') === $value ? 'true' : 'false' }}">{{ $label }}</button>
                @endforeach
            </div></div>@endif
            @if ($report === 'drafts')<x-ui.field label="Status draft" name="draft_status"><x-ui.select name="draft_status" placeholder="Semua status draft" :value="$filters['draft_status'] ?? 'all'" :options="['all' => 'Semua', 'active' => 'Aktif', 'pending_payment' => 'Menunggu pembayaran', 'expired' => 'Kedaluwarsa', 'cancelled' => 'Dibatalkan', 'completed' => 'Selesai']" /></x-ui.field>@endif
            @if ($report === 'drafts')
                @if($cashiers->count() < 5)
                    <div class="space-y-1.5"><span class="label">Kasir</span><div class="flex flex-wrap gap-2" role="group" aria-label="Filter kasir" data-pos-filter-group>
                        <input type="hidden" name="cashier_id" value="{{ $filters['cashier_id'] ?? '' }}" data-pos-filter-value>
                        @foreach((['' => 'Semua kasir'] + $cashiers->all()) as $value => $label)
                            <button type="button" class="filter-chip" data-pos-filter-option="{{ $value }}" aria-pressed="{{ (string) ($filters['cashier_id'] ?? '') === (string) $value ? 'true' : 'false' }}">{{ $label }}</button>
                        @endforeach
                    </div></div>
                @else<x-ui.field label="Kasir" name="cashier_id"><x-ui.select name="cashier_id" placeholder="Semua kasir" :value="$filters['cashier_id'] ?? null" :options="$cashiers" :searchable="true" /></x-ui.field>@endif
            @endif
            <div class="flex gap-2"><a class="btn btn-outline" href="{{ route('pos.manage.reports.show', $report) }}"><x-ui.icon name="refresh" />Atur ulang</a></div>
        </form></div>
        <div data-pos-results>
        @if ($rows->isEmpty()) <div class="border-t"><x-ui.table-state state="empty" title="Tidak ada data pada periode ini" /></div>
        @else <div class="overflow-x-auto border-t"><table class="table table-striped min-w-[760px]"><thead><tr>
            @if ($report === 'sales')<th>Nomor</th><th>Waktu</th><th>Kasir</th><th>Metode</th><th>Total</th>
            @elseif ($report === 'products')<th>Peringkat</th><th>SKU</th><th>Produk</th><th>Qty</th><th>Omzet bruto</th>
            @elseif ($report === 'stock')<th>SKU</th><th>Produk</th><th>Stok</th><th>Minimum</th><th>Nilai estimasi</th>
            @else<th>ID</th><th>Kasir</th><th>Status</th><th>Nilai estimasi</th><th>Dibuat</th><th>Kedaluwarsa</th>@endif
        </tr></thead><tbody>@foreach ($rows as $row)<tr>
            @if ($report === 'sales')<td><a class="font-medium" href="{{ route('pos.transactions.show', $row) }}">{{ $row->transaction_number }}</a></td><td class="text-muted-foreground">{{ $row->completed_at->format('d M Y H:i') }}</td><td>{{ $row->cashier->name }}</td><td><x-ui.badge variant="info" dot>{{ strtoupper($row->payment_method) }}</x-ui.badge></td><td class="tabular-nums">Rp {{ number_format($row->grand_total, 0, ',', '.') }}</td>
            @elseif ($report === 'products')<td>{{ $rows->firstItem() + $loop->index }}</td><td>{{ $row->product_sku }}</td><td>{{ $row->product_name }}</td><td>{{ $row->qty }}</td><td>Rp {{ number_format($row->revenue, 0, ',', '.') }}</td>
            @elseif ($report === 'stock')<td>{{ $row->sku }}</td><td>{{ $row->name }}</td><td>{{ $row->stock_qty }}</td><td>{{ $row->min_stock }}</td><td>{{ $row->cost_price === null ? '—' : 'Rp '.number_format($row->stock_qty * $row->cost_price, 0, ',', '.') }}</td>
            @else<td>{{ $row->id }}</td><td>{{ $row->cashier->name }}</td><td><x-ui.badge :variant="$row->status === 'active' && $row->expires_at->isFuture() ? 'success' : 'neutral'" dot>{{ $row->status === 'active' && $row->expires_at->isPast() ? 'expired' : $row->status }}</x-ui.badge></td><td>Rp {{ number_format(max(0, collect($row->payload_json['items'] ?? [])->sum(fn ($item) => ($item['unit_price'] ?? 0) * ($item['qty'] ?? 0)) - ($row->payload_json['discount_amount'] ?? 0)), 0, ',', '.') }}</td><td>{{ $row->created_at->format('d M Y') }}</td><td>{{ $row->expires_at->format('d M Y') }}</td>@endif
        </tr>@endforeach</tbody></table></div>@endif
        <x-ui.pagination :paginator="$rows" />
        </div>
    </div>
    </div>
</x-layouts.admin>
