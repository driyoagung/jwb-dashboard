<x-layouts.admin title="Pola data" group="Komponen">
<x-ui.page-header title="Pola data" description="Filter, detail, riwayat, tab, dan konfirmasi tindakan yang dapat dipakai ulang." />
@if(session('status'))<x-ui.alert variant="info">{{ session('status') }}</x-ui.alert>@endif

<x-ui.filter-bar :action="route('showcase.data-patterns')" :search="request('q')" :reset-url="route('showcase.data-patterns')" placeholder="Cari entri…">
    <div class="min-w-40 space-y-1.5">
        <label class="label" for="pattern-status">Status</label>
        <x-ui.select id="pattern-status" name="status" placeholder="Semua status" :value="request('status')"
            :options="['aktif' => 'Aktif', 'draf' => 'Draf', 'arsip' => 'Arsip']" :searchable="true" />
    </div>
    <x-slot:active>
        @if(request()->filled('q'))<x-ui.badge variant="soft">Cari: {{ request('q') }}</x-ui.badge>@endif
        @if(request()->filled('status'))<x-ui.badge variant="info">Status: {{ request('status') }}</x-ui.badge>@endif
        @if(!request()->filled('q') && !request()->filled('status'))<span class="text-muted-foreground">Belum ada filter.</span>@endif
    </x-slot:active>
</x-ui.filter-bar>

<div class="grid gap-4 lg:grid-cols-2">
    <x-ui.detail-list title="Informasi entri" :items="[
        ['label' => 'Kode', 'value' => 'ENT-001'],
        ['label' => 'Judul', 'value' => 'Panduan onboarding'],
        ['label' => 'Pemilik', 'value' => 'Ayu Rahmawati'],
        ['label' => 'Kategori', 'value' => 'Panduan'],
    ]" />

    <x-ui.card title="Riwayat entri" description="Urutan kejadian yang mudah dipindai.">
        <x-ui.timeline :items="[
            ['title' => 'Entri diterbitkan', 'time' => 'Hari ini, 10.30', 'datetime' => '2026-10-03T10:30:00', 'description' => 'Versi terbaru tersedia untuk tim.', 'tone' => 'success'],
            ['title' => 'Konten ditinjau', 'time' => 'Kemarin, 15.20', 'description' => 'Bagas menyetujui isi dokumen.'],
            ['title' => 'Entri dibuat', 'time' => '1 Okt 2026', 'description' => 'Ayu membuat draf pertama.'],
        ]" />
    </x-ui.card>
</div>

<x-ui.card title="Tab dan konfirmasi" description="Konten tab dirender server; aksi hapus memakai form Laravel.">
    <x-ui.tabs id="record-tabs" aria-label="Informasi entri" :tabs="['summary' => 'Ringkasan', 'history' => 'Riwayat', 'settings' => 'Pengaturan']" variant="pill">
        <x-slot:tab_summary><p class="text-sm text-muted-foreground">Panduan orientasi untuk anggota tim baru.</p></x-slot:tab_summary>
        <x-slot:tab_history><p class="text-sm text-muted-foreground">Perubahan terakhir dilakukan oleh Bagas kemarin.</p></x-slot:tab_history>
        <x-slot:tab_settings><p class="text-sm text-muted-foreground">Aksi di bawah ini hanya demonstrasi frontend.</p></x-slot:tab_settings>
    </x-ui.tabs>
    <div class="mt-5 border-t pt-5">
        <x-ui.button variant="destructive" size="sm" data-modal-open="#demo-confirm">Lihat konfirmasi</x-ui.button>
    </div>
</x-ui.card>

<x-ui.confirm-action id="demo-confirm" title="Hapus entri?" description="Contoh form konfirmasi; tidak ada data yang dihapus."
    :action="route('showcase.data-patterns.preview')" method="POST" label="Konfirmasi">
    <p class="text-sm text-muted-foreground">Saat memakai data asli, ganti action dengan route DELETE dan tambahkan otorisasi pada server.</p>
</x-ui.confirm-action>
</x-layouts.admin>
