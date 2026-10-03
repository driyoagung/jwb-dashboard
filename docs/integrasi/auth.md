# 🔐 Autentikasi & produksi

Halaman `/login` saat ini hanyalah presentasi: tombol “Masuk” berupa tautan ke dashboard; kolom email/password bukan form yang mengirim kredensial. Route dashboard juga belum memakai middleware autentikasi. Integrasikan fitur login dari pilihan autentikasi Laravel proyek Anda atau implementasikan controller/session sendiri sebelum memakai halaman ini sebagai gerbang akses.

## 🔑 Hubungkan view login

Saat membangun login sesungguhnya, ubah blok input di `resources/views/auth/login.blade.php` menjadi form `method="POST"` yang mengirim ke `route('login.store')`, dengan `@csrf`, kontrol dengan `name="email"` dan `name="password"`, opsi `remember` bila digunakan, serta tombol submit “Masuk”. Gunakan `old('email')` dan `$errors` untuk menampilkan kegagalan autentikasi. Route bernama `login.store` pada contoh ini **harus Anda daftarkan sendiri** atau sesuaikan dengan paket autentikasi yang dipilih.

Controller login harus memvalidasi input, menjalankan autentikasi Laravel, dan meregenerasi sesi setelah berhasil. Logout harus mengakhiri sesi dan meregenerasi token CSRF. Jika menggunakan starter kit/paket autentikasi yang sudah menyediakan route login bernama `login`, periksa konflik dengan `Route::view('/login', 'auth.login')->name('login')` yang ada sekarang dan ganti route tersebut sesuai instalasi Anda.

## 🛡️ Lindungi route admin

Setelah login tersedia, tambahkan middleware `auth` pada halaman privat (misalnya dashboard, analitik, settings, serta CRUD). Jangan memasukkan route login atau halaman 404 publik ke dalam grup ini.

```php
Route::middleware('auth')->group(function () {
    Route::view('/dashboard', 'admin.dashboard')->name('admin.dashboard');
    Route::view('/analytics', 'admin.analytics')->name('admin.analytics');
    Route::view('/settings', 'admin.settings')->name('admin.settings');
    // Route CRUD privat yang ditambahkan di sini.
});
```

Lindungi juga setiap operasi tulis dengan pemeriksaan izin sesuai aturan proyek. Identitas pengguna di sidebar (`resources/views/components/admin/sidebar.blade.php`) dan sebagian info di header masih teks contoh; tampilkan identitas dari `auth()->user()` setelah route benar-benar terlindungi.

## 🚢 Saat deploy

Siapkan `.env` produksi dan database aplikasi, jalankan migrasi sesuai skema baru, lalu bangun aset Laravel dengan `npm run build`. Dokumentasi dibangun terpisah lewat `npm run docs:build` menjadi berkas statis di `docs/.vitepress/dist`; Anda bisa menerbitkannya sebagai situs dokumentasi statis tanpa mengarahkan request docs ke Laravel. Gunakan `ADMIN_SHOWCASE=false` jika katalog contoh tidak ingin ditampilkan pada aplikasi produksi, dan periksa tautan menuju showcase sebelum menonaktifkannya.
