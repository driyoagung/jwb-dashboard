# 📋 Tabel & status data

## 🧱 Tabel HTML

Belum ada komponen `<x-ui.table>`. Gunakan elemen `<table class="table">` di dalam pembungkus `overflow-x-auto`, seperti pada `resources/views/showcase/tables.blade.php`. Varian CSS: `table-striped`, `table-compact`, `table-bordered`.

```blade
<div class="card overflow-x-auto">
    <table class="table min-w-[600px]">
        <thead><tr><th>Nama</th><th>Status</th></tr></thead>
        <tbody>
            @foreach ($records as $record)
                <tr><td>{{ $record->title }}</td><td>{{ $record->status }}</td></tr>
            @endforeach
        </tbody>
    </table>
</div>
```

## 🔎 Tabel interaktif bawaan

`data-table` pada pembungkus mengaktifkan interaksi di `resources/js/admin/interactions.js`: pencarian `data-table-search`, filter select `data-table-filter` berdasarkan `data-status` tiap `<tr>`, sort lewat `<th data-sort>` (`data-sort="num"` untuk angka), paginasi di `data-table-pager`, keterangan `data-table-info`, dan tampilan kosong `data-table-empty`. `data-page-size` mengatur jumlah baris per halaman (default JS: 8). Checkbox `data-check-all` dan `data-check-row` menampilkan `data-bulk`/`data-bulk-count`. Lihat implementasi lengkap di `/components/tables` atau `/examples/records`.

```blade
<div class="card" data-table data-page-size="6">
    <input type="search" data-table-search aria-label="Cari entri" class="input" />
    <select data-table-filter class="select" aria-label="Filter status">
        <option value="">Semua</option><option value="aktif">Aktif</option>
    </select>
    <div class="overflow-x-auto">
        <table class="table"><thead><tr><th data-sort>Judul</th><th>Status</th></tr></thead>
            <tbody>
                @foreach ($records as $record)
                    <tr data-status="{{ $record->status }}"><td>{{ $record->title }}</td><td>{{ $record->status }}</td></tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div data-table-empty hidden>Tidak ada hasil.</div>
    <p data-table-info></p><div data-table-pager></div>
</div>
```

Tabel demo hanya memproses **baris yang sudah dirender di browser**. Jangan mencampur `data-table` ini dengan paginasi server dan berharap pencarian meliputi seluruh database; ikuti [tabel dari server](/integrasi/data) untuk data besar.

### ↕️ Pencarian, filter, dan urutan

Pencarian membandingkan teks semua sel kecuali sel terakhir (biasanya kolom aksi) dan tidak mengirim request. Filter membaca `data-status` pada `<tr>` dan mencocokkannya secara persis dengan nilai `<select>`; pastikan status dalam opsi dan baris menggunakan slug yang sama, contohnya `aktif`. Untuk angka, beri `data-sort="num"` pada `<th>` dan bila isi sel berupa format tampilan (misalnya `Rp 1.000`), pasang `data-value="1000"` pada `<td>` agar hasil urutan berdasarkan angka mentah. Teks sel lain diurutkan memakai locale `id`.

### ☑️ Seleksi baris dan paginasi

`data-check-all` memilih checkbox `data-check-row` pada baris yang terlihat; jumlah terpilih mengendalikan `data-bulk-count` dan `data-bulk`. Filter dan pergantian halaman menghapus seleksi. Tombol aksi bulk dalam showcase hanyalah contoh UI: untuk menghapus/mengekspor data nyata, tambahkan `value` ID pada checkbox, kumpulkan ID, lalu buat request ke route yang sesuai. Kontrol `data-table-pager` dibuat otomatis sebagai tombol browser, bukan tautan route Laravel.

## 🧭 Keadaan kosong, memuat, error {#keadaan-kosong-memuat-error}

`x-ui.table-state` menerima `state` (`empty`, `loading`, `filtered`, `error`), `title`, `description`, serta slot untuk tombol pemulihan. `loading` selalu menampilkan skeleton; state lain menampilkan teks bawaan yang dapat diganti.

| `state` | Arti | Kapan ditampilkan |
| --- | --- | --- |
| `loading` | Empat baris skeleton dengan `role="status"` | Saat permintaan data sedang berlangsung |
| `empty` (default) | Belum ada data sama sekali | Sebelum pengguna membuat entri pertama |
| `filtered` | Tidak ditemukan kecocokan | Setelah pencarian atau filter menghasilkan nol baris |
| `error` | Data gagal dimuat; `role="alert"` | Saat request gagal, sertakan aksi coba lagi pada slot |

`title` dan `description` mengganti teks default, tetapi pada state `loading` komponen tetap merender skeleton tanpa teks kustom/slot. Tombol pada slot hanya muncul saat slot tidak kosong dan state bukan `loading`.

```blade
<x-ui.table-state state="filtered" title="Tidak ada hasil">
    <a href="{{ route('examples.records.index') }}" class="btn btn-outline">Hapus filter</a>
</x-ui.table-state>
```

`x-ui.empty` adalah tampilan kosong umum dengan prop `icon`, `title`, `description` dan slot aksi. `x-ui.pagination` **belum digunakan di halaman showcase**; implementasinya memakai `$elements` tanpa membangunnya dari paginator, jadi jangan mengandalkannya langsung untuk output `$records->links()` sebelum disesuaikan. Untuk integrasi sekarang, gunakan `$records->links()` milik Laravel atau buat partial pagination sendiri.

### 🗃️ `x-ui.empty` {#x-ui-empty}

Gunakan untuk keadaan tanpa konten di luar konteks tabel, misalnya halaman pesanan pertama kali dibuka. `icon` default `file-text`; `title` default `Belum ada data`. Slot opsional dapat berisi tautan membuat data atau instruksi berikutnya. Komponen ini tidak membedakan gagal memuat dan filter tanpa hasil: untuk kasus itu pakai `x-ui.table-state`.

```blade
<x-ui.empty icon="package" title="Belum ada produk"
    description="Produk yang ditambahkan akan tampil di sini.">
    <a href="{{ route('products.create') }}" class="btn btn-primary">Tambah produk</a>
</x-ui.empty>
```

Nama route `products.create` di atas adalah contoh route aplikasi yang perlu Anda buat, bukan route bawaan starter kit.

### 📄 `x-ui.pagination` (belum siap pakai) {#x-ui-pagination-belum-siap-pakai}

Berkas komponennya menerima prop `paginator` dan memanggil `hasPages()`, `firstItem()`, `lastItem()`, `total()` dan URL sebelum/sesudah. Namun loop angka halamannya memakai `$elements` yang tidak didefinisikan di komponen tersebut. Saat mengembangkan komponen ini, bangun daftar elemen halaman dari paginator atau gunakan view pagination Laravel yang lengkap. Untuk `cursorPaginate()`, kontrak `total()`/`lastItem()` juga berbeda; contoh docs ini menggunakan `paginate()`.
