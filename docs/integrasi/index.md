# 🔌 Peta integrasi backend

Starter kit ini adalah tampilan Laravel Blade: saat ini `routes/web.php` memakai `Route::view`, data contoh berasal dari `config/kenanga.php`, dan beberapa form hanya menampilkan toast. Anda dapat mempertahankan komponen tampilannya saat menambahkan backend sendiri.

| Yang ada sekarang | Saat memakai data nyata |
| --- | --- |
| `Route::view` untuk halaman demo | Route ke controller dan kirim variabel ke view |
| `config('kenanga.demo.records')` | Query model atau sumber data aplikasi |
| Form dengan `data-demo-form` | Form `POST`/`PUT` + `@csrf` + validasi + redirect |
| Tabel `data-table` | Paginasi/filter dari database untuk dataset besar |
| Grafik dengan array statis | Agregasi controller ke prop `labels`/`series` |
| Link “Masuk” ke dashboard | Route login, guard/session, dan middleware `auth` |
| Unggah pratinjau lokal | Nama input, validasi file, penyimpanan pada disk |

## 🗺️ Urutan kerja yang disarankan

1. Baca [struktur & Blade](/panduan/struktur) untuk memahami layout dan prop.
2. Ikuti [contoh CRUD](/integrasi/crud) untuk route, model, controller, dan daftar entri.
3. Sambungkan [form & validasi](/integrasi/form).
4. Untuk data besar dan visualisasi, gunakan [tabel & grafik server](/integrasi/data).
5. Terakhir pasang [autentikasi & persiapan produksi](/integrasi/auth).

Contoh di bagian ini adalah **kode contoh untuk ditambahkan**. Nama model, tabel, kolom, dan route yang digunakan tidak tersedia otomatis di starter kit. Cocokkan dengan domain aplikasi Anda.
