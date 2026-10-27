<x-layouts.admin title="Riwayat transaksi" group="Penjualan">
    <x-ui.page-header title="Riwayat transaksi" description="Pantau transaksi selesai, tertunda, dan gagal." />
    <div class="card" data-pos-server-table>
        <div class="p-6 pb-4"><h2 class="card-title">Transaksi</h2><p class="card-desc mt-1.5">Cari dan filter riwayat transaksi.</p></div>
        <x-ui.filter-bar :action="route('pos.transactions.index')" :reset-url="route('pos.transactions.index')" placeholder="Nomor transaksi" embedded live>
            <div class="min-w-40"><label class="sr-only" for="from">Dari</label><x-ui.input id="from" name="from" :value="request('from')" placeholder="Tanggal awal" autocomplete="off" data-date-picker /></div>
            <div class="min-w-40"><label class="sr-only" for="to">Sampai</label><x-ui.input id="to" name="to" :value="request('to')" placeholder="Tanggal akhir" autocomplete="off" data-date-picker /></div>
            <div class="min-w-40"><label class="sr-only" for="method">Pembayaran</label><x-ui.select id="method" name="payment_method" placeholder="Semua metode" :value="request('payment_method')" :options="['cash' => 'Tunai', 'transfer' => 'Transfer', 'qris' => 'QRIS', 'midtrans' => 'Midtrans', 'credit' => 'Kasbon']" /></div>
            <div class="flex flex-wrap gap-2" role="group" aria-label="Filter status transaksi" data-pos-filter-group>
                <input type="hidden" name="status" value="{{ request('status', '') }}" data-pos-filter-value>
                @foreach (['' => 'Semua', 'completed' => 'Selesai', 'pending' => 'Menunggu', 'failed' => 'Gagal'] as $value => $label)
                    <button type="button" class="filter-chip" data-pos-filter-option="{{ $value }}" aria-pressed="{{ request('status', '') === $value ? 'true' : 'false' }}">{{ $label }}</button>
                @endforeach
            </div>
            @can('pos.admin')
                @if($cashiers->count() < 5)
                    <div class="flex flex-wrap gap-2" role="group" aria-label="Filter kasir" data-pos-filter-group>
                        <input type="hidden" name="cashier_id" value="{{ request('cashier_id', '') }}" data-pos-filter-value>
                        @foreach((['' => 'Semua kasir'] + $cashiers->all()) as $value => $label)
                            <button type="button" class="filter-chip" data-pos-filter-option="{{ $value }}" aria-pressed="{{ (string) request('cashier_id', '') === (string) $value ? 'true' : 'false' }}">{{ $label }}</button>
                        @endforeach
                    </div>
                @else
                    <div class="min-w-40"><label class="sr-only" for="cashier">Kasir</label><x-ui.select id="cashier" name="cashier_id" placeholder="Semua kasir" :value="request('cashier_id')" :options="$cashiers" :searchable="true" /></div>
                @endif
            @endcan
        </x-ui.filter-bar>
        <div data-pos-results>
            @if ($transactions->isEmpty()) <div class="border-t"><x-ui.table-state :state="request()->hasAny(['q', 'from', 'to', 'payment_method', 'status']) ? 'filtered' : 'empty'" title="Belum ada transaksi" /></div>
            @else <div class="overflow-x-auto border-t"><table class="table table-striped min-w-[760px]"><thead><tr>
                @foreach (['transaction_number' => 'Nomor', 'created_at' => 'Waktu'] as $column => $label)<th aria-sort="{{ request('sort', 'created_at') === $column ? (request('direction', 'desc') === 'asc' ? 'ascending' : 'descending') : 'none' }}"><a href="{{ request()->fullUrlWithQuery(['sort' => $column, 'direction' => request('sort', 'created_at') === $column && request('direction', 'desc') === 'asc' ? 'desc' : 'asc', 'page' => 1]) }}" class="inline-flex items-center gap-1">{{ $label }}<x-ui.icon name="chevron-down" class="h-3 w-3" /></a></th>@endforeach
                <th>Kasir</th><th>Item</th><th aria-sort="{{ request('sort') === 'grand_total' ? (request('direction', 'asc') === 'asc' ? 'ascending' : 'descending') : 'none' }}"><a href="{{ request()->fullUrlWithQuery(['sort' => 'grand_total', 'direction' => request('sort') === 'grand_total' && request('direction', 'asc') === 'asc' ? 'desc' : 'asc', 'page' => 1]) }}" class="inline-flex items-center gap-1">Total<x-ui.icon name="chevron-down" class="h-3 w-3" /></a></th><th>Pembayaran</th><th>Status</th><th scope="col">Aksi</th></tr></thead><tbody>
                @foreach ($transactions as $transaction)<tr><td class="font-medium">{{ $transaction->transaction_number }}</td><td class="text-muted-foreground">{{ $transaction->created_at->timezone($zone)->format('d M Y H:i') }}</td><td>{{ $transaction->cashier->name }}</td><td class="tabular-nums">{{ $transaction->items_count }}</td><td class="tabular-nums">Rp {{ number_format($transaction->grand_total, 0, ',', '.') }}</td><td><x-ui.badge variant="info" dot>{{ strtoupper($transaction->payment_method) }}</x-ui.badge></td><td><x-ui.badge :variant="match ($transaction->status) { 'completed' => 'success', 'pending' => 'warning', default => 'danger' }" dot>{{ match ($transaction->status) { 'completed' => 'Selesai', 'pending' => 'Menunggu', default => 'Gagal' } }}</x-ui.badge></td><td><a class="btn btn-info btn-sm" href="{{ route('pos.transactions.show', $transaction) }}"><x-ui.icon name="eye" />Lihat detail</a></td></tr>@endforeach
            </tbody></table></div>@endif
            <x-ui.pagination :paginator="$transactions" />
        </div>
    </div>
</x-layouts.admin>
