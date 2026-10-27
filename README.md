# Griyo POS

Griyo POS adalah aplikasi kasir, inventori, dan laporan berbasis Laravel 12 dan Blade. Antarmukanya memakai komponen Kenanga Admin yang sudah tersedia, Vite, Tailwind CSS 4, dan JavaScript modular.

Panduan lengkap tersedia di situs VitePress dalam `docs/`: jalankan `npm run docs:dev` setelah `npm install`. Build dokumentasi dengan `npm run docs:build`. Dokumentasi mencakup referensi komponen `<x-ui.*>` dan cara menghubungkan halaman demo ke backend Laravel.

## Menjalankan proyek

```bash
composer install
npm install
copy .env.example .env
php artisan key:generate
php artisan migrate
php artisan storage:link
php artisan pos:create-admin
npm run dev
php artisan serve
```

Untuk build produksi:

```bash
npm run build
php artisan optimize
```

Untuk produksi, konfigurasi `DB_CONNECTION=mysql` beserta variabel `DB_*` agar memakai MySQL/InnoDB. SQLite dapat dipakai saat pengembangan dan pengujian. Perintah `php artisan pos:create-admin` meminta nama, email, dan kata sandi secara interaktif. Akun seed contoh hanya dibuat pada lingkungan local/testing.

## Griyo POS

| Halaman | Akses |
| --- | --- |
| `/dashboard`, `/cashier`, `/drafts`, `/transactions` | Admin dan kasir aktif |
| `/manage/categories`, `/manage/units`, `/manage/products` | Admin aktif |
| `/manage/stock`, `/manage/stock/movements` | Admin aktif |
| `/manage/reports/sales`, `/manage/reports/products`, `/manage/reports/stock`, `/manage/reports/drafts` | Admin aktif |
| `/manage/users`, `/manage/settings` | Admin aktif |

Admin dapat melihat semua transaksi dan draft; kasir hanya data miliknya. Draft aktif tidak mengurangi stok. Checkout mengunci produk, memvalidasi harga dan stok ulang, lalu membuat transaksi, item snapshot, serta movement secara atomik. Pembayaran transfer dan QRIS dicatat manual; Midtrans diperiksa melalui status pembayaran. Struk hanya tersedia untuk transaksi selesai; laporan dapat diekspor sebagai CSV.

### Scan barcode di kasir

Barcode produk disimpan pada kolom `products.barcode` yang unik dan opsional. Admin dapat mengisi barcode saat membuat atau mengubah produk lewat scanner USB/Bluetooth, tombol **Scan dengan kamera**, atau ketikan; seeder menyediakan barcode EAN-13 valid untuk setiap produk contoh. Di kasir, arahkan scanner USB/Bluetooth ke kolom **Scan barcode** lalu scan: tombol Enter dari scanner langsung menambah produk. Tombol **Scan kamera** otomatis membaca barcode dan menambahkan produk ke keranjang. Pada HTTPS atau localhost, browser membuka video setelah izin kamera diberikan dan otomatis membaca barcode yang terlihat tanpa tombol foto. Browser tidak mengizinkan video kamera live pada alamat HTTP biasa seperti `http://pos-jagat.test`; gunakan `http://localhost` di komputer yang menjalankan POS atau HTTPS untuk perangkat lain. Scanner USB/Bluetooth tetap dapat dipakai pada HTTP biasa. Pencarian barcode dilakukan tepat pada produk aktif, sedangkan produk dengan stok nol tidak bisa ditambahkan ke keranjang.

SKU produk baru dibuat otomatis saat disimpan dan tidak dapat diubah melalui form. Hapus menghilangkan produk dari katalog, kasir, dan daftar stok. Produk dengan riwayat transaksi atau pergerakan stok dihapus secara lunak supaya riwayat tetap utuh; barcode dibebaskan agar dapat dipakai produk baru. Produk tanpa riwayat dihapus permanen. Jalankan `php artisan migrate` setelah memperbarui aplikasi untuk menambahkan kolom penghapusan lunak.

