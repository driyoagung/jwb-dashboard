<x-layouts.admin title="Grafik" group="Komponen">
<x-ui.page-header title="Grafik" description="Pola visualisasi untuk tren, perbandingan, komposisi, dan keadaan tanpa data." />

<section class="grid gap-4 xl:grid-cols-2">
    <x-ui.card title="Grafik garis" description="Bandingkan dua seri dari waktu ke waktu.">
        <x-ui.chart type="line" aria="Grafik garis kunjungan situs dan aplikasi selama tujuh hari" :labels="['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min']" :series="[
            ['name' => 'Situs', 'data' => [42, 48, 46, 58, 62, 68, 73], 'color' => 1],
            ['name' => 'Aplikasi', 'data' => [26, 31, 29, 36, 39, 44, 48], 'color' => 2],
        ]" summary="Keduanya naik sepanjang pekan; situs memiliki kunjungan lebih tinggi setiap hari." />
    </x-ui.card>

    <x-ui.card title="Grafik batang" description="Bandingkan nilai antarperiode dengan cepat.">
        <x-ui.chart type="bar" aria="Grafik batang tugas selesai per bulan" :labels="['Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep']" :series="[['name' => 'Tugas selesai', 'data' => [34, 42, 39, 51, 57, 64], 'color' => 1]]" :legend="true" summary="Jumlah tugas selesai tertinggi pada September: 64." />
    </x-ui.card>

    <x-ui.card title="Grafik area" description="Tekankan perkembangan total dalam satu seri.">
        <x-ui.chart type="area" aria="Grafik area pertumbuhan entri selama enam bulan" :labels="['Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep']" :series="[['name' => 'Entri', 'data' => [18, 25, 29, 38, 43, 56], 'color' => 2]]" :smooth="true" summary="Entri bertambah dari 18 pada April menjadi 56 pada September." />
    </x-ui.card>

    <x-ui.card title="Grafik bertumpuk" description="Lihat jumlah sekaligus kontribusi setiap kategori.">
        <x-ui.chart type="stacked" aria="Grafik batang bertumpuk pekerjaan per kanal" :labels="['Sen', 'Sel', 'Rab', 'Kam', 'Jum']" :series="[
            ['name' => 'Tim inti', 'data' => [22, 26, 24, 31, 29], 'color' => 1],
            ['name' => 'Kolaborator', 'data' => [12, 15, 14, 17, 20], 'color' => 2],
            ['name' => 'Otomatis', 'data' => [7, 8, 10, 9, 12], 'color' => 3],
        ]" summary="Kontribusi tim inti tetap menjadi bagian terbesar pada setiap hari." />
    </x-ui.card>

    <x-ui.card title="Grafik donat" description="Komposisi dari satu total, disertai angka dan persentase.">
        <x-ui.chart type="donut" aria="Grafik donat sumber entri" :items="[
            ['label' => 'Manual', 'value' => 520, 'color' => 1],
            ['label' => 'Impor', 'value' => 280, 'color' => 2],
            ['label' => 'Otomatis', 'value' => 200, 'color' => 3],
        ]" total-label="Entri" summary="Dari 1.000 entri, 52% dibuat manual, 28% dari impor, dan 20% otomatis." />
    </x-ui.card>

    <x-ui.card title="Tanpa data" description="Jangan tampilkan grafik kosong atau sumbu tanpa nilai.">
        <x-ui.table-state state="empty" title="Belum ada data grafik" description="Grafik akan muncul setelah data untuk periode ini tersedia.">
            <button type="button" class="btn btn-outline" data-toast="info" data-toast-title="Pilih periode lain untuk melihat contoh data">Ganti periode</button>
        </x-ui.table-state>
    </x-ui.card>
</section>
</x-layouts.admin>
