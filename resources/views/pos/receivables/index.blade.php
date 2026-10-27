<x-layouts.admin title="Kasbon" group="Penjualan">
    <x-ui.page-header title="Kasbon" description="Pantau tagihan pelanggan dan catat cicilan." />
    <x-ui.card title="Total belum tertagih" class="mb-6"><strong class="text-2xl tabular-nums">Rp {{ number_format($outstanding, 0, ',', '.') }}</strong></x-ui.card>
    <div class="card" data-pos-server-table>
        <div class="p-6"><h2 class="card-title">Daftar tagihan</h2><p class="card-desc mt-1">Kasbon dicatat saat checkout kasir.</p></div>
        <x-ui.filter-bar :action="route('pos.receivables.index')" :reset-url="route('pos.receivables.index')" placeholder="Cari pelanggan atau nomor transaksi" embedded live>
            <div class="flex flex-wrap gap-2" role="group" aria-label="Filter status kasbon" data-pos-filter-group>
                <input type="hidden" name="status" value="{{ request('status', '') }}" data-pos-filter-value>
                @foreach (['' => 'Semua', 'open' => 'Belum dibayar', 'partial' => 'Sebagian', 'overdue' => 'Lewat jatuh tempo', 'paid' => 'Lunas'] as $value => $label)
                    <button type="button" class="filter-chip" data-pos-filter-option="{{ $value }}" aria-pressed="{{ request('status', '') === $value ? 'true' : 'false' }}">{{ $label }}</button>
                @endforeach
            </div>
        </x-ui.filter-bar>
        <div data-pos-results>
        @if ($receivables->isEmpty()) <div class="border-t"><x-ui.table-state :state="request()->hasAny(['q', 'status']) ? 'filtered' : 'empty'" title="Belum ada kasbon" /></div>
        @else <div class="overflow-x-auto border-t"><table class="table table-striped"><thead><tr><th>Transaksi</th><th>Pelanggan</th>@foreach (['due_date' => 'Jatuh tempo', 'total_amount' => 'Total', 'balance' => 'Sisa', 'status' => 'Status'] as $column => $label)<th aria-sort="{{ request('sort', 'created_at') === $column ? (request('direction', 'desc') === 'asc' ? 'ascending' : 'descending') : 'none' }}"><a href="{{ request()->fullUrlWithQuery(['sort' => $column, 'direction' => request('sort', 'created_at') === $column && request('direction', 'desc') === 'asc' ? 'desc' : 'asc', 'page' => 1]) }}" class="inline-flex items-center gap-1">{{ $label }}<x-ui.icon name="chevron-down" class="h-3 w-3" /></a></th>@endforeach<th>Aksi</th></tr></thead><tbody>
            @foreach ($receivables as $receivable)<tr><td class="font-medium">{{ $receivable->transaction->transaction_number }}</td><td>{{ $receivable->customer->name }}</td><td>{{ $receivable->due_date?->format('d M Y') ?? '-' }}</td><td>Rp {{ number_format($receivable->total_amount, 0, ',', '.') }}</td><td class="font-medium">Rp {{ number_format($receivable->balance, 0, ',', '.') }}</td><td><x-ui.badge :variant="$receivable->status === 'paid' ? 'success' : ($receivable->due_date?->isPast() ? 'danger' : 'warning')" dot>{{ $receivable->status === 'paid' ? 'Lunas' : ($receivable->due_date?->isPast() ? 'Terlambat' : ($receivable->status === 'partial' ? 'Sebagian' : 'Belum dibayar')) }}</x-ui.badge></td><td><a href="{{ route('pos.receivables.show', $receivable) }}" class="btn btn-info btn-sm"><x-ui.icon name="eye" />Lihat detail</a></td></tr>@endforeach
        </tbody></table></div>@endif
        <x-ui.pagination :paginator="$receivables" />
        </div>
    </div>
</x-layouts.admin>