Pesan singkat aplikasi menggunakan toast SweetAlert2 di kanan atas, termasuk hasil scan, flash sukses, dan kesalahan validasi.

### Pembayaran Midtrans Sandbox

Setelah membuat akun Midtrans Sandbox, isi `MIDTRANS_SERVER_KEY` dan `MIDTRANS_CLIENT_KEY` di `.env`; biarkan `MIDTRANS_IS_PRODUCTION=false`. Jalankan `php artisan config:clear` setelah mengubah konfigurasi. Server key dipakai server untuk membuat sesi pembayaran dan memeriksa status; jangan menaruhnya di JavaScript. Client key dipakai `snap.js` untuk menampilkan Snap popup di halaman kasir.

Tekan **Bayar** di kasir untuk membuka modal pilihan metode berbentuk kartu. Pilih **Midtrans**, lalu tekan **Proses pembayaran**; Snap popup muncul di halaman yang sama. POS membuat transaksi **Menunggu pembayaran** dan menahan stok. Callback popup meminta pembaruan status dari server, kemudian membuka detail transaksi. Dari detail transaksi, popup dapat dibuka lagi dan tombol **Cek pembayaran** tersedia bila status masih menunggu. Tombol tersebut meminta status terbaru langsung ke Midtrans melalui server, tanpa webhook. Status `settlement` atau `capture` yang diterima menuntaskan transaksi, menampilkan struk, dan mengirim notifikasi. Status gagal atau kedaluwarsa mengembalikan stok serta mengaktifkan lagi draft yang belum kedaluwarsa. Ketika Midtrans belum memiliki status, transaksi tetap menunggu.

Input angka bulat di halaman POS memakai pemisah ribuan saat diketik, misalnya `50000` ditampilkan sebagai `50.000`; nilai yang dikirim ke server tetap angka tanpa titik. Persentase pajak mempertahankan input desimalnya.

Pada lingkungan lokal, pemeriksaan dilakukan dengan tombol tersebut. Untuk produksi yang perlu memperbarui status secara otomatis meski halaman tidak dibuka, siapkan notifikasi server Midtrans atau proses pemeriksaan berkala melalui koneksi publik yang aman.

Gambar produk bersifat opsional untuk produk yang dibuat admin. Admin dapat mengunggah PNG/JPG hingga 5 MB; gambar disimpan di disk `public` dan ditampilkan pada daftar produk serta kartu kasir. Produk contoh dari seeder otomatis memakai 15 foto WebP di `database/seeders/assets/products/`. Jalankan `php artisan storage:link` agar gambar dapat diakses browser.

Rincian audit, skema, aturan bisnis, dan pengujian ada di `docs/pos/`.

### Data contoh untuk pengujian lokal

Jalankan `php artisan db:seed` setelah migrasi. Seeder hanya bekerja pada lingkungan `local` dan `testing`. Data contoh mencakup 5 pengguna (1 nonaktif), 6 kategori, 4 satuan, 32 produk yang semuanya bergambar, stok awal, 90 transaksi beserta item dan movement stok, 12 draft, pengaturan toko, dan 6 record referensi. Seeder dapat dijalankan ulang tanpa menggandakan data contoh; gambar produk demo yang belum ada akan diisi kembali tanpa menimpa gambar unggahan admin.

Masuk sebagai admin dengan email `admin@gmail.com` dan kata sandi `admin123`. Akun kasir `dewi@pos-jagat.test`, `bagas@pos-jagat.test`, dan `maya@pos-jagat.test` memakai kata sandi `password`. Akun `nonaktif@pos-jagat.test` tersedia untuk mencoba penolakan login. Kredensial ini hanya untuk data contoh lokal.

## Struktur utama

