<x-layouts.admin title="Pencarian global" group="Ringkasan">
    <x-ui.page-header title="Pencarian global" description="Cari produk, transaksi, atau nomor draft." />
    <div class="space-y-6" data-pos-server-table>
    <x-ui.filter-bar :action="route('pos.search')" :reset-url="route('pos.search')" search-name="q" :search="$term" placeholder="Nama/SKU produk, nomor transaksi, atau nomor draft" live />
    <div class="space-y-6" data-pos-results>
    @if (mb_strlen($term) < 2)
        <x-ui.card><x-ui.table-state state="empty" title="Masukkan minimal 2 karakter untuk mencari" /></x-ui.card>
    @else
        <div class="card">
            <div class="p-6 pb-4"><h2 class="card-title">Produk</h2><p class="card-desc mt-1.5">Hasil produk sesuai kata kunci.</p></div>
            @if ($products->isEmpty()) <div class="border-t"><x-ui.table-state state="filtered" title="Produk tidak ditemukan" /></div>
            @else <div class="overflow-x-auto border-t"><table class="table table-striped"><thead><tr><th>Produk</th><th>Stok</th><th scope="col">Aksi</th></tr></thead><tbody>
                @foreach ($products as $product)<tr><td><div class="flex items-center gap-3">@if ($product->image_path)<img src="{{ asset('storage/'.$product->image_path) }}" alt="" class="h-9 w-9 rounded-md object-cover" loading="lazy">@else<span class="avatar avatar-soft avatar-square"><x-ui.icon name="package" /></span>@endif<div class="leading-tight"><p class="font-medium">{{ $product->name }}</p><p class="text-xs text-muted-foreground">{{ $product->sku }}</p></div></div></td><td class="tabular-nums">{{ $product->stock_qty }}</td><td><a class="btn btn-info btn-sm" href="{{ auth()->user()->isAdmin() ? route('pos.manage.products.edit', $product) : route('pos.cashier', ['q' => $product->sku]) }}"><x-ui.icon name="eye" />Lihat</a></td></tr>@endforeach
            </tbody></table></div>@endif
        </div>
        <div class="card">
            <div class="p-6 pb-4"><h2 class="card-title">Transaksi</h2><p class="card-desc mt-1.5">Hasil nomor transaksi sesuai kata kunci.</p></div>
            @if ($transactions->isEmpty()) <div class="border-t"><x-ui.table-state state="filtered" title="Transaksi tidak ditemukan" /></div>
            @else <div class="overflow-x-auto border-t"><table class="table table-striped"><thead><tr><th>Nomor</th><th>Kasir</th><th>Total</th><th scope="col">Aksi</th></tr></thead><tbody>
                @foreach ($transactions as $transaction)<tr><td class="font-medium">{{ $transaction->transaction_number }}</td><td>{{ $transaction->cashier->name }}</td><td class="tabular-nums">Rp {{ number_format($transaction->grand_total, 0, ',', '.') }}</td><td><a class="btn btn-info btn-sm" href="{{ route('pos.transactions.show', $transaction) }}"><x-ui.icon name="eye" />Lihat</a></td></tr>@endforeach
            </tbody></table></div>@endif
        </div>
        @if ($drafts->isNotEmpty())<div class="card"><div class="p-6 pb-4"><h2 class="card-title">Draft</h2></div><div class="overflow-x-auto border-t"><table class="table table-striped"><thead><tr><th>Nomor</th><th>Status</th><th scope="col">Aksi</th></tr></thead><tbody>
            @foreach ($drafts as $draft)<tr><td class="font-medium">#{{ $draft->id }}</td><td><x-ui.badge :variant="$draft->status === 'active' ? 'success' : 'neutral'" dot>{{ ucfirst($draft->status) }}</x-ui.badge></td><td><a class="btn btn-info btn-sm" href="{{ route('pos.drafts.show', $draft) }}"><x-ui.icon name="eye" />Lihat</a></td></tr>@endforeach
        </tbody></table></div></div>@endif
    @endif
    </div>
    </div>
</x-layouts.admin>
