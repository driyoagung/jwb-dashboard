# Alur kasbon POS

Kasir memilih **Kasbon** pada modal pembayaran dan wajib memilih pelanggan aktif. Tanggal jatuh tempo dan catatan bersifat opsional. Jika pelanggan baru, kasir menambahnya melalui menu Pelanggan lalu kembali ke Kasir; keranjang tetap tersimpan di browser. Kasbon tidak menerima uang muka pada checkout.

Checkout kasbon membuat transaksi berstatus **selesai**, mengurangi stok, dan membuat satu tagihan sebesar total transaksi dalam satu database transaction. Transaksi dan tagihan memakai kunci unik agar pengiriman ulang checkout tidak menggandakan penjualan. Struk menampilkan nama pelanggan, sisa tagihan, dan jatuh tempo.

Menu Kasbon menampilkan saldo belum tertagih, filter status, dan rincian setiap tagihan. Kasir atau admin dapat mencatat cicilan tunai, transfer, atau QRIS manual. Nominal harus positif dan tidak boleh melebihi sisa tagihan. Saldo dan riwayat pembayaran diperbarui secara atomik dengan penguncian baris. Jika sisa nol, status menjadi **lunas**. Lewat jatuh tempo adalah penanda tampilan/filter untuk tagihan yang belum lunas.

Kasir dapat membaca dan menagih kasbon dari semua pelanggan, karena pelanggan bisa membayar melalui kasir mana saja. Pelanggan aktif dapat dibuat/diperbarui kasir maupun admin. Pelanggan nonaktif tetap muncul di riwayat tetapi tidak bisa membuat kasbon baru. Tidak ada penghapusan pelanggan atau tagihan agar riwayat transaksi tetap utuh.

Penjualan kasbon dihitung saat checkout sebagai omzet penjualan. Cicilan hanya mengurangi piutang, tidak menambah omzet atau mengubah stok. Pembayaran cicilan melalui Midtrans belum didukung; pilihan transfer dan QRIS pada form cicilan adalah pencatatan manual setelah dana diterima.
