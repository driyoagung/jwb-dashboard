<x-layouts.admin title="Kasir" group="Penjualan">
    <x-ui.page-header :title="$draft ? 'Lanjutkan draft #'.$draft->id : 'Kasir'" description="Cari produk, susun keranjang, lalu selesaikan pembayaran atau simpan draft." />
    @if ($draft) <x-ui.alert variant="info" title="Mengedit draft">Harga dan stok saat ini ditampilkan. Simpan perubahan draft sebelum checkout bila isi keranjang berubah.</x-ui.alert> @endif
    <div class="grid gap-6 lg:grid-cols-2">
        <div class="card min-w-0" data-pos-server-table data-pos-barcode data-lookup-url="{{ route('pos.cashier.barcode') }}">
            <div class="p-6 pb-4"><h2 class="card-title">Produk tersedia</h2><p class="card-desc mt-1.5">Cari atau scan produk untuk menambahnya ke keranjang.</p></div>
            <x-ui.filter-bar :action="$draft ? route('pos.cashier', ['draft' => $draft->id]) : route('pos.cashier')" :reset-url="$draft ? route('pos.cashier', ['draft' => $draft->id]) : route('pos.cashier')" placeholder="Scan barcode atau cari nama/SKU" embedded live barcode>
                @if ($draft)<input type="hidden" name="draft" value="{{ $draft->id }}">@endif
                <x-ui.button type="button" variant="outline" size="sm" data-modal-open="#barcode-camera" data-barcode-camera><x-ui.icon name="image" />Scan kamera</x-ui.button>
            </x-ui.filter-bar>
            <p class="field-help px-6 pb-3">Scan barcode untuk langsung menambah produk. Ketik nama/SKU lalu klik kartu hasilnya untuk menambah produk.</p>
            <div data-pos-results>
                @if ($products->isEmpty()) <div class="border-t"><x-ui.table-state :state="request()->filled('q') ? 'filtered' : 'empty'" title="Produk tidak ditemukan" /></div>
                @else <div class="grid gap-4 border-t p-6 sm:grid-cols-2">
                    @foreach ($products as $product)
                        <button type="button" class="card cursor-pointer overflow-hidden text-left focus-visible:ring-2 focus-visible:ring-ring/50" data-cart-add data-id="{{ $product->id }}" data-name="{{ $product->name }}" data-price="{{ $product->sell_price }}" data-stock="{{ $product->stock_qty }}" data-image="{{ $product->image_path ? $product->image_url : '' }}" aria-label="Tambah {{ $product->name }} ke keranjang">
                            @if ($product->image_path)
                                <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="h-36 w-full object-cover" loading="lazy">
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
        <x-ui.card title="Keranjang" class="flex max-h-[calc(100dvh-6rem)] flex-col lg:sticky lg:top-20 lg:self-start [&>.card-content]:flex [&>.card-content]:min-h-0 [&>.card-content]:flex-1 [&>.card-content]:flex-col">
            <x-slot:header><h2 class="card-title">Keranjang</h2><p class="card-desc" data-cart-count aria-live="polite">0 jenis produk · 0 barang</p><p class="text-xs text-muted-foreground" data-cart-sync-status role="status"></p></x-slot:header>
            <div data-pos-cart data-storage-key="{{ $draft ? 'pos-draft-'.$draft->id.'-'.auth()->id() : 'pos-cart-'.auth()->id() }}" data-initial-cart="{{ json_encode($initialCart) }}" data-tax-rate="{{ $settings->tax_rate }}" data-quote-url="{{ route('pos.cashier.quote') }}" class="flex min-h-0 flex-1 flex-col">
                <div class="min-h-0 flex-1 space-y-3 overflow-y-auto overscroll-contain pr-1">
                    <div class="space-y-3" data-cart-rows></div>
                    <p data-cart-empty class="text-sm text-muted-foreground">Belum ada produk di keranjang.</p>
                </div>
                <form method="POST" action="{{ route('pos.checkout') }}" data-cart-form class="mt-4 max-h-[65dvh] shrink-0 space-y-3 overflow-y-auto border-t pt-3">@csrf
                    <div data-cart-inputs hidden></div>
                    <input type="hidden" name="idempotency_key" value="{{ $key }}">
                    <input type="hidden" name="quote_hash" data-cart-quote-hash disabled>
                    <input type="hidden" name="manual_payment_confirmed" value="1" disabled data-manual-confirmed>
                    @if ($draft)<input type="hidden" name="draft_id" value="{{ $draft->id }}">@endif
                    <x-ui.field label="Diskon nominal (Rp)" name="discount_amount"><x-ui.input name="discount_amount" type="number" min="0" :value="$draft?->payload_json['discount_amount'] ?? 0" placeholder="0 jika tanpa diskon" data-grouped-number data-cart-discount /></x-ui.field>
                    <dl class="border-t text-sm">
                        <div class="flex items-center justify-between gap-4 py-1.5"><dt class="text-muted-foreground">Subtotal</dt><dd class="text-right font-medium tabular-nums" data-cart-subtotal>Rp 0</dd></div>
                        <div class="flex items-center justify-between gap-4 py-1.5"><dt class="text-muted-foreground">Diskon</dt><dd class="text-right font-medium tabular-nums" data-cart-discount-total>Rp 0</dd></div>
                        <div class="flex items-center justify-between gap-4 py-1.5"><dt class="text-muted-foreground">Pajak</dt><dd class="text-right font-medium tabular-nums" data-cart-tax>Rp 0</dd></div>
                        <div class="flex items-center justify-between gap-4 border-t py-2 font-semibold"><dt class="text-base">Total</dt><dd class="text-right text-2xl tabular-nums" data-cart-total>Rp 0</dd></div>
                        <div class="flex items-center justify-between gap-4 border-t py-1.5"><dt class="text-muted-foreground" data-cart-change-label>Kembalian</dt><dd class="text-right font-medium tabular-nums" data-cart-change>Rp 0</dd></div>
                    </dl>
                    @if ($draft)<x-ui.field label="Konfirmasi perubahan harga" name="acknowledge_changes"><x-ui.switch name="acknowledge_changes" label="Saya sudah meninjau harga dan stok terbaru" /></x-ui.field>@endif
                    <div class="sticky bottom-0 flex flex-wrap gap-2 bg-card pt-2"><x-ui.button data-modal-open="#cashier-payment" data-cart-pay-open><x-ui.icon name="card" />Bayar</x-ui.button><x-ui.button type="submit" variant="outline" :formaction="$draft ? route('pos.drafts.update', $draft) : route('pos.drafts.store')" name="_method" :value="$draft ? 'PUT' : 'POST'" formnovalidate data-cart-draft><x-ui.icon name="file-text" />{{ $draft ? 'Perbarui draft' : 'Simpan draft' }}</x-ui.button></div>
                    <x-ui.modal id="cashier-payment" title="Pembayaran" description="Pilih metode pembayaran dan periksa total transaksi.">
                        <x-ui.card title="Total pembayaran" class="bg-soft"><strong class="text-2xl tabular-nums" data-cart-modal-total>Rp 0</strong></x-ui.card>
                        <p class="label mt-5 mb-2">Metode pembayaran</p>
                        <div class="grid grid-cols-2 gap-2 sm:grid-cols-3">
                            @foreach (['cash' => ['Tunai', 'dollar'], 'transfer' => ['Transfer manual', 'card'], 'qris' => ['QRIS merchant', 'grid'], 'midtrans' => ['Midtrans', 'zap'], 'credit' => ['Kasbon', 'users']] as $value => [$label, $icon])
                                @continue($value === 'credit' && ! auth()->user()->can('pos.credit'))
                                <label @class(['cursor-pointer', 'opacity-50' => $value === 'transfer' && ! $settings->manualTransferConfigured()])><input type="radio" name="payment_method" value="{{ $value }}" class="peer sr-only" data-cart-method @checked($value === 'cash') @disabled($value === 'transfer' && ! $settings->manualTransferConfigured())><span class="flex flex-col items-center gap-2 rounded-lg border p-4 text-center peer-checked:border-primary peer-checked:bg-soft peer-focus-visible:ring-2 peer-focus-visible:ring-ring/50"><x-ui.icon :name="$icon" class="h-5 w-5" /><span>{{ $label }}</span></span></label>
                            @endforeach
                        </div>
                        @unless($settings->manualTransferConfigured())<p class="mt-2 text-xs text-muted-foreground">Transfer tersedia setelah nomor tujuan dan nama penerima diisi pada Pengaturan toko.</p>@endunless
                        @can('pos.credit')<div class="mt-5 space-y-3" data-cart-credit-group hidden>
                            <x-ui.field label="Pelanggan" name="customer_id"><x-ui.select name="customer_id" placeholder="Pilih pelanggan" :value="$selectedCustomerId" :options="$customers" :searchable="true" /></x-ui.field>
                            <a href="{{ route('pos.customers.create', ['return_to' => 'cashier']) }}" class="btn btn-outline btn-sm"><x-ui.icon name="plus" />Tambah pelanggan</a>
                            <x-ui.field label="Jatuh tempo (opsional)" name="due_date"><x-ui.input name="due_date" type="date" placeholder="Pilih tanggal jatuh tempo" /></x-ui.field>
                            <x-ui.field label="Catatan (opsional)" name="credit_note"><x-ui.input name="credit_note" placeholder="Contoh: bayar saat gajian" /></x-ui.field>
                        </div>@endcan
                        <div class="mt-5" data-cart-paid-group><x-ui.field label="Jumlah dibayar (Rp)" name="amount_received"><x-ui.input name="amount_received" type="number" min="0" placeholder="Masukkan nominal pembayaran" required data-grouped-number data-cart-paid /></x-ui.field>
                            <div class="mt-3 flex flex-wrap gap-2" data-cart-presets>@foreach ([10000, 20000, 50000, 100000] as $preset)<x-ui.button variant="outline" size="sm" data-pay-preset="{{ $preset }}">{{ number_format($preset, 0, ',', '.') }}</x-ui.button>@endforeach</div>
                        </div>
                        <div class="mt-3" data-cart-shortfall hidden><x-ui.alert variant="warning"><span data-cart-shortfall-message></span></x-ui.alert></div>
                        <div class="mt-4 space-y-3" data-cart-error hidden><x-ui.alert variant="danger"><span data-cart-error-message></span></x-ui.alert><x-ui.button variant="outline" data-cart-reconcile><x-ui.icon name="refresh" />Sesuaikan stok &amp; harga</x-ui.button></div>
                        <x-slot:footer><x-ui.button variant="outline" data-modal-close>Batal</x-ui.button><x-ui.button type="submit" data-cart-submit><x-ui.icon name="check" /><span data-cart-submit-label>Proses pembayaran</span></x-ui.button></x-slot:footer>
                    </x-ui.modal>
                </form>
                <x-ui.modal id="manual-payment-confirmation" title="Konfirmasi pembayaran manual" description="Selesaikan pembayaran, lalu periksa dana masuk sebelum mencatat transaksi." size="xl">
                    <x-ui.card title="Total tagihan belanja" class="bg-soft"><strong class="text-2xl tabular-nums" data-manual-total>Rp 0</strong></x-ui.card>
                    <div class="mt-5 space-y-3" data-manual-transfer hidden>
                        <p>Silakan minta pembeli transfer sesuai total tagihan ke tujuan berikut.</p>
                        <x-ui.detail-list title="Tujuan transfer" :items="[['label' => 'Bank / e-wallet', 'value' => $settings->manual_transfer_provider], ['label' => 'Nomor tujuan', 'value' => $settings->manual_transfer_number], ['label' => 'Atas nama', 'value' => $settings->manual_transfer_name]]" />
                    </div>
                    <div class="mt-5 space-y-3" data-manual-qris hidden>
                        <p>Minta pembeli scan QRIS merchant berikut dan memasukkan nominal sesuai total tagihan.</p>
                        <img src="{{ asset('qris_griyo.jpeg') }}" alt="QRIS merchant untuk pembayaran manual" class="mx-auto max-h-[55vh] max-w-full rounded-md object-contain">
                    </div>
                    <x-ui.alert variant="warning" title="Periksa pembayaran di aplikasi" class="mt-5">Pastikan nominal dan dana masuk sudah terlihat di aplikasi penerima sebelum memproses pembayaran. POS tidak memeriksa Transfer atau QRIS manual secara otomatis.</x-ui.alert>
                    <x-slot:footer><x-ui.button variant="outline" data-manual-back>Kembali</x-ui.button><x-ui.button data-manual-confirm><x-ui.icon name="check" />Dana sudah masuk, selesaikan transaksi</x-ui.button></x-slot:footer>
                </x-ui.modal>
            </div>
        </x-ui.card>
    </div>
    @if (config('services.midtrans.client_key'))<script src="{{ config('services.midtrans.is_production') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}" data-client-key="{{ config('services.midtrans.client_key') }}"></script>@endif
</x-layouts.admin>
