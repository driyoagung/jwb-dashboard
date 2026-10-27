# Checklist pengujian POS

- [x] Guest diarahkan ke login; kasir ditolak dari route admin.
- [x] Halaman admin dan kasir inti dirender melalui komponen Kenanga.
- [x] Checkout menyimpan snapshot, pajak, pembayaran, movement dan mengurangi stok sekali.
- [x] Retry dengan idempotency key yang sama tidak menggandakan transaksi.
- [x] Stok kurang dan pembayaran kurang ditolak tanpa mengubah database.
- [x] Draft tidak mereservasi stok, scope milik kasir, perubahan harga meminta konfirmasi, expired ditolak.
- [x] Penyesuaian stok keluar ditolak bila negatif; movement mencatat delta.
- [x] Laporan CSV memitigasi formula injection; admin terakhir tidak dapat didemosi.
- [x] Kegagalan saat movement dibuat me-rollback transaksi, item, dan stok.
- [x] Produk terjual hanya dinonaktifkan; snapshot nama dan harga tetap sama setelah master diubah.
- [x] Agregasi harian mengisi tanggal kosong dan mengikuti zona waktu toko.
- [x] Ekspresi agregasi harian berhasil dijalankan pada koneksi MySQL proyek.
- [x] `php artisan test`, `php artisan view:cache`, `vendor/bin/pint --test`, `npm run build`.
- [ ] Uji dua checkout serentak terhadap stok satu pada MySQL staging.
- [ ] UAT browser desktop/mobile dan cetak struk pada printer pengguna.
- [ ] Uji operasi dengan dataset besar dan indeks pada MySQL staging.

Jalankan: `php artisan test`, `vendor/bin/pint --test`, `php artisan view:cache`, `npm run build`. Untuk migrasi: `php artisan migrate --no-interaction` pada lingkungan yang dituju. Gunakan `ADMIN_SHOWCASE=false` pada produksi.
