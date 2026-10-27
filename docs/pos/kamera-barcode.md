# Kamera untuk scan barcode

1. Buka aplikasi Iriun di ponsel dan komputer sampai pratinjau video di komputer berjalan. Jika memakai alur OBS, tambahkan Iriun sebagai sumber video di OBS lalu tekan **Start Virtual Camera**.
2. Buka modal **Scan dengan kamera** pada form produk atau kasir. Saat Chrome meminta izin kamera untuk situs, pilih **Izinkan**. POS tidak dapat membuka kamera sebelum browser memberi izin.
3. Pilih **Iriun Webcam** untuk video langsung dari Iriun, atau **OBS Virtual Camera** untuk keluaran OBS. Pemilih sumber pada modal tidak mengubah sumber yang dipilih di OBS.
4. Arahkan barcode ke video sampai seluruh garis dan tepi putih terlihat. Hasil scan mengisi barcode atau menambah produk secara otomatis.
5. Jika Chrome atau kamera virtual terus memuat, pastikan pratinjau Iriun/OBS sudah menampilkan video, pilih sumber lain, lalu tekan **Coba lagi**. POS menampilkan pesan jika izin ditolak, kamera tidak ditemukan, sumber tidak mengirim video, atau kamera terlalu lama merespons.

Browser dapat menunda permintaan kamera tanpa batas saat popup izin dibiarkan terbuka. POS membatasi waktu tunggu agar modal tidak terus menampilkan status persiapan. Setelah izin diberikan, tekan **Coba lagi** jika batas waktu sudah terlewati.
