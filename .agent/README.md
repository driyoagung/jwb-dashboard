# Panduan AI agent — Kenanga Admin

Starter kit ini adalah Laravel 12 + Blade, Tailwind CSS 4, Vite, dan JavaScript modular. Saat membangun dashboard untuk role apa pun, pertahankan bahasa visual dan komposisi Kenanga Admin.

## Aturan wajib

1. Gunakan komponen dan pola yang sudah tersedia. Halaman dashboard dimulai dari `<x-layouts.admin>`; halaman publik/login dari `<x-layouts.guest>`. Rakit isi dengan `<x-ui.*>`. Jangan membuat komponen UI baru, menyalin markup internal komponen, atau merancang ulang tombol, card, form, tabel, modal, grafik, dan navigasi secara manual.
2. Beberapa pola resmi belum punya wrapper Blade: tabel `.table`, dropdown `.menu`, dan kontrol berbasis atribut `data-*`. Ikuti markup dan kelas yang sudah dicontohkan di showcase. Ini tetap penggunaan template, bukan alasan untuk mendesain pola baru.
3. Pakai CSS `resources/css/app.css`, ikon sprite `public/icons.svg` lewat `<x-ui.icon>`, dan JS di `resources/js/admin/`. Untuk select searchable pakai `<x-ui.select :searchable="true">` (Choices.js); untuk tanggal pakai `<x-ui.date-range>` atau `data-date-picker` (Flatpickr). Jangan menambah library UI/JS untuk fungsi yang telah tersedia.
4. Pertahankan shell dan komponen presentasi ketika menyambungkan backend. Data `config('kenanga.demo.*')`, banyak `Route::view`, dan beberapa aksi JS masih demo frontend. Login serta CRUD referensi sudah tersambung; dashboard/analytics/settings belum otomatis terlindungi.
5. Sebelum memakai komponen, periksa `@props`, slot, dan `$attributes` pada file Blade aslinya. Jangan mengarang prop atau kemampuan. Cari contoh di `resources/views/showcase/` dan `docs/komponen/`.

## Baca sesuai tugas

Buka [peta dokumentasi agent](docs/README.md) untuk memilih panduan fondasi, implementasi, atau standar kerja. Referensi komponen dan integrasi yang lebih rinci tersedia di `docs/` (jalankan `npm run docs:dev` untuk VitePress).

Jika kebutuhan benar-benar belum terlayani, periksa seluruh komponen dan showcase dahulu. Jelaskan celahnya kepada pemilik proyek sebelum menambah primitive/komponen UI baru.
