# 📈 Chart

`<x-ui.chart>` menaruh konfigurasi grafik pada atribut `data-*`; `resources/js/admin/charts.js` menggambar SVG saat halaman siap. Tidak ada dependensi chart eksternal. File komponen: `resources/views/components/ui/chart.blade.php`.

## 🧩 API dan bentuk data

| Prop | Default | Kegunaan |
| --- | --- | --- |
| `type` | wajib | `line`, `area`, `bar`, `stacked`, `donut`, `radial`, `spark`, `hbar` |
| `labels`, `series`, `items`, `values` | `[]` | Array data sesuai tipe grafik |
| `height` | `240` | Tinggi grafik kartesius/spark (px) |
| `aria` | `Grafik` | Label SVG untuk aksesibilitas |
| `summary` | `null` | `<figcaption>` sesudah grafik |
| `legend`, `smooth` | `false` | Penanda/kurva pada grafik kartesius |
| `prefix`, `suffix` | `''` | Format angka tooltip/label |
| `size`, `totalLabel` | `null` | Ukuran/label total (terutama donut) |

Array PHP dikodekan menjadi JSON pada atribut. Pilih tipe berdasarkan pertanyaan data: tren waktu (`line`/`area`), perbandingan (`bar`), kontribusi bertumpuk (`stacked`), komposisi (`donut`).

## 📉 Line dan area: dua contoh tren

```blade
<x-ui.chart type="line" aria="Kunjungan harian situs dan aplikasi"
    :labels="['Sen', 'Sel', 'Rab', 'Kam']"
    :series="[
        ['name' => 'Situs', 'data' => [42, 48, 46, 58], 'color' => 1],
        ['name' => 'Aplikasi', 'data' => [26, 31, 29, 36], 'color' => 2],
    ]" summary="Kunjungan situs tertinggi pada Kamis." />

<x-ui.chart type="area" :smooth="true" aria="Tren entri empat bulan"
    :labels="['Jun', 'Jul', 'Agu', 'Sep']"
    :series="[['name' => 'Entri', 'data' => [18, 25, 29, 38], 'color' => 2]]" />
```

Array `data` setiap seri harus sejajar dengan `labels`. Dua seri pada line otomatis memunculkan legenda. `smooth` berarti perender menggunakan kurva pada garis/area, bukan filter statistik atas data.

## 📊 Bar dan stacked: perbandingan

```blade
<x-ui.chart type="bar" aria="Pesanan setiap bulan" :legend="true"
    :labels="['Jul', 'Agu', 'Sep']"
    :series="[['name' => 'Pesanan', 'data' => [34, 42, 51], 'color' => 1]]" />

<x-ui.chart type="stacked" aria="Pekerjaan per kanal"
    :labels="['Sen', 'Sel', 'Rab']"
    :series="[
        ['name' => 'Manual', 'data' => [12, 15, 14], 'color' => 1],
        ['name' => 'Otomatis', 'data' => [7, 8, 10], 'color' => 2],
    ]" />
```

## 🍩 Donut dan hbar: komposisi

```blade
<x-ui.chart type="donut" aria="Sumber entri" total-label="Entri" :size="160"
    :items="[
        ['label' => 'Manual', 'value' => 520, 'color' => 1],
        ['label' => 'Impor', 'value' => 280, 'color' => 2],
        ['label' => 'Otomatis', 'value' => 200, 'color' => 3],
    ]" summary="Dari 1.000 entri, 52% dibuat manual." />

<x-ui.chart type="hbar" aria="Pekerjaan per tim" suffix=" pekerjaan"
    :items="[
        ['label' => 'Tim inti', 'value' => 35, 'color' => 1],
        ['label' => 'Kolaborator', 'value' => 20, 'color' => 2],
    ]" />
```

Donut dan hbar perlu nilai positif; renderer membagi terhadap total/nilai maksimum. Jika semuanya nol, tampilkan [table state kosong](/komponen/table-state) alih-alih menghitung persentase 0/0. Hbar saat ini menyusun label melalui `innerHTML` dalam JS: jangan mengirim teks label bebas dari pengguna tanpa escape/sanitasi.

## ⚙️ Radial dan spark: atribut tambahan

Tipe `radial` membaca `data-value`, `data-label`, dan `data-color`, sedangkan `spark` dapat membaca `data-kind`/`data-color`. `x-ui.chart` **tidak memiliki prop khusus** untuk atribut itu dan `$attributes` pada komponen diterapkan ke `<figure>`, bukan elemen grafik dalamnya. Oleh karena itu, tulis markup `data-chart` langsung atau perluas komponen sebelum memakai tipe tersebut.

```blade
<div data-chart="radial" data-value="72" data-label="Target" data-color="chart-2" data-size="120"></div>
<div data-chart="spark" data-aria="Tren pesanan" data-values="[12,15,14,19,24]"
    data-kind="area" data-color="success" data-height="48"></div>
```

Gunakan nama warna CSS dari sistem (`chart-1`..`chart-5`, `success`), bukan nilai tak tepercaya dari request. `x-ui.stat-card` sudah menyajikan sparkline di dalam kartu KPI.

## 🔄 Data Laravel, aksesibilitas, render ulang

Siapkan `labels`/`series`/`items` pada controller dan kirim melalui `:labels="$labels"`, `:series="$series"`. Berikan `aria` yang menjelaskan grafik dan `summary` berisi temuan dalam bahasa biasa. `window.Charts.init(scope)` hanya menginisialisasi elemen baru; setelah mengganti `dataset` pada elemen lama, panggil `window.Charts.draw(element)` untuk redraw. Grafik yang tidak memiliki data sebaiknya diganti state kosong. Contoh query terhubung ada di [grafik dari server](/integrasi/data#siapkan-grafik-dari-data-nyata).
