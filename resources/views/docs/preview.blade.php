<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pratinjau {{ $previewName }} · Kenanga Admin</title>
    <script>if (new URLSearchParams(location.search).get('theme') === 'dark') document.documentElement.dataset.theme = 'dark'; else document.documentElement.dataset.theme = 'light';</script>
    <link rel="stylesheet" href="./build/{{ $css }}">
    <style>body{padding:24px;background:hsl(var(--background));color:hsl(var(--foreground));} .preview-stack{display:grid;gap:16px}.preview-grid{display:grid;gap:16px;grid-template-columns:repeat(auto-fit,minmax(190px,1fr))} .preview-row{display:flex;flex-wrap:wrap;align-items:center;gap:10px} .preview-label{font-size:12px;color:hsl(var(--muted-foreground));margin-bottom:8px} #toasts{position:fixed;bottom:16px;right:16px;z-index:70;max-width:320px}</style>
</head>
<body class="font-sans text-sm antialiased">
<main class="preview-stack">
@if($previewName === 'button')
        <div class="preview-row">
            @foreach(['primary' => 'Utama', 'secondary' => 'Sekunder', 'outline' => 'Outline', 'ghost' => 'Ghost', 'soft' => 'Soft', 'destructive' => 'Hapus', 'success' => 'Sukses', 'warning' => 'Peringatan', 'info' => 'Info', 'link' => 'Tautan'] as $variant => $label)
                <x-ui.button :variant="$variant">{{ $label }}</x-ui.button>
            @endforeach
        </div>
        <div class="preview-row"><x-ui.button size="sm">Kecil</x-ui.button><x-ui.button size="lg">Besar</x-ui.button><x-ui.button size="icon" aria-label="Pengaturan"><x-ui.icon name="settings" /></x-ui.button><x-ui.button disabled>Nonaktif</x-ui.button></div>
@elseif($previewName === 'badge')
        <div class="preview-row">
            @foreach(['primary','soft','success','danger','warning','info','neutral','outline','solid-success','solid-danger','solid-warning','solid-info'] as $variant)
                <x-ui.badge :variant="$variant">{{ $variant }}</x-ui.badge>
            @endforeach
        </div>
        <div class="preview-row"><x-ui.badge variant="success" :dot="true">Aktif</x-ui.badge><x-ui.badge variant="info" :pill="true" icon="info">Pill dengan ikon</x-ui.badge></div>
