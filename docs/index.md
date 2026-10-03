---
layout: home

hero:
  name: Kenanga Admin
  text: 📚 Dokumentasi Laravel Blade
  tagline: Dari halaman demo ke dashboard yang terhubung dengan data proyek Anda. Pelajari komponen, pola halaman, dan langkah integrasi backend.
  actions:
    - theme: brand
      text: Mulai dari instalasi
      link: /panduan/mulai
    - theme: alt
      text: Jelajahi komponen
      link: /komponen/

features:
  - title: 🧩 Blade sebagai fondasi
    details: Susun halaman dengan <x-layouts.admin>, komponen <x-ui.*>, prop, dan slot Blade.
    link: /panduan/struktur
    linkText: Pahami strukturnya
  - title: 🧰 Komponen yang bisa dirujuk
    details: Contoh penggunaan dan batasan komponen diambil dari implementasi dan halaman showcase proyek ini.
    link: /komponen/
    linkText: Buka referensi
  - title: 🔌 Jalur ke data nyata
    details: Ubah Route::view, data config, form demo, tabel client-side, dan login menjadi alur Laravel.
    link: /integrasi/
    linkText: Lihat panduan integrasi
---

## 🧭 Mulai dari mana?

Baru membuka proyek? Ikuti [instalasi dan menjalankan aplikasi](/panduan/mulai), kemudian [struktur halaman Blade](/panduan/struktur). Jika sudah membangun halaman sendiri, buka [peta komponen](/komponen/) atau [panduan integrasi backend](/integrasi/).

::: info Batas starter kit
Dashboard ini menyediakan frontend Laravel Blade. Data contoh berada di `config/kenanga.php`, route demo memakai `Route::view`, dan tombol simpan contoh hanya menampilkan notifikasi. Dokumentasi integrasi berisi **contoh implementasi yang perlu Anda tambahkan**, bukan fitur backend yang sudah tersedia.
:::
