# 🕒 Timeline

<ComponentPreview name="timeline" :height="285" />

`<x-ui.timeline>` menampilkan urutan aktivitas dalam `<ol>` semantik. Sumber: `resources/views/components/ui/timeline.blade.php`.

## 🧩 Bentuk data

`items` adalah array; tiap elemen perlu `title`. `description`, `time`, `datetime` (ISO untuk atribut `<time>`), dan `tone` opsional. `tone="success"` memakai titik hijau, `danger` merah, selain itu warna utama.

## 📝 Contoh riwayat

```blade
<x-ui.timeline :items="[
    ['title' => 'Pesanan selesai', 'time' => 'Hari ini, 10.30', 'datetime' => '2026-10-03T10:30:00', 'tone' => 'success'],
    ['title' => 'Paket dikirim', 'time' => 'Kemarin', 'description' => 'Kurir menerima paket.'],
    ['title' => 'Pembayaran gagal', 'time' => '1 Okt 2026', 'tone' => 'danger'],
]" />
```

## 🔌 Dari relasi Eloquent

```php
$events = $order->events()->latest()->get()->map(fn ($event) => [
    'title' => $event->title,
    'description' => $event->description,
    'time' => $event->created_at->diffForHumans(),
    'datetime' => $event->created_at->toIso8601String(),
    'tone' => $event->is_error ? 'danger' : 'success',
])->all();
```

Urutan mengikuti array yang diberikan; pilih `latest()` untuk kronologi terbaru lebih dulu. Untuk daftar kosong, tampilkan `<x-ui.empty>` di luar timeline.
