# 🚀 Instalasi & menjalankan

Kenanga Admin adalah starter kit frontend Laravel 12 (PHP 8.2+), Blade component, Tailwind CSS 4, dan Vite. Anda membutuhkan Composer, Node.js, npm, serta PHP beserta ekstensi yang diperlukan Laravel.

Select yang dapat dicari menggunakan Choices.js dan kalender tanggal menggunakan Flatpickr, keduanya terpasang lewat `npm install` tanpa CDN.

## 🖥️ Jalankan dashboard

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate
```

Di Windows, gunakan `copy .env.example .env` sebagai pengganti `cp`. Jalankan server Laravel dan server aset di terminal terpisah:

```bash
php artisan serve
```

```bash
npm run dev
```

Lalu buka `/dashboard` pada alamat Laravel yang ditampilkan di terminal. Konfigurasi bawaan `.env.example` menggunakan SQLite; migrasi menyiapkan tabel bawaan Laravel, **bukan** data dashboard. Jika ingin menjalankan server, Vite, worker, dan pemantau log sekaligus, tersedia `composer dev`.

## 📚 Jalankan dokumentasi

Dokumentasi VitePress berjalan terpisah dari aplikasi Laravel:

```bash
npm run docs:dev
```

Buka URL lokal yang dicetak VitePress. Untuk membuat dan melihat hasil statis:

```bash
npm run docs:build
npm run docs:preview
```

Build aplikasi dan dokumentasi adalah dua perintah terpisah: `npm run build` membangun aset Laravel, sedangkan `npm run docs:build` membangun situs dokumentasi. Hasil dokumentasi berada di `docs/.vitepress/dist`.

## 🗺️ Halaman yang tersedia

| URL | Kegunaan |
| --- | --- |
| `/dashboard`, `/analytics`, `/settings` | Halaman utama admin |
| `/components/*` | Showcase komponen Blade dan pola HTML/CSS |
| `/components/data-patterns` | Filter bar, detail list, timeline, tabs, konfirmasi aksi |
| `/examples/records/*` | Contoh daftar, detail, tambah, dan edit entri |
| `/login`, `/demo/404` | Login session dan halaman kesalahan |
| `/reference/records/*` | CRUD tersimpan khusus user login; tersedia saat `ADMIN_SHOWCASE=true` |

Showcase dan contoh entri hanya tersedia saat `ADMIN_SHOWCASE=true`. Detail route ada di `routes/web.php` dan dijelaskan dalam [halaman & navigasi](/panduan/halaman).

## 🛠️ Saat siap membangun aplikasi

Mulai dari [membuat halaman Blade](/panduan/halaman), [memilih komponen](/komponen/), lalu [menghubungkan data ke Laravel](/integrasi/). Jangan mengandalkan data demo untuk persistensi.
