@php($editing = $product !== null)
<x-layouts.admin :title="$editing ? 'Ubah produk' : 'Tambah produk'" group="Master data">
    <x-ui.page-header :title="$editing ? 'Ubah produk' : 'Tambah produk'" />
    <form method="POST" enctype="multipart/form-data" action="{{ $editing ? route('pos.manage.products.update', $product) : route('pos.manage.products.store') }}" class="space-y-6">@csrf @if ($editing) @method('PUT') @endif
        <x-ui.card title="Informasi produk"><div class="grid gap-5 md:grid-cols-2">
            <x-ui.field label="Nama" name="name" :required="true"><x-ui.input name="name" :value="$product?->name" placeholder="Contoh: Air Mineral 600 ml" required /></x-ui.field>
            <x-ui.field id="generated-sku" label="SKU" help="Dibuat otomatis saat produk disimpan dan tidak perlu diisi."><x-ui.input id="generated-sku" name="sku_preview" :value="$product?->sku" placeholder="Dibuat otomatis setelah disimpan" readonly disabled /></x-ui.field>
            <x-ui.field label="Barcode" name="barcode" help="Opsional dan harus unik. Bisa diisi dengan scanner, kamera, atau diketik.">
                <x-ui.input name="barcode" :value="$product?->barcode" placeholder="Contoh: 8997000000012" autocomplete="off" data-product-barcode />
                <x-ui.button variant="outline" size="sm" class="mt-2" data-modal-open="#product-barcode-camera" data-product-camera-open><x-ui.icon name="image" />Scan dengan kamera</x-ui.button>
            </x-ui.field>
            <x-ui.field label="Kategori" name="category_id" :required="true"><x-ui.select name="category_id" placeholder="Pilih kategori" :value="$product?->category_id" :options="$categories" :searchable="true" required /></x-ui.field>
            <x-ui.field label="Satuan" name="unit_id" :required="true"><x-ui.select name="unit_id" placeholder="Pilih satuan" :value="$product?->unit_id" :options="$units" required /></x-ui.field>
            <x-ui.field label="Harga jual (Rp)" name="sell_price" :required="true"><x-ui.input name="sell_price" type="number" min="0" :value="$product?->sell_price" placeholder="Contoh: 15.000" required data-grouped-number /></x-ui.field>
            <x-ui.field label="Harga modal (Rp)" name="cost_price"><x-ui.input name="cost_price" type="number" min="0" :value="$product?->cost_price" placeholder="Contoh: 10.000" data-grouped-number /></x-ui.field>
            <x-ui.field label="Stok minimum" name="min_stock"><x-ui.input name="min_stock" type="number" min="0" :value="$product?->min_stock ?? 0" placeholder="Contoh: 10" required data-grouped-number /></x-ui.field>
            @unless ($editing)<x-ui.field label="Stok awal" name="initial_stock"><x-ui.input name="initial_stock" type="number" min="0" :value="0" placeholder="Jumlah stok saat dibuat" required data-grouped-number /></x-ui.field>@endunless
            <x-ui.field label="Deskripsi" name="description" class="md:col-span-2"><x-ui.textarea name="description" :value="$product?->description" placeholder="Deskripsi singkat produk (opsional)" /></x-ui.field>
            <x-ui.field label="Gambar produk (opsional)" name="image" class="md:col-span-2">
                @if ($product?->image_path)
                    <div class="flex items-center gap-3 rounded-lg border bg-card p-3">
                        <img src="{{ asset('storage/'.$product->image_path) }}" alt="Gambar {{ $product->name }}" class="h-12 w-12 rounded-md object-cover">
                        <span class="text-sm text-muted-foreground">Gambar saat ini. Pilih berkas baru untuk menggantinya.</span>
                    </div>
                @endif
                <x-ui.file-preview id="f-image" name="image" label="Pilih gambar produk" accept="image/png,image/jpeg" formats="PNG atau JPG" :max-mb="5" />
                @if ($product?->image_path)<x-ui.switch name="remove_image" label="Hapus gambar saat ini" hint="Berlaku jika tidak memilih gambar baru." />@endif
            </x-ui.field>
            <x-ui.field label="Status" name="is_active"><x-ui.switch name="is_active" label="Aktif" :checked="$product?->is_active ?? true" /></x-ui.field>
        </div></x-ui.card>
        <div class="flex justify-end gap-2"><a class="btn btn-outline" href="{{ route('pos.manage.products.index') }}"><x-ui.icon name="x" />Batal</a><x-ui.button type="submit"><x-ui.icon name="check" />Simpan</x-ui.button></div>
    </form>
    <x-ui.modal id="product-barcode-camera" title="Scan barcode produk" description="Arahkan kamera ke barcode untuk mengisi kolom produk." size="xl">
        <x-ui.field id="product-camera-source" label="Sumber kamera" help="Pilih OBS Virtual Camera atau Iriun Webcam setelah memberi izin kamera di browser." class="mb-4">
            <x-ui.select id="product-camera-source" placeholder="Kamera otomatis" data-product-camera-source />
        </x-ui.field>
        <video class="w-full rounded-lg bg-muted" autoplay muted playsinline data-product-camera-video></video>
        <p class="mt-3 text-sm text-muted-foreground" data-product-camera-status>Menyiapkan kamera...</p>
        <x-slot:footer><x-ui.button variant="outline" data-product-camera-retry><x-ui.icon name="refresh" />Coba lagi</x-ui.button><x-ui.button variant="outline" data-modal-close>Tutup kamera</x-ui.button></x-slot:footer>
    </x-ui.modal>
</x-layouts.admin>
