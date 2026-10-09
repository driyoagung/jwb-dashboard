# Peta komponen dan JavaScript

File `resources/views/components/ui/nama.blade.php` dipakai sebagai `<x-ui.nama>`. API komponen mengikuti `@props` pada file tersebut. Referensi tiap komponen ada di `docs/komponen/nama.md` dan indeks `docs/komponen/index.md`. Lihat hasil render di `/components/*` ketika `ADMIN_SHOWCASE=true`.

| Kebutuhan | Komponen atau pola resmi | Referensi view |
| --- | --- | --- |
| Halaman admin/publik | `x-layouts.admin`, `x-layouts.guest`, `x-ui.page-header` | `resources/views/admin/`, `auth/login.blade.php` |
| Konten/ringkasan | `x-ui.card`, `x-ui.stat-card`, `x-ui.detail-list`, `x-ui.timeline` | `showcase/cards.blade.php`, `showcase/data-patterns.blade.php` |
| Aksi/status | `x-ui.button`, `x-ui.badge`, `x-ui.icon`, `x-ui.alert` | `showcase/buttons.blade.php`, `showcase/feedback.blade.php` |
| Form | `x-ui.field`, `x-ui.input`, `x-ui.select`, `x-ui.textarea`, `x-ui.switch` | `showcase/forms.blade.php`, `components/examples/record-form.blade.php` |
| Input/filter | `x-ui.filter-bar`, `x-ui.combobox`, `x-ui.date-range`, `x-ui.file-preview` | `showcase/filters.blade.php` |
| Daftar/state | Tabel `.table`, `x-ui.table-state`, `x-ui.empty` | `showcase/tables.blade.php`, `showcase/table-states.blade.php`, `examples/records/index.blade.php` |
| Grafik | `x-ui.chart`, `x-ui.stat-card` | `showcase/charts.blade.php`, `admin/analytics.blade.php` |
| Lapisan/navigasi | `x-ui.modal`, `x-ui.drawer`, `x-ui.confirm-action`, `x-ui.tabs` | `showcase/feedback.blade.php`, `showcase/data-patterns.blade.php` |

Belum ada `<x-ui.table>`: HTML `<table class="table">` dalam `overflow-x-auto` adalah pola resmi. Halaman lama kadang memakai kelas `.card`, `.btn`, `.badge` langsung. Pada halaman baru, pilih komponen Blade bila tersedia; gunakan kelas resmi untuk bagian tanpa wrapper. Tautan bergaya tombol dapat memakai kelas `.btn` karena `x-ui.button` selalu merender `<button>`.

## JavaScript yang tersedia

`resources/js/app.js` mengimpor `bootstrap.js`, `admin/charts.js`, `admin/interactions.js`, `admin/advanced-inputs.js`, dan `admin/enhanced-controls.js`. Jangan menulis ulang handler yang sudah ada.

| Perilaku | Cara memakai | Implementasi |
| --- | --- | --- |
| Select searchable | `<x-ui.select :searchable="true" ... />` memberi `data-enhanced-select` | Choices.js di `enhanced-controls.js` |
| Tanggal | `data-date-picker` atau `<x-ui.date-range>` | Flatpickr locale Indonesia, nilai `Y-m-d` |
| Grafik | `<x-ui.chart type="..." :labels="..." :series="..." />` | SVG di `charts.js` |
| Modal/drawer | Komponen terkait + `data-modal-open="#id"`/`data-modal-close` | `interactions.js` |
| Toast/dropdown/tab | `data-toast`, `data-dropdown`, role tab dan ARIA | `interactions.js` |
| Tabel client-side | `data-table`, `data-table-search`, `data-table-filter`, `data-sort`, `data-table-pager` | `interactions.js`; hanya baris yang sudah dirender |
| Combobox/date range/file preview | Komponen dengan atribut `data-*` bawaannya | `advanced-inputs.js` |

Kontrak lengkap ada di `docs/komponen/interaksi.md` dan `docs/komponen/tabel.md`. Untuk konten yang disisipkan setelah page load, `window.App.initPage(container)` menginisialisasi grafik/tabel; `window.EnhancedControls.init(container)` menginisialisasi Choices.js/Flatpickr. `advanced-inputs.js` melakukan query awal sekali sehingga komponen lanjutan yang disisipkan dinamis memerlukan penanganan tambahan. Halaman Blade biasa memuat ulang halaman dan tidak memerlukan panggilan manual.

Jangan mengimpor Choices.js atau Flatpickr lagi per halaman, atau memakai `kenanga-admin-template/dist` sebagai aset runtime Laravel. Ikon harus memakai simbol yang tersedia dalam `public/icons.svg`; lihat `docs/komponen/icon.md`.
