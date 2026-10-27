# Skema database POS

Migrasi `2026_10_17_000001_create_pos_foundation.php` menambah role/status pada `users` serta tabel `store_settings`, `categories`, `units`, `products`, `transactions`, `transaction_items`, `stock_movements`, dan `transaction_drafts`. Migrasi berikutnya menambah snapshot `category_id` pada item transaksi untuk filter laporan kategori. Migrasi ketiga menambah `pos_notifications`; migrasi keempat menambah `image_path` opsional pada produk. Migrasi kelima menambah status/URL pembayaran Midtrans, referensi draft, dan waktu selesai yang boleh kosong saat pembayaran tertunda. Migrasi keenam menyimpan token Snap untuk membuka ulang popup pembayaran.

| Tabel | Relasi dan aturan penting |
| --- | --- |
| `users` | `role` (`admin`/`cashier`), `is_active`; auth session existing dipakai ulang |
| `store_settings` | satu baris `id=1`; pajak `decimal(5,2)`, expiry dan batas draft |
| `categories`, `units` | nama unik; tidak dihapus bila masih dipakai produk |
| `products` | FK kategori/satuan, SKU dan barcode unik, harga integer rupiah, stok integer nonnegatif, path gambar nullable pada disk `public` |
| `transactions` | nomor dan idempotency key unik, FK kasir/draft, status pending/completed/failed, angka rupiah integer, waktu selesai opsional, status/URL/token Snap Midtrans |
| `transaction_items` | FK transaksi dan produk opsional, snapshot nama/SKU/satuan/harga/qty; kategori untuk filter laporan |
| `stock_movements` | FK produk/aktor/transaksi opsional, delta bertanda, stok sebelum/sesudah dan alasan; append only pada aplikasi |
| `transaction_drafts` | FK kasir, status termasuk pending_payment, expiry, JSON item/qty/harga tersimpan; draft aktif tidak mereservasi stok |
| `pos_notifications` | FK penerima, jenis, pesan, URL tujuan, waktu dibaca; hanya penerima dapat membaca atau menghapus |

Indeks pada FK, SKU/barcode, nama produk, status, tanggal transaksi, kasir, dan expiry draft. Uang disimpan sebagai `unsignedBigInteger`, tidak memakai float. Jumlah barang dibatasi sampai 2 miliar, harga produk sampai Rp 1 miliar dan qty per item checkout sampai 1.000 agar kalkulasi integer aman.

Migrasi dirancang untuk MySQL/InnoDB dan juga diuji pada SQLite in-memory. Verifikasi locking checkout bersamaan pada instance MySQL produksi/staging tetap diperlukan.
