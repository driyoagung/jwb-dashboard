# 🗂️ Card

<ComponentPreview name="card" :height="960" />

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

## 📊 Kartu target dan saldo

Keduanya adalah **pola HTML/CSS**, bukan prop `x-ui.card`. Halaman `/components/cards` memakai pola berikut; Anda bisa mengganti angka dengan variabel controller:

```blade
<div class="grid gap-4 sm:grid-cols-2">
    <div class="card p-6">
        <p class="text-sm font-medium text-muted-foreground">Target bulan ini</p>
        <p class="mt-2 text-2xl font-semibold tabular-nums">78%</p>
        <div class="progress mt-4"><div class="progress-bar" style="width:78%"></div></div>
        <p class="mt-2 text-xs text-muted-foreground">Rp 58,4 jt dari Rp 75 jt</p>
    </div>
    <div class="rounded-xl bg-primary p-6 text-primary-foreground">
        <p class="text-sm font-medium">Saldo tersedia</p>
        <p class="mt-2 text-2xl font-semibold tabular-nums">Rp 12,8 jt</p>
        <button type="button" class="btn mt-4 bg-primary-foreground/15 text-primary-foreground hover:bg-primary-foreground/25">Tarik dana</button>
    </div>
</div>
```

Untuk bilah progres dinamis, batasi persentase di PHP ke rentang `0–100`, misalnya `$progress = min(100, max(0, $progress))`, lalu gunakan `style="width: {{ $progress }}%"`. Saldo berwarna memakai pasangan token `bg-primary` dan `text-primary-foreground` agar label terbaca pada tema terang maupun gelap.

## 🧾 Kartu profil dan aktivitas

```blade
<div class="grid gap-4 md:grid-cols-2">
    <div class="card p-6">
        <span class="avatar avatar-lg">AR</span>
        <h3 class="mt-3 font-semibold">Ayu Rahmawati</h3>
        <p class="text-sm text-muted-foreground">Manajer toko · Yogyakarta</p>
        <a href="{{ route('admin.settings') }}" class="btn btn-outline mt-4">Lihat profil</a>
    </div>
    <div class="card p-6">
        <h3 class="font-semibold">Aktivitas terbaru</h3>
        <ul class="mt-4 space-y-3">
            @foreach (config('kenanga.demo.activity') as $item)
                <li class="flex items-start gap-3"><x-ui.icon :name="$item['icon']" />
                    <span>{{ $item['text'] }} <small class="text-muted-foreground">{{ $item['time'] }}</small></span>
                </li>
            @endforeach
        </ul>
    </div>
</div>
```

Ganti `config('kenanga.demo.activity')` dengan data riwayat nyata dari controller. Jika ingin bentuk riwayat vertikal siap pakai, gunakan [timeline](/komponen/timeline).

## 🛍️ Kartu produk dan harga

```blade
<div class="grid gap-4 md:grid-cols-2">
    <div class="card overflow-hidden">
        <div class="flex h-36 items-center justify-center bg-soft text-soft-foreground"><x-ui.icon name="coffee" class="h-10 w-10" /></div>
        <div class="p-4"><p class="text-xs text-muted-foreground">Biji kopi</p>
            <h3 class="font-semibold">Arabika Gayo 1 kg</h3>
            <p class="mt-3 font-semibold">Rp 170.000</p>
            <button type="button" class="btn btn-outline mt-3 w-full">Tambah ke keranjang</button>
        </div>
    </div>
    <div class="card p-6">
        <h3 class="font-semibold">Paket Pro</h3>
        <p class="mt-1 text-sm text-muted-foreground">Untuk toko yang berkembang.</p>
        <p class="mt-4 text-3xl font-semibold">Rp 149 rb <span class="text-sm text-muted-foreground">/ bulan</span></p>
        <ul class="mt-5 space-y-2 text-sm"><li>Produk tak terbatas</li><li>Analitik lanjutan</li></ul>
        <button type="button" class="btn btn-primary mt-6 w-full">Pilih Pro</button>
    </div>
</div>
```

## 🎛️ Outline putus-putus dan kartu soft

```blade
<div class="grid gap-4 sm:grid-cols-2">
    <div class="rounded-xl border border-dashed p-6">
        <p class="text-sm text-muted-foreground">Outline putus-putus</p>
        <p class="mt-2 text-2xl font-semibold tabular-nums">2.481</p>
    </div>
    <div class="rounded-xl bg-success-soft p-6 text-success">
        <p class="text-sm">Pertumbuhan tahunan</p>
        <p class="mt-2 text-2xl font-semibold tabular-nums">+24,8%</p>
    </div>
</div>
```

## ✅ Tugas dan pesan masuk

```blade
<div class="grid gap-4 md:grid-cols-2">
    <x-ui.card title="Tugas hari ini" description="Centang tugas yang telah selesai.">
        <ul class="space-y-3">
            <li><label class="flex items-center gap-2"><input type="checkbox" /> Kemas pesanan</label></li>
            <li><label class="flex items-center gap-2"><input type="checkbox" /> Restok produk</label></li>
        </ul>
    </x-ui.card>
    <x-ui.card title="Pesan masuk">
        <ul class="divide-y">
            @foreach (config('kenanga.demo.users') as $user)
                <li class="py-2"><strong>{{ $user['name'] }}</strong><p class="text-muted-foreground">{{ $user['email'] }}</p></li>
            @endforeach
        </ul>
    </x-ui.card>
</div>
```

Checklist ini belum tersimpan; saat dihubungkan ke backend, gunakan `name`/`value` dan form atau endpoint update. Untuk inbox sungguhan, ganti data demo dengan koleksi pesan dari controller.

## 💬 Kutipan dan kartu horizontal

```blade
<div class="grid gap-4 md:grid-cols-2">
    <figure class="card p-6">
        <blockquote>“Dashboard ini bikin rekap harian jauh lebih cepat.”</blockquote>
        <figcaption class="mt-4 text-sm text-muted-foreground">Hendra Wijaya · Pemilik Kopi Kenanga</figcaption>
    </figure>
    <div class="card flex overflow-hidden">
        <span class="flex w-24 shrink-0 items-center justify-center bg-soft text-soft-foreground"><x-ui.icon name="truck" class="h-8 w-8" /></span>
        <div class="p-6"><h3 class="font-semibold">Gratis ongkir</h3><p class="text-muted-foreground">Minimal belanja Rp 250.000.</p></div>
    </div>
</div>
```

## 📭 Kartu tanpa isi

```blade
<div class="card"><x-ui.empty icon="file-text" title="Belum ada laporan"
    description="Buat laporan pertama untuk melihat ringkasan penjualan.">
    <a href="{{ route('admin.analytics') }}" class="btn btn-primary">Buka analitik</a>
</x-ui.empty></div>
```

Seluruh pola di atas memakai kelas CSS dashboard yang sama dengan `/components/cards`. API `<x-ui.card>` tetap hanya `title`, `description`, `header`, `footer`, dan slot utama; kartu produk, harga, dan horizontal menggunakan markup khusus agar komposisinya sesuai kebutuhan.