@elseif($previewName === 'card')
        <div class="preview-grid">
            <x-ui.card title="Kartu dasar" description="Judul, deskripsi, dan isi."><p>Informasi utama berada di sini.</p></x-ui.card>
            <x-ui.card title="Dengan footer"><p>Konten dan tindakan.</p><x-slot:footer><x-ui.button size="sm">Simpan</x-ui.button></x-slot:footer></x-ui.card>
        </div>
        <div class="preview-grid"><div class="card p-5"><p class="text-muted-foreground">Target bulanan</p><p class="mt-2 text-2xl font-semibold">78%</p><div class="progress mt-3"><div class="progress-bar" style="width:78%"></div></div></div><div class="rounded-xl bg-primary p-5 text-primary-foreground"><p>Saldo tersedia</p><p class="mt-2 text-2xl font-semibold">Rp 12,8 jt</p></div></div>
        <div class="preview-grid"><div class="rounded-xl border border-dashed p-5"><p class="text-sm text-muted-foreground">Outline putus-putus</p><p class="mt-2 text-2xl font-semibold">2.481</p></div><div class="rounded-xl bg-success-soft p-5 text-success"><p class="text-sm">Pertumbuhan tahunan</p><p class="mt-2 text-2xl font-semibold">+24,8%</p></div></div>
        <div class="preview-grid"><div class="card p-5"><span class="avatar avatar-lg">AR</span><h3 class="mt-3 font-semibold">Ayu Rahmawati</h3><p class="text-muted-foreground">Manajer toko · Yogyakarta</p></div><div class="card p-5"><h3 class="font-semibold">Aktivitas terbaru</h3><p class="mt-3 text-sm">Pesanan #KK-2841 selesai</p><p class="text-xs text-muted-foreground">10 menit lalu</p></div></div>
        <div class="preview-grid"><div class="card p-5"><h3 class="font-semibold">Paket Pro</h3><p class="mt-3 text-2xl font-semibold">Rp 149 rb <span class="text-sm text-muted-foreground">/ bulan</span></p><x-ui.button class="mt-4">Pilih Pro</x-ui.button></div><div class="card overflow-hidden"><div class="flex h-20 items-center justify-center bg-soft text-soft-foreground"><x-ui.icon name="coffee" class="h-8 w-8" /></div><div class="p-4"><h3 class="font-semibold">Arabika Gayo 1 kg</h3><p>Rp 170.000</p></div></div></div>
        <div class="preview-grid"><div class="card p-5"><h3 class="font-semibold">Tugas hari ini</h3><label class="mt-3 flex items-center gap-2"><input type="checkbox" /> Restok biji kopi</label></div><div class="card p-5"><h3 class="font-semibold">Pesan masuk</h3><p class="mt-3">Putri Maharani <span class="text-xs text-muted-foreground">· 09.12</span></p><p class="text-muted-foreground">Apakah biji Gayo bisa digiling?</p></div></div>
        <div class="preview-grid"><blockquote class="card p-5">“Dashboard ini bikin rekap harian lebih cepat.”<footer class="mt-3 text-xs text-muted-foreground">Hendra · Pemilik</footer></blockquote><div class="card flex overflow-hidden"><span class="flex w-20 shrink-0 items-center justify-center bg-soft text-soft-foreground"><x-ui.icon name="truck" /></span><div class="p-5"><h3 class="font-semibold">Gratis ongkir</h3><p class="text-muted-foreground">Pembelian di atas Rp 250.000.</p></div></div></div>
        <div class="card p-6"><x-ui.empty title="Belum ada laporan" description="Buat laporan pertama untuk melihat ringkasan." /></div>
@elseif($previewName === 'stat-card')
        <div class="preview-grid"><x-ui.stat-card title="Pendapatan" value="Rp 58,4 jt" icon="dollar" delta="+13,2%" spark="[12,18,16,23,30]" /><x-ui.stat-card title="Pesanan aktif" value="356" icon="cart" delta="−2,4%" direction="down" tone="danger" spark="[25,23,24,20,18]" /></div>
@elseif($previewName === 'alert')
        <x-ui.alert variant="info" title="Informasi">Pengaturan berlaku setelah halaman dimuat ulang.</x-ui.alert>
        <x-ui.alert variant="success" title="Berhasil">Perubahan tersimpan.</x-ui.alert>
        <x-ui.alert variant="warning" title="Perhatian">Stok produk mulai menipis.</x-ui.alert>
        <x-ui.alert variant="danger" title="Gagal">Periksa kembali masukan Anda.</x-ui.alert>
        <x-ui.alert variant="info" title="Pengumuman" :solid="true">Periksa pengaturan sebelum melanjutkan.<x-slot:action><x-ui.button variant="outline" size="sm">Lihat</x-ui.button></x-slot:action></x-ui.alert>
