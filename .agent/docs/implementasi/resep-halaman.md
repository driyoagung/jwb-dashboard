# Resep halaman Kenanga

Mulai dari view terdekat dan API komponen aslinya. File di bawah adalah resep komposisi, bukan izin membuat komponen UI baru.

| Halaman | Susunan yang dianjurkan | Contoh |
| --- | --- | --- |
| Ringkasan dashboard | `x-layouts.admin` → `x-ui.page-header` → `x-ui.stat-card` → `x-ui.card`/`x-ui.chart`; agregasi di controller | `resources/views/admin/dashboard.blade.php`, `admin/analytics.blade.php` |
| Daftar data | `x-ui.filter-bar` → `x-ui.card` → tabel `.table` → `x-ui.table-state`/paginator | `resources/views/reference/records/index.blade.php` |
| Tambah/edit | `x-ui.page-header` → form Laravel dengan `x-ui.field` + `x-ui.input/select/textarea/switch` → `x-ui.button` | `resources/views/reference/records/form.blade.php` |
| Detail | `x-ui.page-header` → `x-ui.detail-list`/`x-ui.card`/`x-ui.timeline` → aksi `x-ui.confirm-action` | `resources/views/reference/records/show.blade.php` |
| Login/publik | `x-layouts.guest` + kontrol `x-ui.*` | `resources/views/auth/login.blade.php` |

## Keputusan cepat

- Tabel browser `data-table` hanya untuk semua baris yang sudah dirender. Tabel database besar memakai filter GET dan pagination server seperti referensi CRUD. Baca [data besar](data-besar.md).
- Gunakan `x-ui.select :searchable="true"` untuk pilihan lokal yang jumlahnya wajar (Choices.js); `x-ui.date-range` untuk tanggal (Flatpickr). Validasi server tetap wajib.
- Gunakan `x-ui.table-state` untuk kosong/filtered/error/loading, `x-ui.alert` untuk pesan inline, dan modal/confirm-action bawaan untuk konfirmasi.
- Tautan navigasi bisa memakai kelas `.btn` resmi sebab `x-ui.button` merender `<button>`. Untuk tabel gunakan markup `.table` resmi sebab belum ada `x-ui.table`.
- Jangan menampilkan aksi yang belum berfungsi sebagai aksi nyata. Form demo `data-demo-form` dan toast simulasi harus dilepas ketika endpoint sudah tersambung.

Detail prop/slot tetap dibaca dari `resources/views/components/ui/` dan `docs/komponen/`.
