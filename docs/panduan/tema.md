# 🎨 Tema, branding & aset

## 🏷️ Branding

Nilai berikut dibaca `config/kenanga.php` dari `.env`:

```dotenv
ADMIN_NAME="Kenanga Admin"
ADMIN_BRAND_NAME="Kenanga Admin"
ADMIN_WORKSPACE="Kopi Kenanga"
ADMIN_WORKSPACE_DESCRIPTION="Toko online"
ADMIN_SHOWCASE=true
```

`ADMIN_NAME` dipakai untuk judul dokumen, `ADMIN_WORKSPACE` dan deskripsinya tampil di sidebar. Sebagian konten demo (termasuk nama toko di login, profil di sidebar, dan angka dashboard) masih ditulis langsung pada view: ubah bagian tersebut ketika mengganti identitas proyek. Setelah mengubah konfigurasi pada aplikasi yang sudah di-cache, jalankan `php artisan optimize:clear`.

## 🌈 Token visual

`resources/css/app.css` berisi token warna berbasis variabel CSS, ukuran radius, komponen seperti `.btn`, `.badge`, `.table`, serta aturan terang/gelap. Tailwind CSS 4 memindai Blade dan JavaScript lewat direktif `@source`. Jika menambah kelas dinamis, pastikan kelas lengkap tetap dapat ditemukan oleh pemindai Tailwind.

Customizer admin menyediakan tema sistem/terang/gelap, pilihan aksen dan radius, serta sidebar penuh/mini. Pilihan browser disimpan di `localStorage` (`theme`, `accent`, `radius`, `sbmode`). Skrip kecil di `<x-layouts.head>` menerapkannya sebelum CSS dimuat supaya transisi halaman tidak berkedip.

```js
window.App.setPref('theme', 'dark')
window.App.setPref('accent', 'blue')
window.App.setPref('radius', '0.75')
```

Nilai aksen yang tersedia tercantum pada `config('kenanga.accents')`; default CSS adalah `indigo`. Bila hendak mengganti pilihan default atau daftar aksen, samakan konfigurasi PHP, token CSS, dan nilai default pada `resources/js/admin/interactions.js`.

## 🧿 Ikon & font

Ikon utama memakai sprite `public/icons.svg`. Gunakan `<x-ui.icon name="search" class="h-5 w-5" />`; nama harus cocok dengan `id="i-search"` pada sprite. Ikon dekoratif diberi `aria-hidden`; tombol yang hanya berisi ikon perlu `aria-label` pada tombol.

Font Inter pada dashboard diimpor melalui `@fontsource/inter` dari `resources/css/app.css`; aset dashboard dibundel dengan Vite melalui `<x-layouts.head>`. Dokumentasi VitePress punya pipeline dan tema tersendiri serta tidak memuat CSS dashboard secara langsung.
