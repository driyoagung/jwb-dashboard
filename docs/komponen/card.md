# 🗂️ Card

`<x-ui.card>` membungkus konten dalam `<section class="card">`. File sumber: `resources/views/components/ui/card.blade.php`; contoh beragam kartu ada di `/components/cards`.

## 🧩 Prop dan slot

| Input | Default | Hasil |
| --- | --- | --- |
| `title` | `null` | Judul `<h2>` pada header |
| `description` | `null` | Teks di bawah judul |
| Slot utama | wajib untuk isi | `<div class="card-content">` |
| `header` | tidak ada | Header kustom; menggantikan judul dan deskripsi otomatis |
| `footer` | tidak ada | `<div class="card-footer">` |

`class`, `id`, dan atribut lain diterapkan ke `<section>` melalui `$attributes`. Header tidak ditampilkan bila `title`, `description`, dan slot `header` semuanya kosong.

## 📝 Kartu dasar dan footer

```blade
<x-ui.card title="Aktivitas terbaru" description="Perubahan sepanjang minggu ini.">
    <p>{{ $activitySummary }}</p>
    <x-slot:footer>
        <a href="{{ route('admin.analytics') }}" class="btn btn-outline btn-sm">Buka analitik</a>
    </x-slot:footer>
</x-ui.card>
```

## 🛠️ Header buatan sendiri

```blade
<x-ui.card class="min-w-0">
    <x-slot:header>
        <div class="flex items-center justify-between gap-3">
            <div><h2 class="card-title">Pesanan</h2><p class="card-desc">Periode berjalan</p></div>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline btn-sm">Lihat semua</a>
        </div>
    </x-slot:header>
    <p class="text-muted-foreground">{{ $orderSummary }}</p>
</x-ui.card>
```

Jangan berharap prop `title` tetap muncul ketika `header` kustom dipakai: slot `header` mengambil alih seluruh markup header.

## 🧱 Kartu tanpa header dan grid

```blade
<div class="grid gap-4 md:grid-cols-2">
    <x-ui.card class="min-w-0"><strong>Produk aktif</strong><p>{{ $activeProducts }}</p></x-ui.card>
    <x-ui.card class="min-w-0"><strong>Produk habis</strong><p>{{ $outOfStock }}</p></x-ui.card>
</div>
```

Kartu dengan konten `<table>` yang harus menempel ke tepi lebih tepat ditulis sebagai `<div class="card">` dengan header/overflow sendiri; `x-ui.card` selalu memberi `.card-content`. Gunakan [stat card](/komponen/stat-card) untuk data angka dengan delta dan ikon, bukan memaksa semua kartu memakai pola statistik.
