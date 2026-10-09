# Autentikasi dan persiapan produksi

`/login` sekarang memakai `AuthController`, `LoginRequest`, session Laravel, dan form Blade dengan komponen Kenanga. POST `/login` diberi throttle, sesi diregenerasi setelah berhasil, dan POST `/logout` mengakhiri sesi. Belum ada registrasi publik, reset password, atau sistem role umum. Akun perlu dibuat melalui proses provisioning proyek.

Route `/reference/records` memakai middleware `auth` dan policy kepemilikan sebagai contoh CRUD nyata di `.agent/docs/implementasi/referensi-crud.md`. Route `/dashboard`, `/analytics`, `/settings`, serta showcase bawaan masih terbuka agar template dapat dijelajahi. **Jangan menganggap login saat ini melindungi seluruh dashboard.** Saat memakai starter kit untuk proyek privat, kelompokkan seluruh route privat dalam middleware `auth` dan tentukan policy/guard sesuai kontrak role di `.agent/docs/standar/kontrak-role.md`.

## Melindungi fitur proyek

```php
Route::middleware('auth')->group(function (): void {
    Route::get('/products', [ProductController::class, 'index'])
        ->name('admin.products.index');
    // Tambahkan route privat lain di sini.
});
```

Setiap aksi tulis/baca sensitif memerlukan policy/gate di samping middleware `auth`. Filter query sesuai user atau workspace sebelum pagination. Identitas user pada sidebar dan beberapa notifikasi header masih data demo; hubungkan ke `auth()->user()` dan sumber data aplikasi saat shell dipakai dalam produksi. Periksa juga menu atau tautan ke route showcase sebelum mengatur `ADMIN_SHOWCASE=false`.

Jika memasang paket autentikasi lain, periksa konflik route bernama `login`, `login.store`, dan `logout` dengan route bawaan sebelum menggantinya. Jangan menyimpan password atau token di source code. Di produksi, gunakan `APP_DEBUG=false`, HTTPS/session cookie yang sesuai, dan proses provisioning akun yang aman.

## Verifikasi

Feature test di `tests/Feature/ReferenceRecordFeatureTest.php` membuktikan guest diarahkan ke login, sesi login/logout bekerja, dan user lain mendapat 403 pada record yang bukan miliknya. Tambahkan test untuk role dan resource proyek Anda. Jalankan `php artisan test`, `php artisan view:cache`, dan `npm run build` sebelum deploy.
