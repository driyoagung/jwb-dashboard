<x-layouts.admin title="Riwayat harga" group="Master data">
    <x-ui.page-header title="Riwayat harga" description="Harga awal dan perubahan harga modal/jual, beserta pengguna yang menyimpannya." />
    <div class="card" data-pos-server-table>
        <div class="p-6"><form method="GET" action="{{ route('pos.manage.price-histories.index') }}" class="flex flex-wrap items-end gap-3" data-pos-live-filter>
            @if(request('product_id'))<input type="hidden" name="product_id" value="{{ request('product_id') }}">@endif
            <x-ui.field name="q" label="Cari produk"><x-ui.input name="q" :value="request('q')" placeholder="Nama atau SKU produk" icon="search" /></x-ui.field>
            <div class="space-y-1.5"><span class="label">Jenis harga</span><div class="flex flex-wrap gap-2" role="group" aria-label="Jenis harga" data-pos-filter-group>
                <input type="hidden" name="price_type" value="{{ request('price_type') }}" data-pos-filter-value>
                @foreach(['' => 'Semua', 'cost' => 'Harga modal', 'sell' => 'Harga jual'] as $value => $label)<button type="button" class="filter-chip" data-pos-filter-option="{{ $value }}" aria-pressed="{{ (string) request('price_type') === $value ? 'true' : 'false' }}">{{ $label }}</button>@endforeach
            </div></div>
            <a class="btn btn-outline" href="{{ route('pos.manage.price-histories.index') }}"><x-ui.icon name="refresh" />Atur ulang</a>
        </form></div>
        <div data-pos-results>
            @if($histories->isEmpty())<x-ui.table-state state="empty" title="Belum ada riwayat harga" description="Perubahan harga berikutnya akan dicatat otomatis." />
            @else<div class="overflow-x-auto border-t"><table class="table table-striped"><thead><tr><th>Waktu</th><th>Produk</th><th>Jenis</th><th>Harga lama</th><th>Harga baru</th><th>Pengguna</th></tr></thead><tbody>
                @foreach($histories as $history)<tr><td>{{ $history->created_at->timezone($zone)->format('d M Y H:i:s') }}</td><td><p class="font-medium">{{ $history->product->name }}</p><span class="text-xs text-muted-foreground">{{ $history->product->sku }}{{ $history->product->trashed() ? ' · Dihapus dari katalog' : '' }}</span></td><td><x-ui.badge :variant="$history->price_type === 'cost' ? 'warning' : 'info'">{{ $history->price_type === 'cost' ? 'Modal' : 'Jual' }}</x-ui.badge></td><td class="tabular-nums">{{ $history->old_price === null ? 'Belum tercatat' : 'Rp '.number_format($history->old_price, 0, ',', '.') }}</td><td class="tabular-nums">{{ $history->new_price === null ? 'Tidak diisi' : 'Rp '.number_format($history->new_price, 0, ',', '.') }}</td><td>{{ $history->user->name }}</td></tr>@endforeach
            </tbody></table></div>@endif
            <x-ui.pagination :paginator="$histories" />
        </div>
    </div>
</x-layouts.admin>
