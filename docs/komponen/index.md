# 🧭 Peta komponen

Komponen di bawah berada di `resources/views/components/ui/`; tag `<x-ui.nama>` menunjuk file `nama.blade.php`. Beberapa pola showcase berupa HTML + kelas CSS dan atribut `data-*`, bukan komponen Blade tersendiri.

| Kebutuhan | Komponen | Referensi |
| --- | --- | --- |
| Kerangka halaman | `x-layouts.admin`, `x-layouts.guest`, `x-ui.page-header` | [Tampilan](/komponen/tampilan) |
| Konten & aksi | `x-ui.card`, `x-ui.stat-card`, `x-ui.button`, `x-ui.badge`, `x-ui.icon` | [Tampilan](/komponen/tampilan) |
| Form | `x-ui.field`, `x-ui.input`, `x-ui.select`, `x-ui.textarea`, `x-ui.switch` | [Form](/komponen/form) |
| Input lanjutan | `x-ui.combobox`, `x-ui.date-range`, `x-ui.file-preview` | [Form](/komponen/form) |
| Data & status | `x-ui.table-state`, `x-ui.empty`, `x-ui.pagination` | [Tabel](/komponen/tabel) |
| Visualisasi | `x-ui.chart`, `x-ui.stat-card` | [Grafik](/komponen/grafik) |
| Notifikasi & lapisan | `x-ui.alert`, `x-ui.modal`, `x-ui.drawer` | [Umpan balik](/komponen/umpan-balik) |

Sebagian markup halaman, misalnya tabel `.table`, menu dropdown `.menu`, tab, spinner, dan chip filter, diatur melalui CSS dan JavaScript tanpa wrapper `<x-ui.*>`. Lihat [interaksi JavaScript](/komponen/interaksi) untuk kontrak atributnya.

## 🧰 Indeks komponen UI

| Komponen | Untuk apa | Detail penggunaan |
| --- | --- | --- |
| `alert` | Pesan inline sukses/peringatan/kesalahan | [Alert](/komponen/alert) |
| `badge` | Label status tidak interaktif | [Badge](/komponen/badge) |
| `button` | Tombol aksi HTML | [Button](/komponen/button) |
| `card` | Kontainer dengan header/isi/footer | [Card](/komponen/card) |
| `chart` | Grafik SVG dari data PHP | [Chart](/komponen/chart) |
| `combobox` | Cari dan pilih opsi lokal | [Combobox](/komponen/combobox) |
| `date-range` | Preset dan input rentang tanggal | [Date range](/komponen/date-range) |
| `drawer` | Panel dialog samping | [Drawer](/komponen/drawer) |
| `filter-bar` | Form GET pencarian dan filter aktif | [Filter bar](/komponen/filter-bar) |
| `detail-list` | Pasangan label/nilai untuk halaman detail | [Detail list](/komponen/detail-list) |
| `timeline` | Riwayat peristiwa kronologis | [Timeline](/komponen/timeline) |
| `tabs` | Tab underline, pill, dan soft | [Tabs](/komponen/tabs) |
| `confirm-action` | Dialog konfirmasi dengan form POST/DELETE | [Confirm action](/komponen/confirm-action) |
| `empty` | Keadaan kosong umum | [Empty](/komponen/empty) |
| `field` | Label, petunjuk, dan error kontrol | [Field](/komponen/field) |
| `file-preview` | Area pilih/seret file & pratinjau lokal | [File preview](/komponen/file-preview) |
| `icon` | Ikon dari sprite publik | [Icon](/komponen/icon) |
| `input` | Input HTML termasuk ikon/addon | [Input](/komponen/input) |
| `modal` | Dialog tengah untuk tugas terfokus | [Modal](/komponen/modal) |
| `page-header` | Judul utama dan keterangan halaman | [Page header](/komponen/page-header) |
| `pagination` | Rancangan navigasi halaman (belum siap pakai) | [Pagination](/komponen/pagination) |
| `select` | Select dengan opsi dari array | [Select](/komponen/select) |
| `stat-card` | Angka, delta, ikon & sparkline | [Stat card](/komponen/stat-card) |
| `switch` | Checkbox berpenampilan toggle | [Switch](/komponen/switch) |
| `table-state` | Loading, empty, filtered, error | [Table state](/komponen/table-state) |
| `textarea` | Teks multi-baris dengan old/error | [Textarea](/komponen/textarea) |

::: info Katalog langsung
Dengan `ADMIN_SHOWCASE=true`, halaman `/components/cards`, `/components/forms`, `/components/tables`, `/components/charts`, dan halaman komponen lainnya menunjukkan hasil render di aplikasi Laravel.
:::

Komponen tambahan di `resources/views/components/admin/` khusus untuk shell admin (sidebar, header, customizer). Umumnya gunakan `<x-layouts.admin>` ketimbang memanggilnya satu per satu.

Contoh gabungan lima komponen baru tersedia pada `/components/data-patterns`; penjelasan alurnya ada di [pola data baru](/komponen/pola-data).
