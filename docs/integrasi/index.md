# Peta integrasi backend

Starter kit menyediakan halaman Blade dengan data demo dan satu contoh CRUD tersimpan di `/reference/records`. Contoh tersebut memakai login session, Form Request, policy kepemilikan, query server, dan komponen Kenanga. Halaman `/examples/records` serta showcase tetap contoh frontend. Lihat juga `.agent/docs/implementasi/referensi-crud.md` untuk jalur kode yang bisa ditiru.

| Bagian demo | Saat memakai data nyata |
| --- | --- |
| `Route::view` untuk dashboard/showcase | Route ke controller dan kirim data ke Blade |
| `config('kenanga.demo.records')` | Query model sesuai user/workspace |
| Form `data-demo-form` | Form `POST`/`PUT` + `@csrf`, Form Request, policy, redirect |
| Tabel `data-table` | Filter dan pagination database untuk dataset besar |
| Grafik array statis | Agregasi controller ke `x-ui.chart` |
| Profil sidebar/notifikasi contoh | Identitas dari user login dan data aplikasi |
| Unggah pratinjau lokal | Nama input, validasi file, penyimpanan pada disk |

## Urutan kerja

1. Baca [struktur Blade](/panduan/struktur) dan [komponen](/komponen/).
2. Pelajari CRUD referensi yang berjalan; gunakan [panduan CRUD](/integrasi/crud) untuk penjelasan domain contoh lain.
3. Sambungkan [form & validasi](/integrasi/form) serta [tabel & grafik server](/integrasi/data).
4. Tentukan role, lindungi route dashboard yang privat, dan ikuti [autentikasi & produksi](/integrasi/auth).

Contoh kode pada panduan integrasi umumnya perlu disesuaikan dengan domain proyek. Model `Record` pada panduan CRUD lama adalah ilustrasi dan berbeda dari `ReferenceRecord` yang sudah diimplementasikan.
