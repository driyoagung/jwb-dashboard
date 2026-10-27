# Audit codebase POS

Tanggal: 17 Oktober 2026. Audit dilakukan sebelum implementasi POS.

## Stack dan struktur

- PHP CLI 8.4.16, Laravel terpasang 12.69.3 (`composer.json`: PHP `^8.2`, Laravel `^12.0`). PHPUnit 11, Laravel Pint, Vite 6, Tailwind CSS 4, Blade, Inter.
- `package.json` memasang Choices.js dan Flatpickr. Grafik menggunakan renderer SVG lokal `resources/js/admin/charts.js`, bukan Chart.js. `resources/js/app.js` memuat modul admin dan Axios.
- `routes/web.php` memuat halaman demo, login session, dan CRUD referensi. Layout di `resources/views/components/layouts/`; shell di `components/admin/`; komponen anonim di `components/ui/`; CSS di `resources/css/app.css`; ikon sprite di `public/icons.svg`.
- `.env.example` memakai SQLite untuk pengembangan, sementara PRD menargetkan MySQL/InnoDB. Migrasi harus kompatibel MySQL dan pengujian lokal SQLite. `phpunit.xml` memakai SQLite in-memory.
- Status awal repository: `package-lock.json` sudah berubah dan PRD belum terlacak; keduanya bukan hasil implementasi ini.

## Auth dan akses

`AuthController` memakai session Laravel dan throttle login. `User` belum mempunyai role/status. `ReferenceRecordPolicy` membatasi data milik user; route referensi memakai `auth`. Dashboard, analytics, dan settings demo belum memakai `auth`. Tidak ada paket RBAC. POS memerlukan role `admin`/`cashier`, status aktif, gate/policy, scope data kasir, dan proteksi route server. Navigasi `config/kenanga.php` perlu disaring berdasarkan izin.

## Inventaris komponen Blade nyata

Semua file berikut berada di `resources/views/components/ui/`. API di bawah berasal dari `@props` aktual; atribut HTML tambahan diteruskan melalui `$attributes`. Contoh render tersedia di `resources/views/showcase/` dan dokumentasi `docs/komponen/`.

| Tag | Props utama | Slot/contoh pemakaian |
| --- | --- | --- |
| `<x-ui.alert>` | `variant`, `title`, `solid` | isi pesan; `variant="success"` |
| `<x-ui.badge>` | `variant`, `pill`, `dot`, `icon` | label status |
| `<x-ui.button>` | `variant`, `size`, `type` | teks/ikon; selalu elemen `button` |
| `<x-ui.card>` | `title`, `description` | isi; slot `header`/`footer` |
| `<x-ui.chart>` | `type`, `labels`, `series`, `items`, `values`, `height`, `aria`, `summary`, `legend`, `smooth`, `prefix`, `suffix`, `size`, `totalLabel` | data grafik dari server |
| `<x-ui.combobox>` | `id`, `label`, `options`, `name`, `placeholder` | pilihan lokal |
| `<x-ui.confirm-action>` | `id`, `title`, `description`, `action`, `method`, `label`, `tone` | isi dialog konfirmasi |
| `<x-ui.date-range>` | `id`, `label`, `fromName`, `toName`, `from`, `to` | filter rentang tanggal |
| `<x-ui.detail-list>` | `title`, `items`, `columns` | isi detail tambahan |
| `<x-ui.drawer>` | `id`, `title`, `description` | isi dan footer |
| `<x-ui.empty>` | `icon`, `title`, `description` | aksi opsional |
| `<x-ui.field>` | `label`, `name`, `help`, `required`, `class` | kontrol form |
| `<x-ui.file-preview>` | `id`, `label`, `accept`, `maxMb` | pratinjau, input belum bernama |
| `<x-ui.filter-bar>` | `action`, `searchName`, `search`, `placeholder`, `resetUrl` | filter GET dan slot `active` |
| `<x-ui.icon>` | `name`, `class`, `useClass` | simbol dari `public/icons.svg` |
| `<x-ui.input>` | `name`, `value`, `type`, `invalid`, `addon`, `icon` | input form |
| `<x-ui.modal>` | `id`, `title`, `description`, `icon`, `tone`, `size` | isi dan slot `footer` |
| `<x-ui.page-header>` | `title`, `description` | judul halaman |
| `<x-ui.pagination>` | `paginator` | `LengthAwarePaginator` dari `paginate()` |
| `<x-ui.select>` | `name`, `value`, `placeholder`, `invalid`, `options`, `includeBlank`, `searchable` | `:searchable="true"` memakai Choices.js |
| `<x-ui.stat-card>` | `title`, `value`, `icon`, `tile`, `tone`, `direction`, `delta`, `note`, `spark` | KPI |
| `<x-ui.switch>` | `name`, `label`, `checked`, `value`, `hint` | toggle boolean |
| `<x-ui.table-state>` | `state`, `title`, `description` | status tabel |
| `<x-ui.tabs>` | `id`, `tabs`, `variant`, `active` | panel tab |
| `<x-ui.textarea>` | `name`, `value`, `rows`, `invalid` | isi teks |
| `<x-ui.timeline>` | `items` | jejak aktivitas |

