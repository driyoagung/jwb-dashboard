# 🧩 Struktur & pola Blade

Proyek ini memakai **anonymous Blade components** di `resources/views/components`. Nama file menentukan tag: `components/ui/card.blade.php` menjadi `<x-ui.card>`, dan `components/layouts/admin.blade.php` menjadi `<x-layouts.admin>`.

## 🗂️ Direktori utama

| Lokasi | Isi |
| --- | --- |
| `resources/views/components/layouts/` | Layout `admin`, `guest`, `head`; `blank` adalah berkas alternatif yang tidak dipakai route utama |
| `resources/views/components/admin/` | Sidebar, header, footer, customizer, navigasi |
| `resources/views/components/ui/` | Komponen UI yang dapat dirakit di halaman |
| `resources/views/admin/` | Dashboard, analitik, pengaturan |
| `resources/views/showcase/` | Contoh penggunaan komponen |
| `resources/views/examples/records/` | Contoh alur entri frontend |
| `resources/js/admin/` | Interaksi, grafik SVG, input lanjutan |
| `resources/css/app.css` | Tailwind, token tema, dan kelas UI |
| `config/kenanga.php` | Nama, branding, sidebar, pilihan aksen, dan data demo |

## 🧱 Layout dan slot

Halaman admin menggunakan layout komponen; `title` juga digunakan untuk judul browser dan breadcrumb, sedangkan `group` untuk breadcrumb. Konten di antara tag ditempatkan ke `$slot` dalam `<main id="page-root">`.

```blade
<x-layouts.admin title="Produk" group="Katalog">
    <x-ui.page-header title="Produk" description="Kelola produk toko." />

    <x-ui.card title="Produk terbaru">
        <p>Konten halaman.</p>
    </x-ui.card>
</x-layouts.admin>
```

Untuk halaman tanpa sidebar gunakan `<x-layouts.guest title="Masuk">…</x-layouts.guest>` seperti `resources/views/auth/login.blade.php`. Komponen `<x-layouts.head>` memuat meta CSRF, preferensi tema sebelum halaman tampil, dan aset `@vite(['resources/css/app.css', 'resources/js/app.js'])`.

## 🏷️ Prop, atribut, dan named slot

Komponen mendeklarasikan prop melalui `@props`. Atribut HTML lain (misalnya `class`, `id`, `disabled`, atau `aria-label`) umumnya diteruskan melalui `$attributes`. Ekspresi PHP diawali `:`; string biasa tidak.

```blade
<x-ui.card title="Ringkasan" class="min-w-0">
    <x-slot:header>
        <h2 class="card-title">Ringkasan hari ini</h2>
    </x-slot:header>

    Konten utama

    <x-slot:footer>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline">Dashboard</a>
    </x-slot:footer>
</x-ui.card>
```

Pada `x-ui.card`, slot `header` menggantikan header bawaan dari `title`/`description`; slot `footer` muncul di bagian bawah. Contoh prop dinamis: `:options="$categories"`, `:checked="(bool) $user->active"`.

### 🧪 Membaca prop dari file komponen

Saat suatu komponen terasa kurang jelas, buka file Blade-nya dan mulai dari deklarasi `@props([...])`: itulah daftar opsi dan default yang berlaku saat ini. Berikutnya periksa elemen root untuk `$attributes->class(...)` atau `$attributes->merge(...)` agar tahu atribut HTML apa yang diteruskan, lalu periksa `{{ $slot }}`/`@isset($footer)` untuk posisi slot. Contoh: `x-ui.button` selalu merender `<button>`, sedangkan `x-ui.modal` merender `<dialog>`. Perbedaan elemen ini menentukan apakah Anda harus memakai `type="submit"`, `aria-label`, atau `data-modal-open`.

::: tip Pola yang dipakai
Gunakan tag `<x-...>` dan slot untuk susunan halaman. Beberapa contoh masih memakai direktif Blade seperti `@foreach` dan `@if` untuk data dan kondisi; itu tetap bagian dari Blade modern.
:::

## 📦 Sumber data sekarang

Halaman admin dan showcase membaca `config('kenanga.demo.*')` atau menulis nilai contoh langsung di view. JavaScript memakai atribut `data-*` untuk interaksi browser. Lihat [peta migrasi ke backend](/integrasi/) untuk mengganti data tersebut dengan nilai dari controller.
