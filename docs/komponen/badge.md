# 🏷️ Badge

`<x-ui.badge>` menampilkan label status sebagai `<span>`; bukan tautan atau tombol. Sumber: `resources/views/components/ui/badge.blade.php`.

## 🧩 Prop dan pilihan variant

| Prop | Default | Fungsi |
| --- | --- | --- |
| `variant` | `neutral` | `primary`, `soft`, `success`, `danger`, `warning`, `info`, `neutral`, `outline`, `solid-success`, `solid-danger`, `solid-warning`, `solid-info` |
| `pill` | `false` | Bentuk ujung membulat penuh |
| `dot` | `false` | Tanda titik status |
| `icon` | `null` | Nama ikon dari sprite |
| Slot utama | — | Teks label |

Variant tidak dikenal kembali ke `neutral`. `class`, `title` dan atribut HTML lain diteruskan ke elemen `<span>`.

## 🎨 Contoh semua keluarga gaya

```blade
<x-ui.badge variant="primary">Utama</x-ui.badge>
<x-ui.badge variant="soft">Sorotan</x-ui.badge>
<x-ui.badge variant="success">Aktif</x-ui.badge>
<x-ui.badge variant="danger">Gagal</x-ui.badge>
<x-ui.badge variant="warning">Menunggu</x-ui.badge>
<x-ui.badge variant="info">Dalam proses</x-ui.badge>
<x-ui.badge variant="neutral">Arsip</x-ui.badge>
<x-ui.badge variant="outline">Draf</x-ui.badge>
<x-ui.badge variant="solid-success">Selesai</x-ui.badge>
<x-ui.badge variant="solid-danger">Dibatalkan</x-ui.badge>
<x-ui.badge variant="solid-warning">Perlu tinjauan</x-ui.badge>
<x-ui.badge variant="solid-info">Dikirim</x-ui.badge>
```

## ⚙️ Kombinasi pill, dot, ikon

```blade
<x-ui.badge variant="success" :dot="true">Online</x-ui.badge>
<x-ui.badge variant="warning" :pill="true">Menunggu pembayaran</x-ui.badge>
<x-ui.badge variant="info" icon="truck" :pill="true">Dikirim</x-ui.badge>
```

Gunakan sintaks `:dot="true"` dan `:pill="true"` agar Blade mengirim boolean, bukan string `"false"` yang tetap truthy di PHP.

## 🗃️ Status dari database

```blade
@php
    $tone = match ($record->status) {
        'aktif' => 'success',
        'ditinjau' => 'info',
        'draf' => 'warning',
        default => 'neutral',
    };
@endphp
<x-ui.badge :variant="$tone" :dot="true">{{ ucfirst($record->status) }}</x-ui.badge>
```

Petakan nilai domain ke variant yang ada; jangan menjadikan input user langsung sebagai class CSS. Jelaskan status melalui teks, bukan warna atau titik saja. Lihat [`/components/buttons`](/panduan/mulai#halaman-yang-tersedia) untuk showcase tombol dan lencana.
