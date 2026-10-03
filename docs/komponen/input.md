# ⌨️ Input

`<x-ui.input>` merender `<input>` (atau input di dalam `.input-group` bila ada `icon`/`addon`) dengan dukungan `old()` dan error Laravel. Sumber: `resources/views/components/ui/input.blade.php`.

## 🧩 Prop dan atribut

| Prop | Default | Fungsi |
| --- | --- | --- |
| `name` | `null` | Nama field dan dasar ID otomatis |
| `value` | `null` | Nilai awal; ditimpa `old(name)` setelah validasi |
| `type` | `text` | Tipe input HTML |
| `invalid` | `null` | Paksa status invalid; jika null gunakan `$errors->has(name)` |
| `icon` | `null` | Ikon di kiri input |
| `addon` | `null` | Label teks di kiri input |

Atribut `placeholder`, `autocomplete`, `min`, `max`, `step`, `required`, `readonly`, `disabled`, dan kelas CSS diteruskan ke `<input>`. Berikan `id` bila tidak ingin memakai ID `f-{name}` bawaan.

## 📝 Teks dan email

```blade
<x-ui.field name="title" label="Judul" :required="true">
    <x-ui.input name="title" :value="$record?->title" placeholder="Contoh: Panduan tim" required />
</x-ui.field>
<x-ui.field name="email" label="Email">
    <x-ui.input name="email" type="email" autocomplete="email" icon="mail" />
</x-ui.field>
```

`icon` menunjukkan dekorasi input, bukan label. `<x-ui.field>` atau label HTML terpisah tetap diperlukan agar kontrol memiliki nama yang jelas.

## 💰 Angka, addon, dan validasi

```blade
<x-ui.field name="price" label="Harga">
    <x-ui.input name="price" type="number" addon="Rp" min="0" step="1" :value="$product?->price" />
</x-ui.field>
<x-ui.input name="sku" value="KP-001" readonly />
<x-ui.input name="serial" value="ABC-001" disabled />
```

`addon` hanyalah label visual; request `price` tetap harus berisi angka dan divalidasi di server. `disabled` tidak dikirim dalam form, sedangkan `readonly` tetap dikirim. Saat `invalid` true atau `$errors` memiliki pesan, komponen menambah `aria-invalid="true"` dan `input-invalid`; pesan teksnya ditampilkan oleh [field](/komponen/field).

## 📎 File dan batasan

```blade
<x-ui.input name="attachment" type="file" accept="image/png,image/jpeg" />
```

Untuk `type="file"` komponen sengaja tidak mengisi `value`; browser melarang mengisi path file secara otomatis. Pastikan form memakai `enctype="multipart/form-data"` dan validasi server. Untuk area seret/thumbnail lokal gunakan [file preview](/komponen/file-preview).
