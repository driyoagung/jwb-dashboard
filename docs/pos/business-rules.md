# Aturan bisnis POS

## Akses

| Area | Admin | Kasir |
| --- | --- | --- |
| Dashboard | seluruh transaksi/draft | transaksi/draft milik sendiri |
| Kasir dan checkout | ya | ya |
| Draft | semua | milik sendiri |
| Riwayat dan struk | semua | milik sendiri |
| Master data, stok, laporan, pengguna, pengaturan | ya | tidak |

Route privat memakai `auth` dan Gate `pos.sell`/`pos.admin`; pembacaan detail draft/transaksi serta query daftar juga dibatasi di server. Pengguna nonaktif tidak dapat login atau memakai route POS. Admin aktif terakhir tidak boleh didemosi/dinonaktifkan.

## Uang dan stok

- Subtotal = jumlah harga unit saat checkout dikali qty. Diskon nominal dibatasi subtotal. Pajak dihitung dari subtotal setelah diskon, dibulatkan ke rupiah terdekat; total = dasar kena pajak + pajak.
- Pembayaran tunai harus minimal total dan menghasilkan kembalian. Transfer dan QRIS manual harus sama persis dengan total dan tidak menghasilkan kembalian. Sebelum checkout, kasir melihat tujuan transfer atau QRIS merchant dan mengonfirmasi setelah memeriksa dana masuk; konfirmasi ini adalah pernyataan kasir, bukan verifikasi otomatis dari penyedia pembayaran. Transfer hanya tersedia setelah nomor tujuan dan nama penerima diatur oleh admin.
- Produk nonaktif/stok kurang ditolak. Checkout memakai transaksi database dan `lockForUpdate` dengan urutan ID produk tetap, menyimpan snapshot item, mengurangi stok satu kali dan menambah movement. Idempotency key unik mencegah retry menjadi transaksi ganda.
- Produk yang memiliki transaksi atau movement hanya dapat dinonaktifkan. Riwayat transaksi dan movement tidak disediakan aksi edit/hapus.
- Penyesuaian stok manual mencatat aktor, alasan, delta, nilai sebelum/sesudah. Stok negatif ditolak.

## Draft

Draft maksimum 20 aktif per kasir dan berlaku 24 jam secara default; setting toko dapat mengubah keduanya. Draft kedaluwarsa dibaca sebagai expired walaupun tidak ada scheduler. Draft expired tidak bisa checkout. Saat checkout draft, harga dan stok dibaca ulang; perubahan harga perlu konfirmasi. Draft aktif tidak mengurangi stok. Checkout Midtrans menahan stok sampai pembayaran terkonfirmasi atau gagal; draft berpindah ke pending_payment dan tidak bisa diubah atau dibatalkan sementara itu.

Keranjang browser menyimpan produk dan diskon. Saat dipulihkan dan sebelum pembayaran, harga, stok, dan pajak diperiksa ulang melalui endpoint quote. Fingerprint quote diperiksa lagi di dalam transaksi checkout agar perubahan harga atau pajak tidak menghasilkan pembayaran dengan total yang belum ditinjau kasir. Tombol penyesuaian stok menghapus produk yang tidak tersedia dan membatasi jumlah sesuai stok terbaru; kasir meninjau hasilnya sebelum membayar.

Draft yang berhasil disimpan membersihkan keranjang sumber. Pembayaran dari draft membersihkan keranjang draft dan keranjang utama; logout membersihkan semua key keranjang milik pengguna tersebut tanpa menghapus preferensi atau data pengguna lain. Penghapusan juga diikuti tab lain dan halaman yang dipulihkan dari back/forward cache. Validasi gagal mempertahankan keranjang. Pemilihan Transfer manual atau QRIS merchant langsung membuka konfirmasi pembayaran setelah total diperiksa.

Pembayaran Midtrans tertunda dapat dibatalkan dari detail transaksi oleh kasir pemilik atau admin. Proses ini memakai lock yang sama dengan pembuatan sesi. Sesi Snap yang belum memilih metode harus ditutup terlebih dahulu; transaksi yang sudah memilih metode dibatalkan melalui API transaksi dan statusnya diperiksa ulang. Stok hanya dilepas setelah pembatalan dikonfirmasi. Jika sesi awal gagal dibuat, proses mencoba memulihkan sesi dengan order ID yang sama sebelum membatalkannya. Kegagalan koneksi atau status yang belum pasti tetap menahan stok dan menyediakan opsi coba lagi. Pembayaran yang ternyata sudah berhasil tetap dicatat selesai.

Kasbon dianggap terlambat hanya jika tanggal jatuh tempo lebih kecil dari tanggal hari ini di zona waktu toko. Label, filter, dan validasi tanggal checkout memakai tanggal toko yang sama; jatuh tempo hari ini belum terlambat.

## Laporan

Pelanggan dan kasbon dikendalikan oleh `config/pos.php`: `POS_CUSTOMERS_ENABLED` dan `POS_CREDIT_ENABLED` default false. Kasbon memerlukan kedua flag true; middleware, navigasi dan checkout mengikuti flag yang sama. Menonaktifkan fitur tidak menghapus data atau omzet lama. Seeder baru mengikuti konfigurasi dan tidak membuat metode OTHER.

`price_histories` hanya menyimpan id, product_id, user_id, price_type (`cost`/`sell`), old_price, new_price, created_at. Harga awal memakai old_price null; modal yang dikosongkan memakai new_price null. Pencatatan perubahan dan pembaruan produk berada dalam satu transaksi dengan row lock; simpan tanpa perubahan tidak membuat riwayat. Produk yang memiliki riwayat dihapus secara soft delete. Histori hanya dapat dibaca admin dan tidak tersedia aksi ubah/hapus.

Harga modal lebih besar dari harga jual ditolak di server dan diberi peringatan pada form. Upload produk menerima JPG/JPEG, PNG, WebP, GIF sampai 5 MB. Pratinjau mengikuti atribut accept; route `pos.products.image` memeriksa akses, path dan MIME sebelum mengirim gambar dari disk public.

Laba kotor = subtotal - discount_amount - cost_total, tanpa pajak. `transaction_items.unit_cost` dan `transactions.cost_total` diisi saat checkout dari produk yang dikunci. Jika salah satu modal belum diisi, cost_total dan laba kotor null. Total laba hanya menjumlahkan transaksi selesai dengan modal lengkap dan menyebut jumlah transaksi yang dikecualikan. Tidak ada backfill harga modal saat ini ke transaksi lama. Nilai negatif setelah diskon tetap dilaporkan. Data seed baru memiliki snapshot modal; data lama tidak diubah.

Omzet hanya berasal dari `transactions.status=completed` dan `grand_total`. Timestamp disimpan dalam UTC; daftar Draft, riwayat stok, transaksi, notifikasi, dan laporan menampilkan waktu sesuai zona waktu toko. Dashboard memakai zona waktu toko untuk hari ini dan tujuh hari terakhir. Ekspor CSV memiliki filter yang sama dengan daftar, BOM UTF-8 dan mitigasi formula injection. Nilai draft merupakan perkiraan dari harga saat draft disimpan, bukan omzet.

Timestamp transaksi, notifikasi, draft, dan stok memakai cast immutable agar konversi untuk tampilan tidak mengubah objek tanggal yang dipakai bagian lain. Sumbu Y grafik memakai label ringkas dengan margin yang menyesuaikan panjang label; tooltip tetap menampilkan nilai lengkap. Struk memakai area 80 mm dengan isi cetak 72 mm dan margin halaman 4 mm; tombol tidak dicetak.
