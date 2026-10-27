<x-layouts.admin title="Kasir" group="Penjualan">
    <x-ui.page-header :title="$draft ? 'Lanjutkan draft #'.$draft->id : 'Kasir'" description="Cari produk, susun keranjang, lalu selesaikan pembayaran atau simpan draft." />
    @if ($draft) <x-ui.alert variant="info" title="Mengedit draft">Harga dan stok saat ini ditampilkan. Simpan perubahan draft sebelum checkout bila isi keranjang berubah.</x-ui.alert> @endif
    <div class="grid gap-6 lg:grid-cols-2">
        <div class="card min-w-0" data-pos-server-table>
            <div class="p-6 pb-4"><h2 class="card-title">Produk tersedia</h2><p class="card-desc mt-1.5">Cari atau scan produk untuk menambahnya ke keranjang.</p></div>
            <div class="border-t p-6" data-pos-barcode data-lookup-url="{{ route('pos.cashier.barcode') }}">
                <x-ui.field id="cashier-barcode" label="Scan barcode" help="Scanner USB/Bluetooth: arahkan ke kolom ini lalu scan. Enter langsung menambah produk.">
                    <x-ui.input id="cashier-barcode" name="barcode_scan" icon="search" placeholder="Scan atau ketik barcode produk" autocomplete="off" data-barcode-input />
                </x-ui.field>
                <div class="mt-3 flex flex-wrap gap-2">
                    <x-ui.button data-barcode-add><x-ui.icon name="cart" />Tambah produk</x-ui.button>
                    <x-ui.button variant="outline" data-modal-open="#barcode-camera" data-barcode-camera><x-ui.icon name="image" />Scan kamera</x-ui.button>
                </div>
            </div>
            <x-ui.filter-bar :action="$draft ? route('pos.cashier', ['draft' => $draft->id]) : route('pos.cashier')" :reset-url="$draft ? route('pos.cashier', ['draft' => $draft->id]) : route('pos.cashier')" placeholder="Scan barcode atau cari nama/SKU" embedded live>
                @if ($draft)<input type="hidden" name="draft" value="{{ $draft->id }}">@endif
            </x-ui.filter-bar>
            <div data-pos-results>
                @if ($products->isEmpty()) <div class="border-t"><x-ui.table-state :state="request()->filled('q') ? 'filtered' : 'empty'" title="Produk tidak ditemukan" /></div>
                @else <div class="grid gap-4 border-t p-6 sm:grid-cols-2">
                    @foreach ($products as $product)
                        <button type="button" class="card cursor-pointer overflow-hidden text-left focus-visible:ring-2 focus-visible:ring-ring/50" data-cart-add data-id="{{ $product->id }}" data-name="{{ $product->name }}" data-price="{{ $product->sell_price }}" data-stock="{{ $product->stock_qty }}" data-image="{{ $product->image_path ? asset('storage/'.$product->image_path) : '' }}" aria-label="Tambah {{ $product->name }} ke keranjang">
                            @if ($product->image_path)
                                <img src="{{ asset('storage/'.$product->image_path) }}" alt="{{ $product->name }}" class="h-36 w-full object-cover" loading="lazy">
                            @else
                                <span class="flex h-36 items-center justify-center bg-soft text-soft-foreground"><x-ui.icon name="package" class="h-10 w-10" /></span>
                            @endif
                            <span class="block p-4">
                                <span class="block text-xs text-muted-foreground">{{ $product->category->name }} · {{ $product->sku }}</span>
                                <span class="block font-semibold">{{ $product->name }}</span>
                                <span class="mt-3 flex items-center justify-between gap-2"><span class="font-semibold tabular-nums">Rp {{ number_format($product->sell_price, 0, ',', '.') }}</span><x-ui.badge variant="success">Stok {{ $product->stock_qty }}</x-ui.badge></span>
                                <span class="btn btn-outline btn-sm mt-3 w-full"><x-ui.icon name="cart" />Tambah ke keranjang</span>
                            </span>
                        </button>
                    @endforeach
                </div>@endif
                <x-ui.pagination :paginator="$products" />
            </div>
        </div>
        <x-ui.modal id="barcode-camera" title="Scan barcode dengan kamera" description="Arahkan kamera ke barcode produk." size="xl">
            <x-ui.field id="cashier-camera-source" label="Sumber kamera" help="Pilih OBS Virtual Camera atau Iriun Webcam setelah memberi izin kamera di browser." class="mb-4">
                <x-ui.select id="cashier-camera-source" placeholder="Kamera otomatis" data-barcode-camera-source />
            </x-ui.field>
            <video class="w-full rounded-lg bg-muted" autoplay muted playsinline data-barcode-video></video>
            <p class="mt-3 text-sm text-muted-foreground" data-camera-status>Menyiapkan kamera...</p>
            <x-slot:footer><x-ui.button variant="outline" data-barcode-camera-retry><x-ui.icon name="refresh" />Coba lagi</x-ui.button><x-ui.button variant="outline" data-modal-close>Tutup kamera</x-ui.button></x-slot:footer>
        </x-ui.modal>
        <x-ui.card title="Keranjang">
            <div data-pos-cart data-storage-key="{{ $draft ? 'pos-draft-'.$draft->id.'-'.auth()->id() : 'pos-cart-'.auth()->id() }}" data-initial-cart="{{ json_encode($initialCart) }}" data-tax-rate="{{ $settings->tax_rate }}">
                <div class="space-y-3" data-cart-rows></div>
                <p data-cart-empty class="text-sm text-muted-foreground">Belum ada produk di keranjang.</p>
                <form method="POST" action="{{ route('pos.checkout') }}" data-cart-form class="mt-5 space-y-4">@csrf
                    <div data-cart-inputs></div>
                    <input type="hidden" name="idempotency_key" value="{{ $key }}">
                    @if ($draft)<input type="hidden" name="draft_id" value="{{ $draft->id }}">@endif
                    <x-ui.field label="Diskon nominal (Rp)" name="discount_amount"><x-ui.input name="discount_amount" type="number" min="0" :value="$draft?->payload_json['discount_amount'] ?? 0" placeholder="0 jika tanpa diskon" data-grouped-number data-cart-discount /></x-ui.field>
                    <dl class="border-t text-sm">
                        <div class="flex items-center justify-between gap-4 py-2"><dt class="text-muted-foreground">Subtotal</dt><dd class="text-right font-medium tabular-nums" data-cart-subtotal>Rp 0</dd></div>
                        <div class="flex items-center justify-between gap-4 py-2"><dt class="text-muted-foreground">Diskon</dt><dd class="text-right font-medium tabular-nums" data-cart-discount-total>Rp 0</dd></div>
                        <div class="flex items-center justify-between gap-4 py-2"><dt class="text-muted-foreground">Pajak</dt><dd class="text-right font-medium tabular-nums" data-cart-tax>Rp 0</dd></div>
                        <div class="flex items-center justify-between gap-4 border-t py-3 font-semibold"><dt class="text-base">Total</dt><dd class="text-right text-2xl tabular-nums" data-cart-total>Rp 0</dd></div>
                        <div class="flex items-center justify-between gap-4 border-t py-2"><dt class="text-muted-foreground">Kembalian</dt><dd class="text-right font-medium tabular-nums" data-cart-change>Rp 0</dd></div>
                    </dl>
                    @if ($draft)<x-ui.field label="Konfirmasi perubahan harga" name="acknowledge_changes"><x-ui.switch name="acknowledge_changes" label="Saya sudah meninjau harga dan stok terbaru" /></x-ui.field>@endif
                    <div class="flex flex-wrap gap-2"><x-ui.button data-modal-open="#cashier-payment" data-cart-pay-open><x-ui.icon name="card" />Bayar</x-ui.button><x-ui.button type="submit" variant="outline" :formaction="$draft ? route('pos.drafts.update', $draft) : route('pos.drafts.store')" name="_method" :value="$draft ? 'PUT' : 'POST'" formnovalidate data-cart-draft><x-ui.icon name="file-text" />{{ $draft ? 'Perbarui draft' : 'Simpan draft' }}</x-ui.button></div>
                    <x-ui.modal id="cashier-payment" title="Pembayaran" description="Pilih metode pembayaran dan periksa total transaksi.">
                        <x-ui.card title="Total pembayaran" class="bg-soft"><strong class="text-2xl tabular-nums" data-cart-modal-total>Rp 0</strong></x-ui.card>
                        <p class="label mt-5 mb-2">Metode pembayaran</p>
                        <div class="grid grid-cols-2 gap-2 sm:grid-cols-3">
                            @foreach (['cash' => ['Tunai', 'dollar'], 'transfer' => ['Transfer', 'card'], 'qris' => ['QRIS', 'grid'], 'midtrans' => ['Midtrans', 'zap'], 'credit' => ['Kasbon', 'users']] as $value => [$label, $icon])
                                <label class="cursor-pointer"><input type="radio" name="payment_method" value="{{ $value }}" class="peer sr-only" data-cart-method @checked($value === 'cash')><span class="flex flex-col items-center gap-2 rounded-lg border p-4 text-center peer-checked:border-primary peer-checked:bg-soft peer-focus-visible:ring-2 peer-focus-visible:ring-ring/50"><x-ui.icon :name="$icon" class="h-5 w-5" /><span>{{ $label }}</span></span></label>
                            @endforeach
                        </div>
                        <div class="mt-5 space-y-3" data-cart-credit-group hidden>
                            <x-ui.field label="Pelanggan" name="customer_id"><x-ui.select name="customer_id" placeholder="Pilih pelanggan" :value="$selectedCustomerId" :options="$customers" :searchable="true" /></x-ui.field>
                            <a href="{{ route('pos.customers.create', ['return_to' => 'cashier']) }}" class="btn btn-outline btn-sm"><x-ui.icon name="plus" />Tambah pelanggan</a>
                            <x-ui.field label="Jatuh tempo (opsional)" name="due_date"><x-ui.input name="due_date" type="date" placeholder="Pilih tanggal jatuh tempo" /></x-ui.field>
                            <x-ui.field label="Catatan (opsional)" name="credit_note"><x-ui.input name="credit_note" placeholder="Contoh: bayar saat gajian" /></x-ui.field>
                        </div>
                        <div class="mt-5" data-cart-paid-group><x-ui.field label="Jumlah dibayar (Rp)" name="amount_received"><x-ui.input name="amount_received" type="number" min="0" placeholder="Masukkan nominal pembayaran" required data-grouped-number data-cart-paid /></x-ui.field>
                            <div class="mt-3 flex flex-wrap gap-2" data-cart-presets>@foreach ([10000, 20000, 50000, 100000] as $preset)<x-ui.button variant="outline" size="sm" data-pay-preset="{{ $preset }}">{{ number_format($preset, 0, ',', '.') }}</x-ui.button>@endforeach</div>
                        </div>
                        <div class="mt-4" data-cart-error hidden><x-ui.alert variant="danger"><span data-cart-error-message></span></x-ui.alert></div>
                        <x-slot:footer><x-ui.button variant="outline" data-modal-close>Batal</x-ui.button><x-ui.button type="submit" data-cart-submit><x-ui.icon name="check" />Proses pembayaran</x-ui.button></x-slot:footer>
                    </x-ui.modal>
                </form>
            </div>
        </x-ui.card>
    </div>
    @if (config('services.midtrans.client_key'))<script src="{{ config('services.midtrans.is_production') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}" data-client-key="{{ config('services.midtrans.client_key') }}"></script>@endif
</x-layouts.admin>