Layout `<x-layouts.admin title="..." group="...">` dan `<x-layouts.guest>` berada di `components/layouts/`. Pola tabel resmi adalah `<table class="table">` di dalam `overflow-x-auto`; tidak ada `<x-ui.table>`. Menu dropdown memakai `.menu` dan atribut `data-dropdown`.

## JavaScript dan contoh nyata

`interactions.js` menyediakan `data-table`, modal, dropdown, tabs, toast (`window.App.toast`) dan inisialisasi ulang `window.App.initPage`. `advanced-inputs.js` menangani combobox/date range/file preview. `enhanced-controls.js` menginisialisasi Choices.js/Flatpickr. `data-table` hanya memproses baris DOM saat ini; untuk daftar POS yang besar gunakan `x-ui.filter-bar`, tabel `.table`, dan `x-ui.pagination` seperti `resources/views/reference/records/index.blade.php`. Showcase `tables`, `cards`, `forms`, `feedback`, `charts`, dan `data-patterns` menjadi referensi komposisi. `data-demo-form` tidak boleh dipakai pada aksi tersimpan.

## Pemetaan POS ke Kenanga

| Modul | Komponen/pola |
| --- | --- |
| Dashboard/laporan | layout admin, page-header, stat-card, card, chart, tabel `.table`, badge |
| Kategori/satuan/produk/pengguna | filter-bar, card, field, input/select/textarea/switch, tabel `.table`, table-state, pagination, confirm-action |
| Stok/riwayat stok | card, badge, tabel `.table`, pagination, modal/confirm-action |
| Kasir/draft | card, input/select, alert, modal, button, tabel `.table` |
| Transaksi/struk | card, detail-list, tabel `.table`, badge; halaman cetak minimal |
| Pengaturan | card, field, input/textarea, button, alert |

## Gap, risiko, keputusan

- Role/status belum ada: tambahkan kolom dan aturan akses minimal; cegah admin aktif terakhir dinonaktifkan.
- Profil/header dan sebagian aksi demo masih statis: hubungkan identitas dan hapus affordance palsu pada halaman POS.
- Grafik PRD menyebut Chart.js bila tersedia; paket tidak terpasang. Gunakan chart Kenanga yang ada.
- `data-table` reaktif cocok untuk dataset kecil yang seluruh barisnya dimuat; daftar POS memakai query/pagination server untuk konsistensi dan akses role.
- `x-ui.combobox` belum memulihkan pilihan edit. `x-ui.file-preview` kini mendukung `name`; gambar produk memakai upload PNG/JPG tervalidasi pada disk publik.
- SQLite tidak menjamin perilaku locking MySQL. Uji concurrency checkout pada MySQL sebelum produksi.

Urutan kerja: fondasi data/role → master data → inventori → checkout → draft/riwayat → dashboard/laporan → hardening dan UAT. Detail ada di `implementation-plan.md`.
