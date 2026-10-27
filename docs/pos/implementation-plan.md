# Rencana implementasi POS

1. Fondasi: migrasi domain, role/status pengguna, pengaturan toko, auth route, policy/gate, sidebar berbasis izin. Verifikasi migrasi dan 403.
2. Master data: kategori, satuan, produk dengan validasi, status, pencarian dan pagination server. Verifikasi SKU/barcode unik dan histori produk terjaga.
3. Inventori: stok integer nonnegatif, penyesuaian atomik, movement immutable dan daftar riwayat. Verifikasi rollback.
4. Kasir: keranjang berbasis form/JS minimal, harga dihitung ulang server, checkout atomik, locking stok, pembayaran dan idempotency. Verifikasi request ulang dan stok tidak negatif.
5. Draft dan riwayat: batas draft per kasir, expiry, rekonsiliasi harga/stok, detail transaksi serta struk cetak.
6. Dashboard dan laporan: agregasi dari DB dengan scope role, grafik Kenanga, CSV aman dan konsisten dengan filter.
7. Hardening: feature tests akses/validasi/stock, lint dan build, migrasi MySQL, pemeriksaan desktop/mobile dan dokumentasi penggunaan.

Setiap fase memakai komponen dan CSS Kenanga yang ada. `data-table` dipakai hanya untuk data kecil yang seluruhnya dimuat; daftar database memakai tabel `.table` dengan filter dan paginator server. Tidak ada library UI baru. Fase implementasi aplikasi selesai; verifikasi concurrency MySQL staging dan UAT browser/printer masih tercatat di `testing-checklist.md`.
