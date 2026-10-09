# Kesiapan komponen untuk data nyata

Matriks ini membantu agent memilih komponen tanpa menganggap contoh interaktif sebagai backend siap pakai. Periksa kode komponen saat mengimplementasikan fitur.

| Komponen/pola | Siap dipakai | Batas yang harus ditangani |
| --- | --- | --- |
| `x-layouts.admin`, `x-ui.page-header/card/button/badge/icon` | Presentasi dashboard | Data, identitas, dan izin berasal dari proyek. Sidebar/header bawaan masih memiliki profil/notifikasi contoh. |
| `x-ui.field/input/select/textarea/switch` | Form Laravel dengan `name`, `old()`, error | Butuh route, Form Request, policy, dan penyimpanan. `x-ui.select :searchable="true"` memakai Choices.js pada opsi yang sudah dimuat. |
| `x-ui.filter-bar` | Form GET pencarian/filter | Filter dilakukan controller; komponen tidak mencari di database sendiri. |
| Tabel `.table` + `data-table` | Dataset kecil yang seluruh barisnya ada di DOM | Client-side saja. Lepas `data-table` untuk pagination/filter server. |
| `x-ui.table-state`, `x-ui.empty` | State kosong/filtered/loading/error | Tampilkan state berdasarkan hasil query atau request nyata. |
| `x-ui.chart`, `x-ui.stat-card` | Visualisasi dan KPI | Kirim agregasi valid dari controller; data statis dashboard saat ini hanya demo. |
| `x-ui.modal/drawer/confirm-action/tabs` | Interaksi UI | Endpoint dan otorisasi aksi tetap harus tersedia. |
| `x-ui.date-range`/Flatpickr | Pilihan tanggal dan preset | Validasi tanggal/rentang di server. |
| `x-ui.combobox` | Pilihan lokal sederhana | Belum memulihkan pilihan awal/`old()` untuk edit; perbaiki komponen yang ada sebelum memakai edit nyata. |
| `x-ui.file-preview` | Pratinjau lokal | Input belum punya `name`; validasi/penyimpanan file harus ditambah di server. |
| `x-ui.pagination` | Siap untuk `LengthAwarePaginator` dari `paginate()` | Tidak untuk `simplePaginate()`/`cursorPaginate()` karena memerlukan total dan nomor halaman. |
| `/login` | Session login nyata untuk user yang sudah ada | Belum ada registrasi/reset password; dashboard demo publik belum otomatis terlindungi. |

Jangan membuat komponen pengganti untuk mengatasi batas di tabel ini. Perbaiki/perluas komponen yang ada jika proyek memerlukannya, jaga API lama, dan tambahkan test yang relevan.
