# Kontrak role dan akses

**Jangan menebak nama role atau izin proyek baru.** Sebelum membuat dashboard untuk admin, editor, staf, atau role lain, tulis matriks akses proyek. Starter kit menyediakan shell dan contoh policy kepemilikan; belum ada kolom role, permission package, guard khusus, atau aturan lintas tenant.

## Kontrak yang sudah berlaku pada referensi CRUD

| Pengguna | Daftar | Buat | Baca detail | Ubah/hapus |
| --- | --- | --- | --- | --- |
| Guest | Redirect ke login | Redirect ke login | Redirect ke login | Redirect ke login |
| User login, data sendiri | Ya | Ya | Ya | Ya |
| User login, data milik orang lain | Tidak muncul di daftar | — | 403 | 403 |

`ReferenceRecordPolicy` adalah **contoh kepemilikan**, bukan kebijakan admin universal. Jangan menambah bypass admin hanya berdasarkan label di sidebar. Jika proyek membutuhkan administrator lintas pemilik, definisikan ability dan scope query secara eksplisit, lalu uji keduanya.

## Matriks yang wajib diisi saat membuat proyek

| Area/resource | Role | Lihat daftar/detail | Buat | Ubah | Hapus/ekspor | Batas data |
| --- | --- | --- | --- | --- | --- | --- |
| [isi domain] | [isi role] | [ya/tidak] | [ya/tidak] | [ya/tidak] | [ya/tidak] | [semua/milik sendiri/workspace] |

Untuk setiap baris matriks, tentukan:

- Sumber identitas dan guard; route privat memakai middleware `auth`.
- Policy/gate untuk setiap aksi dan query scope untuk batas data. Filter daftar harus diberi scope **sebelum** pagination.
- Menu di `config/kenanga.php` sesuai kemampuan user, tetapi menu tersembunyi bukan pengganti otorisasi HTTP.
- Kasus 401/redirect, 403, dan akses lintas user/workspace di feature test.
- Perubahan role atau kepemilikan saat data sudah ada, termasuk siapa yang berhak memindahkan data.

Periksa `docs/integrasi/auth.md` untuk catatan autentikasi starter kit dan [referensi CRUD](../implementasi/referensi-crud.md) untuk contoh policy yang berjalan.
