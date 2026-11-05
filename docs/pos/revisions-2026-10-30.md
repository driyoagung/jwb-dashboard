# Revisi dari catatan aplikasi

Sumber: `public/Catatan Perbaikan Pada Aplikasi.pdf`, 10 halaman.

| Halaman | Catatan | Implementasi |
| --- | --- | --- |
| 1 | Login sesuai referensi | Dua panel, identitas ikon dan teks, form dengan ikon, toggle kata sandi, logo terpusat, dan padding card fitur. |
| 1–2 | Logo sidebar menuju Dashboard | Ikon `logo_G.png` dan teks terpisah dalam tautan Dashboard; pointer dan fokus keyboard tersedia. |
| 2, 9 | Sumbu Y grafik terpotong | Format ringkas `rb`, `jt`, `M`, `T`; margin kiri mengikuti panjang label dan jarak tick mengikuti tinggi grafik. Tooltip mempertahankan nilai lengkap. Berlaku global pada grafik Cartesian. |
| 3, 8 | Waktu berbeda antarhalaman | Penyimpanan UTC, konversi zona toko, timestamp immutable, dan penanda zona waktu pada daftar. Pengujian alur lengkap mencakup WIB, WITA, WIT serta kedaluwarsa draft satu jam. |
| 3–4 | Daftar produk scroll, pembayaran tetap terlihat | Panel menyesuaikan ruang viewport; daftar produk memiliki scroll sendiri dan tombol pembayaran tetap di bawah. |
| 4 | Kembali ke kasir setelah transaksi | Tombol Transaksi baru tersedia pada detail transaksi. |
| 5–6 | Konfirmasi Transfer dan QRIS | Memilih metode langsung membuka modal tujuan transfer atau QRIS merchant dan total terbaru. Pencatatan hanya dilakukan setelah kasir mengonfirmasi dana masuk. |
| 6–7 | Keranjang bersih setelah draft, pembayaran, logout | Respons sukses mengirim daftar key yang dibersihkan; logout membersihkan hanya keranjang pengguna tersebut. Validasi gagal tidak menghapus keranjang. Tab lain dan halaman dari back/forward cache mengikuti penghapusan. |
| 9 | Format struk | Tata letak 80 mm dengan logo monokrom, metadata, kolom angka rata kanan, total tebal, pemisah, footer, dan tombol yang disembunyikan saat cetak. |
| 10 | Jarak card laporan | Jarak vertikal antarbagian dan gap card ringkasan memakai utilitas Kenanga; tetap berlaku setelah filter reaktif. |
