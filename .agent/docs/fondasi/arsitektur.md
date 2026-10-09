# Arsitektur codebase

## Stack dan lokasi

| Area | Lokasi | Peran |
| --- | --- | --- |
| Route | `routes/web.php` | Dashboard, analytics, settings, login, showcase, dan contoh halaman; mayoritas `Route::view`. |
| Layout | `resources/views/components/layouts/` | `admin` memuat sidebar, header, footer, customizer, toast; `guest` untuk publik; `head` memuat aset Vite dan preferensi tema. |
| Shell admin | `resources/views/components/admin/` | Navigasi, header, footer, customizer. Sudah dipanggil layout. |
| UI | `resources/views/components/ui/` | Anonymous Blade components: `nama.blade.php` menjadi `<x-ui.nama>`. |
| Halaman | `resources/views/admin/`, `auth/`, `showcase/`, `examples/records/` | Contoh komposisi dan alur daftar/detail/create/edit. |
| Konfigurasi | `config/kenanga.php` | Brand, grup navigasi, aksen, data demo. |
| Aset | `resources/css/app.css`, `resources/js/app.js`, `resources/js/admin/`, `public/icons.svg` | Tema, UI, perilaku interaktif, dan ikon lokal. |
| Template asal | `kenanga-admin-template/` | Sumber historis HTML; implementasi Laravel berada di `resources/`. |
| Dokumentasi | `docs/` | Panduan VitePress komponen, halaman, tema, dan integrasi backend. |

`composer.json` memakai PHP `^8.2` dan Laravel `^12.0`. `package.json` memuat Vite, Tailwind CSS 4, Inter, Choices.js, Flatpickr, dan VitePress. Tidak ada framework frontend React/Vue pada aplikasi Laravel ini.

## Alur render dan navigasi

Route mengirim data ke view Blade. View dibungkus `<x-layouts.admin title="..." group="...">`; layout merender `<x-layouts.head>` dan shell admin, lalu isi view pada `#page-root`. `title` dipakai untuk judul browser/breadcrumb dan `group` untuk breadcrumb. Aset dimuat oleh `@vite(['resources/css/app.css', 'resources/js/app.js'])`. Preferensi tema, aksen, radius, dan sidebar disimpan di `localStorage`.

Sidebar membaca `config('kenanga.navigation')`. Item memakai named route (`route`) dan pola penanda aktif (`active`); submenu memakai `children`. Route yang dirujuk menu harus terdaftar. `ADMIN_SHOWCASE=false` menutup route dan menu showcase/contoh; periksa tautan ke sana saat mematikannya. Untuk role lain, sesuaikan menu dan hak akses server, sambil tetap memakai layout Kenanga.

## Batas starter kit saat ini

- Dashboard, analytics, settings, showcase, dan `/examples/records` masih berisi data contoh. `/login` sudah memakai session; `/reference/records` adalah CRUD tersimpan dengan middleware `auth`. Route dashboard/analytics/settings belum memakai middleware `auth`.
- `data-demo-form` mencegat submit dan hanya membuat toast; `data-load` mensimulasikan loading. Lepaskan atribut demo saat menghubungkan aksi sungguhan.
- `data-table` hanya memproses baris yang sudah dirender di browser. Untuk dataset besar gunakan filter dan paginasi server tanpa `data-table` pada dataset yang sama.
- `x-ui.pagination` sudah memakai `LengthAwarePaginator` dan `UrlWindow` untuk nomor halaman; gunakan pada tabel server-side agar tampilan tetap Kenanga.
- `x-ui.combobox` belum mengisi pilihan awal/`old()` untuk edit; `x-ui.file-preview` belum memberi `name` pada input. Baca `docs/komponen/form.md` sebelum memakainya pada form nyata.

Rujukan: `README.md`, `docs/panduan/struktur.md`, `docs/panduan/halaman.md`, dan `docs/integrasi/index.md`.
