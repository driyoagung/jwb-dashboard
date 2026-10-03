# 📅 Date range

`<x-ui.date-range>` menyajikan preset 7/30 hari dan dua input tanggal yang ditingkatkan oleh Flatpickr. Sumber: `resources/views/components/ui/date-range.blade.php`, `resources/js/admin/advanced-inputs.js`, dan `enhanced-controls.js`.

## 🧩 API

| Prop | Default | Fungsi |
| --- | --- | --- |
| `id` | wajib | Dasar ID `{id}-from` dan `{id}-to` |
| `label` | `Rentang tanggal` | Judul fieldset |
| `fromName`, `toName` | `null` | Nama request untuk awal/akhir |
| `from`, `to` | `null` | Nilai awal ISO `Y-m-d`; `old()` diprioritaskan bila nama ada |
| Atribut root | — | Diteruskan ke `<fieldset data-date-range>` |

Jika `fromName`/`toName` tidak diisi, input tidak memiliki `name` dan hanya menjadi demo lokal. Nilai yang dikirim form tetap `Y-m-d` meskipun label kalender ditampilkan dengan nama bulan bahasa Indonesia. Tanpa JS, input teks meminta tanggal ISO; validasi server tetap diperlukan.

## 📆 Pemakaian demo dan beberapa instance

```blade
<x-ui.date-range id="periode-penjualan" label="Periode penjualan" from-name="from" to-name="to" />
<x-ui.date-range id="periode-kunjungan" label="Periode kunjungan" class="mt-6" />
```

`id` harus unik agar label “Dari” dan “Sampai” menunjuk input yang tepat. Menekan preset menetapkan hari terakhir sebagai hari ini dan hari pertama termasuk dalam rentang; “Hapus” mengosongkan keduanya.

## 🔗 Mengirim filter ke backend

Untuk memakai dalam form GET, isi nama field dan nilai awal:

```blade
<form method="GET" action="{{ route('examples.records.index') }}">
    <x-ui.date-range id="periode-filter" from-name="from" to-name="to"
        :from="request('from')" :to="request('to')" />
    <x-ui.button type="submit">Terapkan</x-ui.button>
</form>
```

Tombol preset sendiri tidak mengirim request. Jika dua instance berada dalam form yang sama, gunakan **nama input berbeda** untuk tiap rentang. Tombol Hapus membersihkan kedua tanggal tetapi tetap memerlukan submit untuk memperbarui daftar server.

## ✅ Validasi tanggal

```php
$filters = $request->validate([
    'from' => ['nullable', 'date'],
    'to' => ['nullable', 'date', 'after_or_equal:from'],
]);
```

Pesan kesalahan urutan yang muncul pada komponen hanya validasi browser. Query tanggal dapat memakai `whereDate('created_at', '>=', $filters['from'])` dan batas akhir serupa setelah nilai diperiksa.
