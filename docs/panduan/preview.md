# 👁️ Preview komponen di dokumentasi

Dokumentasi VitePress tetap statis. Preview di halaman tiap komponen dibuat dari **render Blade asli** melalui `php artisan docs:previews`, bukan simulasi Vue yang dapat menyimpang dari implementasi aplikasi. Hasilnya berupa halaman HTML mandiri di `docs/public/preview/` yang memuat CSS/JS hasil `npm run build`.

## 🚀 Jalankan lokal

```bash
npm install
npm run docs:dev
```

Perintah tersebut membangun aset Laravel, merender preview, lalu menjalankan VitePress. Untuk produksi, `npm run docs:build` melakukan langkah yang sama sebelum build statis. Jika komponen Blade/CSS/JS berubah ketika server docs masih berjalan, jalankan `npm run docs:previews` lagi dan muat ulang browser. Output preview digenerate dan diabaikan Git; sumbernya ada di `resources/views/docs/preview.blade.php`.

## 🧩 Cara membaca halaman komponen

Preview berada di atas blok kode. Cocokkan bentuk, interaksi, dan state pada preview dengan contoh Blade di bawahnya. Beberapa preview menggabungkan banyak variant sekaligus—misalnya button, badge, dan card—agar seluruh bentuk cepat terlihat. Kode di halaman referensi menunjukkan cara menyalin tiap variant ke layout admin.

## 🔒 Batas pratinjau

Form di iframe dicegah melakukan submit agar tidak mengubah data. Untuk confirm action, dialog dan tombol submit bisa dicoba tanpa memanggil route. Data preview adalah contoh lokal; penggunaan sesungguhnya tetap mengikuti route, model, validasi, dan CSRF aplikasi. `x-ui.pagination` sengaja menampilkan pesan keterbatasan karena komponen tersebut belum siap menerima paginator secara utuh. Tema iframe mengikuti pilihan terang/gelap VitePress.
