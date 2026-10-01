<x-layouts.admin title="Status tabel" group="Komponen">
<x-ui.page-header title="Status tabel" description="Contoh tampilan tabel saat data tersedia, memuat, kosong, tidak ditemukan, atau gagal dimuat." />

<div class="card" data-table-state-demo>
    <div class="card-header"><h2 class="card-title">Daftar entri</h2><p class="card-desc">Pilih keadaan di bawah untuk melihat respons antarmuka.</p></div>
    <div class="flex flex-wrap gap-2 px-6 pb-5" role="group" aria-label="Contoh status tabel">
        @foreach (['ready' => 'Berisi', 'loading' => 'Memuat', 'empty' => 'Kosong', 'filtered' => 'Tidak ditemukan', 'error' => 'Error'] as $state => $label)
            <button type="button" class="filter-chip" data-table-state-set="{{ $state }}" aria-pressed="{{ $state === 'ready' ? 'true' : 'false' }}">{{ $label }}</button>
        @endforeach
    </div>
    <div class="border-t" data-table-state-view="ready">
        <div class="overflow-x-auto">
            <table class="table min-w-[520px]"><thead><tr><th>Judul</th><th>Pemilik</th><th>Status</th><th class="text-right">Diperbarui</th></tr></thead>
                <tbody>
                    <tr><td class="font-medium">Panduan onboarding</td><td>Ayu Rahmawati</td><td><span class="badge badge-success">Aktif</span></td><td class="text-right">Hari ini</td></tr>
                    <tr><td class="font-medium">Catatan rapat tim</td><td>Bagas Prasetyo</td><td><span class="badge badge-warning">Draf</span></td><td class="text-right">Kemarin</td></tr>
                    <tr><td class="font-medium">Checklist peluncuran</td><td>Maya Kusuma</td><td><span class="badge badge-info">Ditinjau</span></td><td class="text-right">28 Sep 2026</td></tr>
                </tbody>
            </table>
        </div>
        <p class="border-t px-6 py-3 text-sm text-muted-foreground">Menampilkan 3 dari 3 entri.</p>
    </div>
    <div class="border-t" data-table-state-view="loading" hidden><x-ui.table-state state="loading" /></div>
    <div class="border-t" data-table-state-view="empty" hidden><x-ui.table-state state="empty"><a href="{{ route('examples.records.create') }}" class="btn btn-primary"><x-ui.icon name="plus" />Tambah entri</a></x-ui.table-state></div>
    <div class="border-t" data-table-state-view="filtered" hidden><x-ui.table-state state="filtered"><button type="button" class="btn btn-outline" data-table-state-action="ready">Hapus filter</button></x-ui.table-state></div>
    <div class="border-t" data-table-state-view="error" hidden><x-ui.table-state state="error"><button type="button" class="btn btn-primary" data-table-state-action="ready">Coba lagi</button></x-ui.table-state></div>
</div>

<x-ui.card title="Prinsip penerapan" description="Gunakan komponen yang sama pada tabel proyek nyata.">
    <ul class="grid gap-3 text-sm sm:grid-cols-2">
        <li class="rounded-lg bg-muted/50 p-4"><span class="font-medium">Memuat</span><p class="mt-1 text-muted-foreground">Skeleton menjaga bentuk tabel tetap stabil saat data diambil.</p></li>
        <li class="rounded-lg bg-muted/50 p-4"><span class="font-medium">Kosong</span><p class="mt-1 text-muted-foreground">Ajak pengguna membuat data pertama, bukan menampilkan ruang kosong.</p></li>
        <li class="rounded-lg bg-muted/50 p-4"><span class="font-medium">Tidak ditemukan</span><p class="mt-1 text-muted-foreground">Berikan jalan cepat untuk menghapus filter.</p></li>
        <li class="rounded-lg bg-muted/50 p-4"><span class="font-medium">Error</span><p class="mt-1 text-muted-foreground">Jelaskan masalah dan sediakan tindakan untuk mencoba lagi.</p></li>
    </ul>
</x-ui.card>
</x-layouts.admin>
