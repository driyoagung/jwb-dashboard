<x-layouts.admin title="Filter dan input" group="Komponen">
<x-ui.page-header title="Filter dan input" description="Pola pencarian dan pemilihan yang bisa dipakai tanpa backend." />

<section class="grid gap-4 lg:grid-cols-2">
    <x-ui.card title="Combobox" description="Ketik untuk menyaring pilihan; gunakan panah dan Enter dari keyboard.">
        <x-ui.combobox id="demo-category" label="Kategori" name="category" placeholder="Cari kategori…" :options="[
            ['value' => 'dokumen', 'label' => 'Dokumen'],
            ['value' => 'laporan', 'label' => 'Laporan'],
            ['value' => 'panduan', 'label' => 'Panduan'],
            ['value' => 'catatan', 'label' => 'Catatan tim'],
            ['value' => 'arsip', 'label' => 'Arsip'],
        ]" />
        <p class="field-help mt-3">Nilai terpilih tersedia di input tersembunyi untuk dipakai dalam formulir Laravel.</p>
    </x-ui.card>

    <x-ui.card title="Rentang tanggal" description="Preset cepat dan pilihan tanggal manual dengan validasi urutan.">
        <x-ui.date-range id="demo-period" />
    </x-ui.card>

    <x-ui.card title="Filter chip" description="Gabungkan beberapa kriteria dan lihat pilihan aktif.">
        <div data-filter-chips>
            <p class="label mb-3">Status</p>
            <div class="flex flex-wrap gap-2" role="group" aria-label="Filter status">
                <button type="button" class="filter-chip" aria-pressed="true" data-filter-chip>Semua</button>
                <button type="button" class="filter-chip" aria-pressed="false" data-filter-chip>Aktif</button>
                <button type="button" class="filter-chip" aria-pressed="false" data-filter-chip>Menunggu</button>
                <button type="button" class="filter-chip" aria-pressed="false" data-filter-chip>Arsip</button>
            </div>
            <p class="mt-4 text-sm text-muted-foreground" role="status" aria-live="polite" data-filter-summary>Menampilkan semua status.</p>
        </div>
    </x-ui.card>

    <x-ui.card title="Pratinjau berkas" description="Pilih atau seret berkas; validasi berlangsung di browser, tanpa unggahan ke server.">
        <x-ui.file-preview id="demo-upload" label="Pilih gambar atau PDF" />
    </x-ui.card>
</section>
</x-layouts.admin>
