<x-layouts.admin title="Draft transaksi" group="Penjualan">
    <x-ui.page-header title="Draft transaksi" description="Draft tidak memesan atau mengurangi stok." />
    <div class="card" data-pos-server-table>
        <div class="flex flex-col gap-3 p-6 pb-4 sm:flex-row sm:items-center sm:justify-between"><div><h2 class="card-title">Daftar draft</h2><p class="card-desc mt-1.5">Cari dan filter draft transaksi.</p></div><a class="btn btn-primary btn-sm" href="{{ route('pos.cashier') }}"><x-ui.icon name="cart" />Buka kasir</a></div>
        <x-ui.filter-bar :action="route('pos.drafts.index')" :reset-url="route('pos.drafts.index')" placeholder="Cari nomor draft" embedded live>
            <div class="min-w-40"><label class="sr-only" for="draft-status">Status</label><x-ui.select id="draft-status" name="status" placeholder="Semua status" :value="request('status')" :options="['active' => 'Aktif', 'pending_payment' => 'Menunggu pembayaran', 'expired' => 'Kedaluwarsa', 'cancelled' => 'Dibatalkan', 'completed' => 'Selesai']" /></div>
        </x-ui.filter-bar>
        <div data-pos-results>
            @if ($drafts->isEmpty()) <div class="border-t"><x-ui.table-state :state="request()->hasAny(['q', 'status']) ? 'filtered' : 'empty'" title="Belum ada draft" /></div>
            @else <div class="overflow-x-auto border-t"><table class="table table-striped"><thead><tr><th>Nomor</th><th>Kasir</th><th>Item</th><th>Status</th><th>Kedaluwarsa</th><th scope="col">Aksi</th></tr></thead><tbody>
                @foreach ($drafts as $draft)<tr><td class="font-medium">#{{ $draft->id }}</td><td>{{ $draft->cashier->name }}</td><td class="tabular-nums">{{ count($draft->payload_json['items'] ?? []) }}</td><td><x-ui.badge :variant="$draft->status === 'active' && $draft->expires_at->isFuture() ? 'success' : 'neutral'" dot>{{ $draft->status === 'active' && $draft->expires_at->isPast() ? 'Kedaluwarsa' : ucfirst($draft->status) }}</x-ui.badge></td><td class="text-muted-foreground">{{ $draft->expires_at->format('d M Y H:i') }}</td><td><a class="btn btn-info btn-sm" href="{{ route('pos.drafts.show', $draft) }}"><x-ui.icon name="eye" />Lihat detail</a></td></tr>@endforeach
            </tbody></table></div>@endif
            <x-ui.pagination :paginator="$drafts" />
        </div>
    </div>
</x-layouts.admin>
