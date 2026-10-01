<x-layouts.admin title="Detail entri" group="Contoh halaman">
@php($record = config('kenanga.demo.records.0'))
<div class="flex flex-wrap items-start justify-between gap-4">
    <div><a href="{{ route('examples.records.index') }}" class="mb-3 inline-flex items-center gap-1 text-sm text-muted-foreground hover:text-foreground"><x-ui.icon name="arrow-left" class="h-4 w-4" />Kembali ke daftar</a><h1 class="text-2xl font-semibold tracking-tight">{{ $record['title'] }}</h1><p class="mt-1 text-muted-foreground">Contoh halaman detail dengan informasi utama dan riwayat.</p></div>
    <a href="{{ route('examples.records.edit') }}" class="btn btn-primary"><x-ui.icon name="edit" />Ubah entri</a>
</div>

<div class="grid gap-4 lg:grid-cols-[minmax(0,2fr)_minmax(16rem,1fr)]">
    <x-ui.card title="Ringkasan" description="Informasi yang paling sering dibutuhkan di halaman detail.">
        <p class="leading-relaxed">Langkah awal bagi anggota tim yang baru bergabung. Contoh entri ini menunjukkan bagaimana judul, metadata, dan catatan dapat disusun tanpa mengikat template pada satu jenis proyek.</p>
        <dl class="mt-6 grid gap-5 border-t pt-5 sm:grid-cols-2">
            <div><dt class="text-sm text-muted-foreground">Kode</dt><dd class="mt-1 font-medium">{{ $record['code'] }}</dd></div>
            <div><dt class="text-sm text-muted-foreground">Kategori</dt><dd class="mt-1 font-medium">{{ $record['category'] }}</dd></div>
            <div><dt class="text-sm text-muted-foreground">Pemilik</dt><dd class="mt-1 font-medium">{{ $record['owner'] }}</dd></div>
            <div><dt class="text-sm text-muted-foreground">Status</dt><dd class="mt-1"><span class="badge badge-{{ $record['tone'] }}">{{ $record['status'] }}</span></dd></div>
        </dl>
    </x-ui.card>
    <x-ui.card title="Aktivitas" description="Jejak perubahan singkat untuk memberi konteks.">
        <ol class="space-y-5 text-sm">
            <li class="flex gap-3"><span class="mt-1 h-2 w-2 shrink-0 rounded-full bg-primary"></span><div><p class="font-medium">Entri diperbarui</p><p class="text-muted-foreground">Ayu Rahmawati · Hari ini, 09.24</p></div></li>
            <li class="flex gap-3"><span class="mt-1 h-2 w-2 shrink-0 rounded-full bg-muted-foreground"></span><div><p class="font-medium">Status menjadi aktif</p><p class="text-muted-foreground">Bagas Prasetyo · Kemarin</p></div></li>
            <li class="flex gap-3"><span class="mt-1 h-2 w-2 shrink-0 rounded-full bg-muted-foreground"></span><div><p class="font-medium">Entri dibuat</p><p class="text-muted-foreground">Ayu Rahmawati · 26 Sep 2026</p></div></li>
        </ol>
    </x-ui.card>
</div>
</x-layouts.admin>
