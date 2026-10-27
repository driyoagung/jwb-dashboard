<x-layouts.admin :title="'Draft #'.$draft->id" group="Penjualan">
    <x-ui.page-header :title="'Draft #'.$draft->id" :description="'Kasir: '.$draft->cashier->name" />
    @if ($changes->isNotEmpty()) <x-ui.alert variant="warning" title="Perubahan produk">Harga, stok, atau status produk berubah sejak draft disimpan. Tinjau ulang sebelum checkout.</x-ui.alert> @endif
    @php($active = $draft->status === 'active' && $draft->expires_at->isFuture())
    @unless ($active) <x-ui.alert variant="warning" title="Draft tidak aktif">Draft ini hanya dapat dilihat atau dibatalkan.</x-ui.alert> @endunless
    <x-ui.card title="Isi draft">
        <div class="overflow-x-auto"><table class="table"><thead><tr><th>Produk</th><th>Harga saat disimpan</th><th>Harga kini</th><th>Stok kini</th><th>Jumlah</th></tr></thead><tbody>
            @foreach ($saved as $item) @php($product = $products[$item['product_id']] ?? null)
                <tr><td>{{ $item['product_name'] }}</td><td>Rp {{ number_format($item['unit_price'], 0, ',', '.') }}</td><td>{{ $product ? 'Rp '.number_format($product->sell_price, 0, ',', '.') : 'Produk hilang' }}</td><td>{{ $product?->stock_qty ?? 0 }}</td><td>{{ $item['qty'] }}</td></tr>
            @endforeach
        </tbody></table></div>
    </x-ui.card>
    @if ($active)
        <a class="btn btn-outline" href="{{ route('pos.cashier', ['draft' => $draft->id]) }}"><x-ui.icon name="cart" />Lanjutkan dan bayar di kasir</a>
        <x-ui.card title="Perbarui jumlah">
            <form method="POST" action="{{ route('pos.drafts.update', $draft) }}" class="space-y-4">@csrf @method('PUT')
                @foreach ($saved as $index => $item)
                    <input type="hidden" name="items[{{ $index }}][product_id]" value="{{ $item['product_id'] }}">
                    <x-ui.field :label="$item['product_name']" :name="'items.'.$index.'.qty'"><x-ui.input :name="'items['.$index.'][qty]'" type="number" min="1" :value="$item['qty']" placeholder="Jumlah produk" required data-grouped-number /></x-ui.field>
                @endforeach
                <x-ui.field label="Diskon (Rp)" name="discount_amount"><x-ui.input name="discount_amount" type="number" min="0" :value="$draft->payload_json['discount_amount'] ?? 0" placeholder="0 jika tanpa diskon" data-grouped-number /></x-ui.field>
                <x-ui.button type="submit"><x-ui.icon name="check" />Simpan perubahan</x-ui.button>
            </form>
        </x-ui.card>
    @endif
    @if ($draft->status === 'active')<x-ui.confirm-action :id="'cancel-draft'" title="Batalkan draft?" description="Draft tidak dapat dilanjutkan setelah dibatalkan." :action="route('pos.drafts.destroy', $draft)" label="Batalkan draft"><x-ui.button variant="outline" data-modal-open="#cancel-draft"><x-ui.icon name="x" />Batalkan draft</x-ui.button></x-ui.confirm-action>@endif
</x-layouts.admin>