@elseif($previewName === 'chart')
        <div class="preview-grid"><x-ui.chart type="line" aria="Tren pesanan" :height="180" :labels="['Sen','Sel','Rab','Kam']" :series="[['name'=>'Pesanan','data'=>[20,32,27,45]]]" /><x-ui.chart type="bar" aria="Produk terjual" :height="180" :labels="['Sen','Sel','Rab','Kam']" :series="[['name'=>'Produk','data'=>[12,22,16,34]]]" /></div>
        <div class="preview-grid"><x-ui.chart type="area" aria="Pertumbuhan entri" :height="180" :labels="['Sen','Sel','Rab','Kam']" :series="[['name'=>'Entri','data'=>[13,19,22,34]]]" /><x-ui.chart type="stacked" aria="Kontribusi kanal" :height="180" :labels="['Sen','Sel','Rab','Kam']" :series="[['name'=>'Web','data'=>[6,8,10,12]],['name'=>'Toko','data'=>[4,5,4,7]]]" /></div>
        <div class="preview-grid"><x-ui.chart type="donut" aria="Komposisi entri" :items="[['label'=>'Manual','value'=>60],['label'=>'Impor','value'=>40]]" total-label="Entri" /><x-ui.chart type="hbar" aria="Penjualan produk" :items="[['label'=>'Arabika','value'=>35],['label'=>'Robusta','value'=>20]]" /></div>
@elseif($previewName === 'combobox')
        <x-ui.combobox id="preview-combobox" label="Kategori" name="category" :options="[['value'=>'panduan','label'=>'Panduan'],['value'=>'laporan','label'=>'Laporan'],['value'=>'arsip','label'=>'Arsip']]" />
@elseif($previewName === 'date-range')
        <x-ui.date-range id="preview-period" from-name="from" to-name="to" />
@elseif($previewName === 'select')
        <div class="preview-grid"><x-ui.field label="Select biasa" name="category"><x-ui.select name="category" placeholder="Pilih kategori" :options="['panduan'=>'Panduan','laporan'=>'Laporan']" /></x-ui.field><x-ui.field label="Dapat dicari" name="status"><x-ui.select name="status" placeholder="Semua status" :searchable="true" :options="['aktif'=>'Aktif','draf'=>'Draf','arsip'=>'Arsip']" /></x-ui.field></div>
@elseif($previewName === 'field')
        <x-ui.field name="email" label="Email" help="Gunakan alamat email kerja." :required="true"><x-ui.input name="email" type="email" placeholder="nama@perusahaan.id" required /></x-ui.field>
@elseif($previewName === 'input')
        <div class="preview-grid"><x-ui.field label="Teks" name="name"><x-ui.input name="name" placeholder="Nama lengkap" /></x-ui.field><x-ui.field label="Dengan ikon" name="email"><x-ui.input name="email" icon="mail" placeholder="Alamat email" /></x-ui.field><x-ui.field label="Harga" name="price"><x-ui.input name="price" addon="Rp" placeholder="170000" /></x-ui.field><x-ui.field label="Tidak valid" name="invalid"><x-ui.input name="invalid" :invalid="true" value="Periksa kembali" /></x-ui.field></div>
@elseif($previewName === 'textarea')
        <x-ui.field label="Ringkasan" name="summary" help="Jelaskan entri dalam beberapa kalimat."><x-ui.textarea name="summary" rows="4" placeholder="Tulis ringkasan…" /></x-ui.field>
@elseif($previewName === 'switch')
        <x-ui.switch name="notify" label="Terima notifikasi" hint="Pemberitahuan penting dikirim ke email Anda." :checked="true" /><x-ui.switch name="publish" label="Terbitkan entri" />
@elseif($previewName === 'modal')
        <div class="preview-row"><x-ui.button data-modal-open="#preview-modal">Buka modal</x-ui.button></div>
        <x-ui.modal id="preview-modal" title="Tinjau perubahan" description="Pastikan data sudah sesuai." icon="info"><p>Perubahan ini hanya pratinjau.</p><x-slot:footer><x-ui.button variant="outline" data-modal-close>Tutup</x-ui.button></x-slot:footer></x-ui.modal>
@elseif($previewName === 'drawer')
        <x-ui.button data-modal-open="#preview-drawer">Buka drawer filter</x-ui.button><x-ui.drawer id="preview-drawer" title="Filter pesanan" description="Pilih kriteria untuk daftar."><x-ui.field label="Status" name="status"><x-ui.select name="status" :options="['aktif'=>'Aktif','draf'=>'Draf']" /></x-ui.field><x-slot:footer><x-ui.button variant="outline" data-modal-close>Tutup</x-ui.button></x-slot:footer></x-ui.drawer>
