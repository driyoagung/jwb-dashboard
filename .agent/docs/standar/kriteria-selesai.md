# Kriteria selesai fitur dashboard

Gunakan daftar ini saat meninjau hasil kerja AI agent. Sesuaikan dengan tugas; perubahan teks sederhana tidak perlu seluruh pengujian backend.

## Tampilan Kenanga

- [ ] View memakai `x-layouts.admin` atau `x-layouts.guest`; komposisi memakai komponen `x-ui.*` dan pola resmi saat tidak ada wrapper.
- [ ] Tidak ada primitive UI/library baru yang menduplikasi komponen Kenanga. Ikon berasal dari sprite lokal.
- [ ] Named route, navigasi aktif, breadcrumb, mode terang/gelap, dan tampilan responsif diperiksa.
- [ ] State kosong, filtered, error, loading, validasi form, serta umpan balik sukses sesuai perilaku fitur.

## Backend dan akses

- [ ] Matriks role/resource proyek sudah jelas; middleware dan policy diterapkan pada semua route/aksi privat.
- [ ] Query daftar dibatasi sesuai user/workspace sebelum pagination; tidak ada akses silang lewat URL atau payload.
- [ ] Input tulis divalidasi Form Request; controller menggunakan data tervalidasi; CSRF dan method spoofing benar.
- [ ] Aksi simpan/hapus sungguh tersambung; tidak tertinggal `data-demo-form`, tautan `#`, atau toast palsu yang seolah-olah sukses.
- [ ] Daftar besar memakai filter/sort/pagination server; `data-table` tidak dicampur dengan paginator server pada dataset yang sama.
- [ ] Relasi yang dirender eager loaded, indeks mendukung query penting, dan ekspor besar diproses bertahap/queue bila perlu.

## Verifikasi

- [ ] Feature test membuktikan alur utama, data invalid, guest, role yang diizinkan/ditolak, dan batas data.
- [ ] `vendor/bin/pint --test`, `php artisan test`, `php artisan view:cache`, dan `npm run build` dijalankan sesuai area perubahan.
- [ ] Dokumentasi `.agent` dan contoh halaman diperbarui bila kontrak komponen, route, atau batas fiturnya berubah.
- [ ] Keterbatasan yang tersisa disebutkan secara spesifik saat menyerahkan fitur.
- [ ] Laporan akhir menyertakan satu usulan commit message Conventional Commits, satu kalimat bahasa Inggris; PR memakai template `.github/PULL_REQUEST_TEMPLATE.md`.

Lihat [CRUD referensi](../implementasi/referensi-crud.md), [kontrak role](kontrak-role.md), dan [matriks komponen](../fondasi/matriks-komponen.md).
