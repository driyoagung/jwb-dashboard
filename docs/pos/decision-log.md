# Catatan keputusan POS

| Tanggal | Keputusan | Alasan dan dampak |
| --- | --- | --- |
| 17 Okt 2026 | Grafik memakai `<x-ui.chart>` Kenanga | Chart.js tidak terpasang; visual mengikuti komponen yang tersedia. |
| 17 Okt 2026 | Daftar POS memakai filter dan pagination server | `data-table` Kenanga hanya bereaksi pada baris DOM halaman ini; query server menjaga hasil dan batas akses tetap benar. |
| 17 Okt 2026 | Gambar produk belum dipakai | Input `x-ui.file-preview` belum mempunyai `name` untuk upload nyata; katalog inti tetap berjalan. |
| 17 Okt 2026 | Kategori item transaksi menyimpan FK kategori saat checkout | Filter laporan kategori memakai kategori pada saat penjualan, tanpa berubah saat produk dipindah kategori. |
| 17 Okt 2026 | Shell POS hanya menampilkan navigasi POS dengan tema terang, aksen indigo, dan radius kecil | Katalog template tetap dapat diakses melalui URL saat showcase aktif, tetapi tidak muncul di sidebar; tampilan operasional konsisten. |
| 17 Okt 2026 | Navbar POS memakai pencarian server, tema gelap tersimpan, dan notifikasi per pengguna | Kasir hanya mencari data miliknya; transaksi, draft, dan penyesuaian stok menghasilkan informasi yang dapat dibaca atau dihapus sesuai kepemilikan. |
| 17 Okt 2026 | Layar penuh mempertahankan frame saat navigasi | Fullscreen API browser melekat pada satu elemen; navigasi berlangsung di dalam frame agar perpindahan menu tidak keluar dari fullscreen. |