@elseif($previewName === 'empty')
        <x-ui.empty icon="package" title="Belum ada produk" description="Tambahkan produk pertama agar muncul di katalog."><x-ui.button size="sm">Tambah produk</x-ui.button></x-ui.empty>
@elseif($previewName === 'table-state')
        <div class="preview-grid"><div class="card"><x-ui.table-state state="empty" /></div><div class="card"><x-ui.table-state state="filtered" title="Tidak ada hasil"><x-ui.button variant="outline" size="sm">Hapus filter</x-ui.button></x-ui.table-state></div></div>
        <div class="preview-grid"><div class="card"><x-ui.table-state state="error" title="Data gagal dimuat"><x-ui.button variant="outline" size="sm">Coba lagi</x-ui.button></x-ui.table-state></div><div class="card"><x-ui.table-state state="loading" /></div></div>
@elseif($previewName === 'pagination')
        <p class="text-sm text-muted-foreground">Komponen ini masih membutuhkan implementasi elemen halaman. Gunakan paginator Laravel: <code>$records->links()</code>.</p>
@elseif($previewName === 'page-header')
        <x-ui.page-header title="Pesanan" description="Pantau dan kelola seluruh pesanan toko." />
@elseif($previewName === 'icon')
        <div class="preview-row">@foreach(['search','cart','chart','settings','check-circle','bell','coffee'] as $name)<span class="inline-flex items-center gap-2 rounded-md border bg-card p-3"><x-ui.icon :name="$name" class="h-5 w-5" />{{ $name }}</span>@endforeach</div>
@elseif($previewName === 'file-preview')
        <x-ui.file-preview id="preview-file" label="Pilih gambar atau PDF" />
@elseif($previewName === 'filter-bar')
        <x-ui.filter-bar action="#" reset-url="#"><div class="min-w-40 space-y-1.5"><label class="label" for="preview-filter-status">Status</label><x-ui.select id="preview-filter-status" name="status" placeholder="Semua status" :searchable="true" :options="['aktif'=>'Aktif','draf'=>'Draf']" /></div><x-slot:active><x-ui.badge variant="info">Status: Aktif</x-ui.badge></x-slot:active></x-ui.filter-bar>
@elseif($previewName === 'detail-list')
        <x-ui.detail-list title="Informasi entri" :items="[['label'=>'Kode','value'=>'ENT-001'],['label'=>'Judul','value'=>'Panduan onboarding'],['label'=>'Pemilik','value'=>'Ayu Rahmawati'],['label'=>'Status','value'=>'Aktif']]" />
@elseif($previewName === 'timeline')
        <x-ui.timeline :items="[['title'=>'Entri diterbitkan','time'=>'Hari ini','tone'=>'success','description'=>'Versi terbaru tersedia untuk tim.'],['title'=>'Ditinjau','time'=>'Kemarin','description'=>'Konten disetujui.'],['title'=>'Dibuat','time'=>'1 Okt 2026']]" />
@elseif($previewName === 'tabs')
        @include('docs.previews.tabs')
@elseif($previewName === 'confirm-action')
        <x-ui.button variant="destructive" data-modal-open="#preview-confirm">Hapus entri</x-ui.button><x-ui.confirm-action id="preview-confirm" title="Hapus entri?" description="Contoh tampilan konfirmasi." action="#"><p>Pratinjau: tidak terhubung ke data asli.</p></x-ui.confirm-action>
@endif
</main>
<div id="toasts" aria-live="polite"></div>
<script>document.addEventListener('submit', function (event) { event.preventDefault(); });</script>
<script type="module" src="./build/{{ $js }}"></script>
</body></html>
