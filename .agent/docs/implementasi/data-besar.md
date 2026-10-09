# Data besar dan tabel server-side

## Pilih strategi sesuai ukuran data

| Situasi | Mekanisme | UI Kenanga |
| --- | --- | --- |
| Dataset kecil yang seluruh barisnya memang dirender | `data-table` bawaan: cari, filter, sort, paginasi di browser | Pola `.table` dari `resources/views/showcase/tables.blade.php` |
| Dataset besar atau data sensitif terhadap role | Filter, sort, dan paginasi di database melalui route/controller Laravel | `x-ui.filter-bar`, `x-ui.select :searchable="true"` bila pilihan kecil, `.table`, `x-ui.table-state` |
| Dataset sangat besar, live refresh, atau UX tanpa reload diperlukan | Endpoint server dengan parameter tervalidasi; render kembali hasil memakai struktur tabel Kenanga | Pertahankan komponen/pola Kenanga; tambah JS hanya untuk request/replace hasil yang memang diperlukan |

`resources/js/admin/interactions.js` menginisialisasi `data-table` dari seluruh `<tr>` yang ada ketika halaman dimuat. Ia **tidak** meminta data ke server. Jangan memasang `data-table` pada daftar yang dipaginasi server: pencarian/sort browser akan berlaku hanya untuk satu halaman. Belum ada library server-side DataTables di `package.json`; jangan menganggapnya tersedia atau menambah dependensi hanya karena tabel besar. Mulai dari form GET + paginator Laravel.

## Kontrak daftar server-side

1. Validasi parameter GET (misalnya `q`, `status`, `sort`, `direction`, `per_page`) melalui Form Request ketika kompleks. Batasi panjang pencarian, nilai status, pilihan sort, dan ukuran halaman. Gunakan allowlist kolom untuk `orderBy`; jangan masukkan nama kolom mentah dari request.
2. Scope query sesuai role/tenant **sebelum** filter dan pagination. Eager load relasi yang terlihat dan gunakan `withCount()` untuk jumlah. Pilih kolom yang dibutuhkan; sertakan key relasi.
3. Sort di database dengan urutan yang stabil (misalnya `latest('created_at')` disertai `id` bila timestamp dapat sama). `paginate(...)->withQueryString()` untuk navigasi halaman dan mempertahankan filter. Untuk halaman sangat dalam saat total angka tidak diperlukan, pertimbangkan `cursorPaginate()` dengan urutan unik/stabil.
4. Render filter melalui `x-ui.filter-bar` dan kontrol Kenanga. `x-ui.filter-bar` adalah form GET; Choices.js pada `x-ui.select :searchable="true"` hanya mencari opsi yang sudah dimuat di browser, **bukan** remote search seluruh database.
5. Render baris memakai tabel `.table` resmi dan status `x-ui.table-state state="empty"` atau `state="filtered"`. Gunakan `<x-ui.pagination :paginator="$paginator" />` dengan hasil `paginate()` agar navigasi server tetap memakai gaya Kenanga.
6. Uji bahwa pencarian, filter, sort, jumlah per halaman, dan pembatasan role berlaku pada seluruh dataset; uji juga query saat hasil kosong.

Contoh filter dan query ringkas:

```blade
<x-ui.filter-bar :action="route('admin.products.index')"
    :reset-url="route('admin.products.index')">
    <x-ui.select name="status" placeholder="Semua status"
        :value="request('status')"
        :options="['active' => 'Aktif', 'draft' => 'Draf']" />
</x-ui.filter-bar>
```

```php
$allowedSorts = ['name', 'created_at'];
$sort = in_array($data['sort'] ?? '', $allowedSorts, true)
    ? $data['sort']
    : 'created_at';
$direction = ($data['direction'] ?? 'desc') === 'asc' ? 'asc' : 'desc';

$products = Product::query()
    ->where('workspace_id', $workspace->id)
    ->when($data['q'] ?? null, fn ($query, $q) => $query->where('name', 'like', '%'.$q.'%'))
    ->when($data['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
    ->orderBy($sort, $direction)
    ->orderBy('id')
    ->paginate(25)
    ->withQueryString();
```

`$data` pada contoh berasal dari `$request->validated()`; `Product`, `workspace_id`, dan route adalah ilustrasi. Untuk pencarian teks berskala besar, rancang indeks/strategi pencarian sesuai database; pola `LIKE '%...%'` hanyalah titik awal dan dapat lambat pada tabel besar.

## Ekspor, grafik, dan input banyak pilihan

- Ekspor dan pemrosesan massal memakai `chunkById()`/`lazyById()` atau cursor sesuai kebutuhan, lalu queue untuk pekerjaan lama. Jangan `Model::all()` di dalam request ekspor. Untuk transformasi yang harus memicu model event per baris, proses per model dengan batch terukur; untuk update sederhana yang tidak memerlukan event, gunakan operasi set-based.
- Agregasi grafik dilakukan di database, bukan membaca seluruh record ke PHP. Kirim `labels`/`series`/`items` ke `x-ui.chart`. Batasi rentang tanggal, sertakan periode bernilai nol bila grafik tren memerlukannya, dan tampilkan state kosong dari komponen Kenanga.
- `x-ui.date-range` memakai Flatpickr untuk input tanggal dan preset, tetapi urutan/tanggal tetap harus divalidasi di server. Untuk filter status/kategori beropsi wajar, `x-ui.select :searchable="true"` memakai Choices.js. Jangan memuat ribuan opsi ke dalam Choices.js; bila perlu pencarian remote, rancang endpoint terotorisasi dan perluasan komponen/pola yang sudah ada bersama pemilik proyek.
- Untuk Ajax, gunakan route JSON yang terotorisasi dan parameter sama dengan daftar server-side. Jaga tabel tetap berstruktur `.table`; jangan mengimpor framework atau library tabel baru untuk menduplikasi perilaku Kenanga. `window.App.initPage(container)` hanya memasang perilaku bawaan pada elemen baru dan tidak menjadikan `data-table` server-side.

Baca juga `docs/integrasi/data.md`, `docs/komponen/tabel.md`, `docs/komponen/filter-bar.md`, dan `docs/komponen/chart.md`.
