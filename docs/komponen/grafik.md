# 📊 Grafik & statistik

`x-ui.chart` menghasilkan elemen `data-chart` yang digambar sebagai SVG oleh `resources/js/admin/charts.js` tanpa dependensi chart eksternal. Nilai mengikuti token warna `--chart-1` hingga `--chart-5` dalam `resources/css/app.css`.

## 🧱 Grafik dari komponen {#grafik-dari-komponen}

Tipe yang didukung JavaScript: `line`, `bar`, `area`, `stacked`, `donut`, `radial`, `spark`, `hbar`. Komponen mendukung `type`, `labels`, `series`, `items`, `values`, `height` (default 240), `aria`, `summary`, `legend`, `smooth`, `prefix`, `suffix`, `size`, `totalLabel`. Bentuk data tergantung tipe; contoh resmi ada di `resources/views/showcase/charts.blade.php` dan dashboard.

```blade
<x-ui.chart
    type="bar"
    aria="Jumlah pesanan setiap bulan"
    :labels="['Jul', 'Agu', 'Sep']"
    :series="[['name' => 'Pesanan', 'data' => [24, 32, 40], 'color' => 1]]"
    :legend="true"
    summary="Jumlah pesanan meningkat selama tiga bulan." />
```

Untuk donat gunakan `items` dengan label dan nilai:

```blade
<x-ui.chart type="donut" aria="Komposisi kanal pesanan"
    :items="[
        ['label' => 'Situs', 'value' => 60, 'color' => 1],
        ['label' => 'Toko', 'value' => 40, 'color' => 2],
    ]" total-label="Pesanan" />
```

`labels` menunjukkan sumbu horizontal untuk grafik kartesius; tiap item di `series` memiliki `name`, `data` (urutan sama dengan labels) dan `color` opsional. `summary` menjadi `<figcaption>` sehingga grafik memiliki penjelasan tekstual. Data angka bukan hasil query otomatis; siapkan arraynya di controller seperti [contoh grafik dari server](/integrasi/data).

### 📈 Line, area, bar, dan stacked

Keempat tipe memakai pasangan `labels` dan `series`. `labels` adalah urutan kategori/waktu; masing-masing `series` harus memiliki `name` dan array `data` dengan panjang dan urutan yang sama. `bar` membandingkan nilai antar label; `stacked` menjumlahkan kontribusi seluruh seri pada label yang sama; `area` mengisi bidang di bawah kurva; `line` mempertahankan garis saja. `smooth` memengaruhi garis/area, `legend` menampilkan daftar seri, sementara `prefix` dan `suffix` mengatur format angka pada grafik.

### 🍩 Donut dan tipe lanjutan

`donut` memakai `items` berisi `label`, `value`, dan opsional `color`. `totalLabel` memberi keterangan pada angka total. Mesin JS juga mendukung `radial`, `hbar`, serta `spark`; contoh `spark` paling mudah dilihat pada `x-ui.stat-card` di dashboard. Sebelum menggunakan tipe lanjutan, lihat struktur atribut tipe terkait di `resources/js/admin/charts.js`: komponen Blade tidak menyediakan prop khusus `value`, `label`, `kind`, atau `color` pada root untuk seluruh variasi mentah; beberapa variasi membutuhkan markup `data-chart` langsung seperti di halaman admin.

### 🧩 Aksesibilitas dan input data

Prop `aria` menjadi label pada SVG yang dihasilkan, sedangkan `summary` menjadi `<figcaption>` yang dapat dibaca tanpa menafsirkan grafik. Sertakan ringkasan yang menyatakan pola atau nilai penting, bukan hanya mengulang judul. Jangan menaruh data dari request secara mentah ke atribut JSON; siapkan dan validasi nilai di controller. Bila semua nilai kosong/nol, tampilkan state kosong alih-alih memaksakan visualisasi.

## 🔄 Data kosong dan render ulang

Untuk rentang tanpa nilai, tampilkan `<x-ui.table-state state="empty">` alih-alih grafik kosong. Grafis diinisialisasi saat halaman dimuat (`window.Charts.init(document)` melalui `App.initPage`). Jika data sudah berubah pada elemen grafik yang sama, perbarui `dataset` lalu panggil `window.Charts.draw(element)`. `Charts.init` melewati elemen yang sudah pernah diinisialisasi; jangan memanggilnya untuk berharap data lama tergambar ulang.

`x-ui.stat-card` memakai mesin grafik yang sama untuk sparkline. Detail prop dan contohnya ada di [tata letak & tampilan](/komponen/tampilan).
