<x-layouts.admin title="Formulir" group="Komponen">
<x-ui.page-header title="Formulir" description="Input, select, checkbox, radio, switch, slider, unggah berkas, dan validasi." />

<section class="grid gap-4 lg:grid-cols-2">
  <x-ui.card title="Input teks" description="Label, bantuan, ikon, dan grup input." class="min-w-0">
    <div class="space-y-4">
      <div class="space-y-2"><label class="label" for="f-name">Nama lengkap</label><input id="f-name" class="input" placeholder="Ayu Rahmawati"><p class="field-help">Nama yang tampil di profil publik.</p></div>
      <div class="space-y-2"><label class="label" for="f-email">Email</label>
        <div class="relative"><x-ui.icon name="mail" class="pointer-events-none absolute left-2.5 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" /><input id="f-email" type="email" class="input pl-8" placeholder="ayu@kopikenanga.id"></div></div>
      <div class="space-y-2"><label class="label" for="f-price">Harga</label><div class="input-group"><span class="input-addon">Rp</span><input id="f-price" class="input" placeholder="170000"><span class="input-addon">/ kg</span></div></div>
      <div class="space-y-2"><label class="label" for="f-site">Situs web</label><div class="input-group"><span class="input-addon">https://</span><input id="f-site" class="input" placeholder="kopikenanga.id"></div></div>
      <div class="space-y-2"><label class="label" for="f-msg">Catatan</label><textarea id="f-msg" class="textarea" placeholder="Tulis catatan untuk pelanggan"></textarea></div>
    </div>
  </x-ui.card>

  <x-ui.card title="Status validasi" description="Valid, tidak valid, nonaktif, dan hanya baca." class="min-w-0">
    <div class="space-y-4">
      <div class="space-y-2"><label class="label" for="v-ok">Username</label><input id="v-ok" class="input input-valid" value="kopikenanga"><p class="field-ok">Username tersedia.</p></div>
      <div class="space-y-2"><label class="label" for="v-bad">Email</label><input id="v-bad" class="input input-invalid" value="ayu@kopikenanga" aria-invalid="true"><p class="field-error">Format email tidak valid.</p></div>
      <div class="space-y-2"><label class="label" for="v-dis">Nonaktif</label><input id="v-dis" class="input" value="Tidak bisa diubah" disabled></div>
      <div class="space-y-2"><label class="label" for="v-ro">Hanya baca</label><input id="v-ro" class="input" value="KK-2841" readonly></div>
    </div>
  </x-ui.card>
</section>

<section class="grid gap-4 lg:grid-cols-2">
  <x-ui.card title="Select, tanggal, dan slider" class="min-w-0">
    <div class="space-y-4">
      <div class="space-y-2"><label class="label" for="s-cat">Kategori</label><select id="s-cat" class="select"><option>Biji kopi</option><option>Peralatan</option><option>Paket</option></select></div>
      <div class="grid grid-cols-2 gap-3">
        <div class="space-y-2"><label class="label" for="s-date">Tanggal</label><input id="s-date" type="date" class="input"></div>
        <div class="space-y-2"><label class="label" for="s-time">Waktu</label><input id="s-time" type="time" class="input"></div>
      </div>
      <div class="space-y-2"><div class="flex justify-between"><label class="label" for="s-range">Diskon</label><span class="text-sm text-muted-foreground">20%</span></div><input id="s-range" type="range" class="w-full" min="0" max="100" value="20"></div>
    </div>
  </x-ui.card>

  <x-ui.card title="Checkbox, radio, dan switch" class="min-w-0">
    <div class="grid gap-6 sm:grid-cols-2">
      <fieldset class="space-y-3"><legend class="label mb-3">Kanal penjualan</legend>
        <label class="flex items-center gap-2"><input type="checkbox" checked> Situs web</label>
        <label class="flex items-center gap-2"><input type="checkbox" checked> Marketplace</label>
        <label class="flex items-center gap-2"><input type="checkbox"> WhatsApp</label>
        <label class="flex items-center gap-2 opacity-50"><input type="checkbox" disabled> Toko fisik</label>
      </fieldset>
      <fieldset class="space-y-3"><legend class="label mb-3">Pengiriman</legend>
        <label class="flex items-center gap-2"><input type="radio" name="ship" checked> Reguler</label>
        <label class="flex items-center gap-2"><input type="radio" name="ship"> Ekspres</label>
        <label class="flex items-center gap-2"><input type="radio" name="ship"> Ambil di toko</label>
      </fieldset>
    </div>
    <div class="mt-6 space-y-3 border-t pt-5">
      <div class="flex items-center justify-between"><span>Terima pesanan otomatis</span><label class="switch"><input type="checkbox" class="peer sr-only" checked aria-label="Terima pesanan otomatis"><span class="switch-track"></span></label></div>
      <div class="flex items-center justify-between"><span>Kirim email konfirmasi</span><label class="switch"><input type="checkbox" class="peer sr-only" aria-label="Kirim email konfirmasi"><span class="switch-track"></span></label></div>
      <div class="flex items-center justify-between opacity-60"><span>Mode pemeliharaan</span><label class="switch"><input type="checkbox" class="peer sr-only" disabled aria-label="Mode pemeliharaan"><span class="switch-track"></span></label></div>
    </div>
  </x-ui.card>
