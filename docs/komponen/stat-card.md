# 📊 Stat card

`<x-ui.stat-card>` menampilkan angka KPI, ikon, delta, perbandingan, dan sparkline opsional. Sumber: `resources/views/components/ui/stat-card.blade.php`; contoh pada `/dashboard` dan `/components/cards`.

## 🧩 Prop

| Prop | Default | Fungsi |
| --- | --- | --- |
| `title`, `value`, `icon`, `delta` | wajib | Label, angka, nama ikon, teks perubahan |
| `tile` | `bg-soft text-soft-foreground` | Kelas warna bidang ikon |
| `tone` | `success` | Warna badge/sparkline (`success`, `danger` pada contoh) |
| `direction` | `up` | Ikon `trend-up` atau `trend-down` |
| `note` | `dari bulan lalu` | Penjelasan periode/perbandingan |
| `spark` | `null` | String JSON angka untuk tren (jalur array perlu penyesuaian) |

Atribut tambahan pada tag diteruskan ke pembungkus kartu. `delta` selalu ditampilkan meskipun bernilai kosong; atur isinya sesuai perbandingan yang sebenarnya.

## 📈 Pertumbuhan dan penurunan

```blade
<div class="grid gap-4 sm:grid-cols-2">
    <x-ui.stat-card title="Pelanggan baru" value="1.284" icon="userplus"
        delta="+8,1%" tone="success" direction="up" note="dari bulan lalu" />
    <x-ui.stat-card title="Pesanan aktif" value="356" icon="cart"
        delta="−2,4%" tone="danger" direction="down"
        tile="bg-warning-soft text-warning" note="dari minggu lalu" />
</div>
```

`tone` adalah **warna penyajian**, bukan rumus sentimen bisnis: penurunan pengembalian dapat berarti kabar baik, jadi pilih warna berdasarkan makna metrik, bukan hanya arah panah.

## 📉 Sparkline dan nilai controller

```php
return view('admin.dashboard', [
    'activeOrders' => 356,
    'weeklyOrders' => [42, 50, 47, 53, 61, 58, 66],
]);
```

```blade
<x-ui.stat-card title="Pesanan aktif" :value="number_format($activeOrders, 0, ',', '.')"
    icon="cart" delta="+8%" note="7 hari terakhir" :spark="json_encode($weeklyOrders)" />
```

Berikan `spark` sebagai **string JSON angka** agar `charts.js` dapat membaca `data-values`. Jalur array di komponen memakai `Js::from()`, yang menghasilkan ekspresi JavaScript alih-alih string JSON biasa; karena `charts.js` memakai `JSON.parse`, gunakan `json_encode` seperti contoh untuk data dari controller. Berikan urutan kronologis angka; hindari array kosong atau nilai tidak numerik.

::: info Komponen statistik lain
`x-admin.stat-card` juga ada tetapi **API-nya berbeda** (`label`, `trend`, `href`, array `spark` menggambar SVG langsung). Halaman dashboard aktif memakai `x-ui.stat-card`; panduan ini mengikuti versi UI tersebut.
:::
