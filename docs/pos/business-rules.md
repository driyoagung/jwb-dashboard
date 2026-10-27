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
- Pembayaran tunai harus minimal total dan menghasilkan kembalian. Pembayaran lain harus sama persis dengan total, tanpa kembalian, dan hanya dicatat manual.
- Produk nonaktif/stok kurang ditolak. Checkout memakai transaksi database dan `lockForUpdate` dengan urutan ID produk tetap, menyimpan snapshot item, mengurangi stok satu kali dan menambah movement. Idempotency key unik mencegah retry menjadi transaksi ganda.
- Produk yang memiliki transaksi atau movement hanya dapat dinonaktifkan. Riwayat transaksi dan movement tidak disediakan aksi edit/hapus.
- Penyesuaian stok manual mencatat aktor, alasan, delta, nilai sebelum/sesudah. Stok negatif ditolak.

## Draft

Draft maksimum 20 aktif per kasir dan berlaku 24 jam secara default; setting toko dapat mengubah keduanya. Draft kedaluwarsa dibaca sebagai expired walaupun tidak ada scheduler. Draft expired tidak bisa checkout. Saat checkout draft, harga dan stok dibaca ulang; perubahan harga perlu konfirmasi. Draft aktif tidak mengurangi stok. Checkout Midtrans menahan stok sampai pembayaran terkonfirmasi atau gagal; draft berpindah ke pending_payment dan tidak bisa diubah atau dibatalkan sementara itu.

## Laporan

Omzet hanya berasal dari `transactions.status=completed` dan `grand_total`. Dashboard memakai zona waktu toko untuk hari ini dan tujuh hari terakhir. Ekspor CSV memiliki filter yang sama dengan daftar, BOM UTF-8 dan mitigasi formula injection. Nilai draft merupakan perkiraan dari harga saat draft disimpan, bukan omzet.
