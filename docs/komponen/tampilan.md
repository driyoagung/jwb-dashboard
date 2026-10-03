# 🧱 Tata letak & tampilan

## 🗂️ Layout dan judul halaman

`x-layouts.admin` menerima `title` (wajib), `group` (default `Platform`) dan slot isi halaman. `x-layouts.guest` menerima `title` untuk halaman tanpa sidebar. Judul isi halaman biasanya memakai `x-ui.page-header` dengan `title` dan `description` opsional.

```blade
<x-layouts.admin title="Pesanan" group="Penjualan">
    <x-ui.page-header title="Pesanan" description="Pantau pesanan terbaru." />
    {{-- Konten halaman --}}
</x-layouts.admin>
```

### 🧭 `x-layouts.admin` dan `x-layouts.guest`

`x-layouts.admin` membangun dokumen lengkap: `<head>` dengan `@vite`, sidebar, header, `<main>`, footer, customizer, serta kontainer toast. Slot isi ditempatkan di area konten yang sudah responsif; jangan menambahkan `<html>` atau `<body>` lagi di halaman anak. `group` hanya mengubah label breadcrumb, bukan grup navigasi sidebar. `x-layouts.guest` memakai head dan kontainer toast yang sama tanpa sidebar; login bawaan menggunakan layout ini.

### 🏷️ `x-ui.page-header` {#x-ui-page-header}

`title` wajib, `description` opsional. Komponen menghasilkan `<h1>` untuk judul konten. Gunakan satu judul utama per halaman; teks pada header admin adalah breadcrumb, bukan pengganti heading konten. Atribut tambahan seperti `class="mb-6"` diteruskan ke pembungkus melalui `$attributes`.

## 🧩 Card dan slot {#card-dan-slot}

`x-ui.card` menerima `title` dan `description` opsional, serta slot isi, `header`, dan `footer`. Jika `header` diisi, ia menggantikan judul/deskripsi header standar.

```blade
<x-ui.card title="Pelanggan" description="Daftar terbaru" class="min-w-0">
    <p>Konten kartu</p>
    <x-slot:footer>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline">Lihat dashboard</a>
    </x-slot:footer>
</x-ui.card>
```

Header kartu dirender hanya jika ada `title`, `description`, atau named slot `header`. Isi selalu dibungkus `.card-content`, dan `footer` dirender hanya jika disediakan. Hindari membuat `.card` lain di dalam konten hanya untuk padding: komponen sudah menyediakan struktur kartu. Jika membutuhkan tata letak tanpa padding konten bawaan (misalnya tabel sampai ke tepi kartu), gunakan markup `<div class="card">` langsung seperti showcase tabel.

## 🖱️ Tombol, badge, dan ikon

### 🔘 `x-ui.button` {#x-ui-button}

`x-ui.button` menghasilkan elemen `<button>`; prop `type` default `button` (gunakan `submit` di dalam form). `variant`: `primary`, `secondary`, `outline`, `ghost`, `soft`, `destructive`, `success`, `warning`, `info`, `link`. `size`: `default`, `sm`, `lg`, `icon`. Prop yang tidak dikenali untuk variant/size kembali ke default. Untuk navigasi gunakan `<a class="btn btn-primary">` karena komponen ini **tidak** menghasilkan tautan.

Komponen meneruskan atribut HTML standar ke elemen tombol; misalnya `disabled`, `name`, `value`, `aria-label`, `data-modal-open` atau `data-toast`. `type="button"` adalah default yang aman di dalam form. Untuk mengirim form, **setel `type="submit"`**. `size="icon"` hanya mengatur dimensi; tombol tanpa teks tetap membutuhkan `aria-label`.

```blade
<x-ui.button variant="outline" size="icon" aria-label="Buka pengaturan" data-modal-open="#settings-modal">
    <x-ui.icon name="settings" />
</x-ui.button>
```

### 🏷️ `x-ui.badge` {#x-ui-badge}

`x-ui.badge` menerima `variant` (default `neutral`), `pill`, `dot`, dan `icon`. Pilihan variant: `primary`, `soft`, `success`, `danger`, `warning`, `info`, `neutral`, `outline`, `solid-success`, `solid-danger`, `solid-warning`, `solid-info`. Badge menghasilkan `<span>`, bukan tombol; gunakan untuk menyatakan status, bukan sebagai elemen interaktif. `pill` dan `dot` harus berupa ekspresi Blade boolean, misalnya `:pill="true"`. Untuk status dari database, petakan nilai status ke salah satu variant tepercaya sebelum menampilkannya.

### 🧿 `x-ui.icon` {#x-ui-icon}

`name` wajib dan merujuk ID `i-{name}` di `public/icons.svg`, misalnya `name="check-circle"` menunjuk `i-check-circle`. Ukuran bawaan `h-4 w-4` dapat diganti lewat `class`. Ikon menggunakan `aria-hidden="true"`, sehingga teks penting tetap harus ditulis secara terlihat atau pada `aria-label` kontrol. Ini berbeda dari komponen lama `<x-icon>` yang memakai sprite inline `#i-...`; untuk halaman admin aktif gunakan `<x-ui.icon>`.

```blade
<x-ui.button type="submit" variant="primary" size="sm">Simpan</x-ui.button>
<x-ui.badge variant="success" :dot="true">Aktif</x-ui.badge>
<x-ui.icon name="check-circle" class="h-4 w-4" />
```

`x-ui.icon` mengambil simbol dari `public/icons.svg`. Jangan membangun nama ikon dari input pengguna; pilih nama simbol yang memang tersedia. Untuk tombol ikon saja, sertakan `aria-label` pada tombol.

## 📈 Kartu statistik {#kartu-statistik}

`x-ui.stat-card` membutuhkan `title`, `value`, `icon`, dan `delta`; pilihan `tone` (default `success`), `direction` (`up`/`down`), `note`, `tile`, dan `spark` (string JSON angka). `tile` adalah kelas CSS untuk bidang ikon. Sparkline dirender oleh `resources/js/admin/charts.js`.

```blade
<x-ui.stat-card
    title="Pesanan aktif" :value="number_format($activeOrders, 0, ',', '.')"
    icon="cart" delta="+8%" tone="success" direction="up"
    spark="[12,16,14,19,22]" />
```

Nilai perbandingan dan teks statistik perlu dihitung dari data Anda; tidak dilakukan otomatis oleh komponen. Lihat contoh halaman `resources/views/admin/dashboard.blade.php`.

`spark` hanya digambar ketika bernilai truthy. Berikan string JSON seperti `"[12,16,14]"`; untuk array dari controller gunakan `:spark="json_encode($weeklyOrders)"`. Jalur array pada implementasi memakai `Js::from()`, sedangkan JS grafik membaca `JSON.parse`, jadi array langsung perlu penyesuaian komponen. `direction` membentuk ikon `trend-up`/`trend-down`, sementara `tone` memengaruhi warna badge/sparkline. Detail ada di [referensi stat card](/komponen/stat-card). `note` defaultnya `dari bulan lalu`; perbarui bila periode berbeda.

::: info Dua versi kartu statistik
Ada pula `resources/views/components/admin/stat-card.blade.php` dengan API **berbeda** (`label`, `value`, `trend`, `href` dan array `spark`). Dashboard aktif menggunakan `x-ui.stat-card`; jangan menyalin prop `x-admin.stat-card` ke `x-ui.stat-card`.
:::