</section>

<section class="grid gap-4 lg:grid-cols-2">
  <x-ui.card title="Kartu pilihan" description="Radio berbentuk kartu untuk pilihan penting." class="min-w-0">
    <div class="grid gap-3 sm:grid-cols-2">
      <label class="cursor-pointer"><input type="radio" name="plan" class="peer sr-only" checked><span class="block rounded-lg border p-4 peer-checked:border-primary peer-checked:bg-soft peer-focus-visible:ring-2 peer-focus-visible:ring-ring/50"><span class="flex items-center gap-2 font-medium"><x-ui.icon name="package" />Reguler</span><span class="mt-1 block text-sm text-muted-foreground">2–3 hari · Rp 22.000</span></span></label>
      <label class="cursor-pointer"><input type="radio" name="plan" class="peer sr-only"><span class="block rounded-lg border p-4 peer-checked:border-primary peer-checked:bg-soft peer-focus-visible:ring-2 peer-focus-visible:ring-ring/50"><span class="flex items-center gap-2 font-medium"><x-ui.icon name="zap" />Ekspres</span><span class="mt-1 block text-sm text-muted-foreground">Besok tiba · Rp 45.000</span></span></label>
    </div>
  </x-ui.card>

  <x-ui.card title="Unggah berkas" description="Area seret dan lepas." class="min-w-0">
    <label class="dropzone cursor-pointer">
      <span class="flex h-10 w-10 items-center justify-center rounded-full bg-soft text-soft-foreground"><x-ui.icon name="upload" class="h-5 w-5" /></span>
      <span class="font-medium">Klik untuk memilih atau seret berkas ke sini</span>
      <span class="field-help">PNG, JPG, atau PDF. Maksimal 5 MB.</span>
      <input type="file" class="sr-only">
    </label>
  </x-ui.card>
</section>

<div class="card">
  <div class="card-header"><h2 class="card-title">Contoh formulir lengkap: tambah produk</h2><p class="card-desc">Tata letak dua kolom dengan tindakan di bawah.</p></div>
  <div class="card-content">
    <div class="grid gap-4 md:grid-cols-2">
      <div class="space-y-2"><label class="label" for="p-name">Nama produk</label><input id="p-name" class="input" placeholder="Arabika Gayo 1 kg"></div>
      <div class="space-y-2"><label class="label" for="p-sku">SKU</label><input id="p-sku" class="input" placeholder="KP-001"></div>
      <div class="space-y-2"><label class="label" for="p-cat">Kategori</label><select id="p-cat" class="select"><option>Biji kopi</option><option>Peralatan</option><option>Paket</option></select></div>
      <div class="space-y-2"><label class="label" for="p-price">Harga</label><div class="input-group"><span class="input-addon">Rp</span><input id="p-price" class="input" placeholder="170000"></div></div>
      <div class="space-y-2 md:col-span-2"><label class="label" for="p-desc">Deskripsi</label><textarea id="p-desc" class="textarea" placeholder="Deskripsi singkat produk"></textarea></div>
    </div>
  </div>
  <div class="card-footer justify-end"><button type="button" class="btn btn-outline">Batal</button><button type="button" class="btn btn-primary" data-toast="success" data-toast-title="Produk disimpan">Simpan produk</button></div>
</div>
</x-layouts.admin>