- `resources/views/components/layouts` — layout admin, guest, dan blank.
- `resources/views/components/admin` — sidebar, header, footer, navigasi, dan komponen bawaan Kenanga.
- `resources/views/components/ui` — komponen UI reusable seperti card, button, field, badge, modal, drawer, combobox, date range, file preview, table state, dan chart.
- `resources/views/admin` — dashboard, analitik, dan pengaturan akun.
- `resources/views/showcase` — katalog komponen untuk referensi saat membangun proyek baru.
- `resources/views/examples/records` — contoh alur daftar, detail, tambah, dan edit yang sepenuhnya frontend.
- `resources/views/reference/records` — contoh CRUD tersimpan dengan komponen Kenanga.
- `resources/js/admin` — grafik SVG dan interaksi dashboard.
- `resources/css/app.css` — design tokens, tema, dan component styles.
- `config/kenanga.php` — identitas POS, navigasi, tampilan tetap, serta data demo untuk showcase.

## Halaman bawaan

| URL | Route name | Keterangan |
| --- | --- | --- |
| `/dashboard` | `admin.dashboard` | Dashboard utama |
| `/components/*` | `showcase.*` | Katalog komponen |
| `/components/filters` | `showcase.filters` | Combobox, rentang tanggal, filter chip, dan unggah berkas |
| `/components/table-states` | `showcase.table-states` | State tabel siap, memuat, kosong, tanpa hasil, dan error |
| `/components/charts` | `showcase.charts` | Variasi grafik SVG dan state kosong |
| `/examples/records` | `examples.records.index` | Contoh daftar dengan pencarian, filter, dan paginasi |
| `/examples/records/new` | `examples.records.create` | Contoh form tambah |
| `/examples/records/detail` | `examples.records.show` | Contoh halaman detail |
| `/examples/records/edit` | `examples.records.edit` | Contoh form edit |
| `/login` | `login` | Login session untuk akun yang sudah ada |
| `/reference/records` | `reference.records.index` | CRUD tersimpan; perlu login dan `ADMIN_SHOWCASE=true` |
| `/demo/404` | `demo.404` | Demo halaman 404 |

Login session, CRUD referensi, dan modul POS sudah terhubung ke backend. Halaman `/examples/records` serta showcase tetap demonstrasi frontend dan tidak ditampilkan pada sidebar POS. Gunakan `ADMIN_SHOWCASE=false` pada produksi. Panduan agent ada di `.agent/README.md`.

## Navigasi POS

Menu sidebar didefinisikan di `config/kenanga.php` dan dikelompokkan menurut fungsi: Ringkasan, Penjualan, Master data, Inventori, Laporan, dan Pengaturan. Menu master data, inventori, laporan, dan pengaturan hanya muncul untuk admin. Nama toko pada sidebar berasal dari pengaturan toko. `ADMIN_SHOWCASE=false` menonaktifkan route katalog komponen dan halaman contoh; route tersebut tidak muncul di sidebar meski showcase aktif.

Contoh halaman baru:

```blade
<x-layouts.admin title="Produk" group="Katalog">
    <x-ui.page-header
        title="Produk"
        description="Kelola produk toko Anda."
    />

    <x-ui.card>
        Konten halaman
    </x-ui.card>
</x-layouts.admin>
```

## Tampilan

Dashboard POS memakai tema terang secara bawaan, dengan aksen indigo dan radius kecil dari pilihan Kenanga yang sudah tersedia. Pengguna dapat beralih ke tema gelap dari navbar; pilihannya tersimpan di browser. Pilihan aksen dan radius template tidak ditampilkan pada antarmuka POS.

Navbar menyediakan pencarian global untuk produk, transaksi, dan nomor draft sesuai akses akun; tombol tema gelap; notifikasi transaksi, draft, dan penyesuaian stok; serta layar penuh. Notifikasi diperbarui setiap 20 detik dan dapat dibuka atau dihapus oleh pemiliknya. Layar penuh mempertahankan navigasi antarhalaman di dalam satu frame; tekan tombol layar penuh lagi atau `Esc` untuk keluar. Izin fullscreen tetap bergantung pada browser dan perangkat.

## Validasi

```bash
php artisan test
php artisan view:cache
npm run build
```

Sprite ikon lokal berada di `public/icons.svg`; starter kit tidak bergantung pada CDN untuk ikon maupun font Inter.

