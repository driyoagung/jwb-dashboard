# 📑 Detail list

`<x-ui.detail-list>` menampilkan pasangan label/nilai dalam `<dl>` semantik. Cocok untuk detail entri, profil, atau metadata pesanan. Sumber: `resources/views/components/ui/detail-list.blade.php`.

## 🧩 API

| Prop/slot | Default | Kegunaan |
| --- | --- | --- |
| `title` | `null` | Judul bagian |
| `items` | `[]` | Array `['label' => ..., 'value' => ...]` |
| `columns` | `2` | `2` menghasilkan dua kolom pada layar `sm`; `1` satu kolom |
| Slot utama | — | Baris `<div><dt>…</dt><dd>…</dd></div>` tambahan |

Nilai dirender sebagai teks Blade dan di-escape. Jika `value` null/tidak tersedia, komponen menampilkan `—`.

## 📝 Detail dari controller

```blade
<x-ui.detail-list title="Informasi entri" :items="[
    ['label' => 'Kode', 'value' => $record->code],
    ['label' => 'Judul', 'value' => $record->title],
    ['label' => 'Status', 'value' => ucfirst($record->status)],
]" />
```

## 📐 Satu kolom dan nilai kustom

```blade
<x-ui.detail-list title="Informasi pembayaran" :columns="1" :items="[
    ['label' => 'Metode', 'value' => 'Transfer bank'],
    ['label' => 'Nomor referensi', 'value' => $payment->reference],
]">
    <div><dt class="text-xs text-muted-foreground">Total</dt><dd class="mt-1 font-semibold">Rp {{ number_format($payment->amount, 0, ',', '.') }}</dd></div>
</x-ui.detail-list>
```

Format uang/tanggal di controller atau saat membangun array; komponen tidak melakukan format domain. Slot membolehkan badge/tautan di dalam `<dd>` bila nilai perlu markup.
