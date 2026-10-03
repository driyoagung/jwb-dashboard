# 📅 Date range

`<x-ui.date-range>` menyajikan preset 7/30 hari dan dua input tanggal. Sumber: `resources/views/components/ui/date-range.blade.php` dan `resources/js/admin/advanced-inputs.js`.

## 🧩 API

| Prop | Default | Fungsi |
| --- | --- | --- |
| `id` | wajib | Dasar ID `{id}-from` dan `{id}-to` |
| `label` | `Rentang tanggal` | Judul fieldset |
| Atribut root | — | Diteruskan ke `<fieldset data-date-range>` |

Tidak ada prop `from`, `to`, atau nama request bawaan. HTML yang dihasilkan tidak berisi `name` pada kedua input; preset hanya mengubah nilai di browser.

## 📆 Pemakaian demo dan beberapa instance

```blade
<x-ui.date-range id="periode-penjualan" label="Periode penjualan" />
<x-ui.date-range id="periode-kunjungan" label="Periode kunjungan" class="mt-6" />
```

`id` harus unik agar label “Dari” dan “Sampai” menunjuk input yang tepat. Menekan preset menetapkan hari terakhir sebagai hari ini dan hari pertama termasuk dalam rentang; “Hapus” mengosongkan keduanya.

## 🔗 Mengirim filter ke backend

Sebelum memakai dalam form GET, ubah markup komponen agar input `data-range-from` dan `data-range-to` memiliki `name` (misalnya `from`/`to`) dan `value` dari request. Penyesuaian yang diperlukan di komponen (ilustrasi):

```blade
<input id="{{ $id }}-from" name="from" type="date" value="{{ request('from') }}" class="input" data-range-from>
<input id="{{ $id }}-to" name="to" type="date" value="{{ request('to') }}" class="input" data-range-to>
```

Lalu letakkan komponen di `<form method="GET">` bersama tombol submit; tombol preset sendiri tidak mengirim request. Jika dua instance berada dalam form yang sama, gunakan **nama input berbeda** untuk tiap rentang.

## ✅ Validasi tanggal

```php
$filters = $request->validate([
    'from' => ['nullable', 'date'],
    'to' => ['nullable', 'date', 'after_or_equal:from'],
]);
```

Pesan kesalahan urutan yang muncul pada komponen hanya validasi browser. Query tanggal dapat memakai `whereDate('created_at', '>=', $filters['from'])` dan batas akhir serupa setelah nilai diperiksa.
