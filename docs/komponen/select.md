# 🔽 Select

<ComponentPreview name="select" :height="330" />

`<x-ui.select>` membungkus `<select>` HTML dan mendukung pilihan dari array PHP, placeholder, nilai lama dari form, serta error Laravel. Sumber: `resources/views/components/ui/select.blade.php`.

## 🧩 Prop

| Prop | Default | Fungsi |
| --- | --- | --- |
| `name` | `null` | Nama request dan ID otomatis |
| `value` | `null` | Pilihan awal; dikalahkan `old(name)` |
| `options` | `[]` | Peta nilai → label (`['aktif' => 'Aktif']`) |
| `placeholder` | `null` | Opsi kosong dengan teks sendiri |
| `includeBlank` | `false` | Opsi kosong bertuliskan `— Pilih —` |
| `invalid` | `null` | Paksa tampilan invalid |
| `searchable` | `false` | Aktifkan Choices.js untuk pencarian dan dropdown non-native |
| Slot utama | kosong | Tambahan `<option>` manual |

ID otomatis adalah `f-{name}`; atribut `required`, `disabled`, `multiple`, `aria-label`, `class`, dan `id` diteruskan ke `<select>`. Komponen mengubah nilai opsi dan `old()` menjadi string saat membandingkan.

## 📝 Pilihan tunggal dan placeholder

```blade
<x-ui.field name="status" label="Status" :required="true">
    <x-ui.select name="status" placeholder="Pilih status" required
        :value="$record?->status"
        :options="['draf' => 'Draf', 'ditinjau' => 'Ditinjau', 'aktif' => 'Aktif']" />
</x-ui.field>
```

Saat memakai `required`, opsi kosong membantu browser mendeteksi pilihan yang belum dibuat. Tanpa placeholder/includeBlank, opsi pertama langsung menjadi pilihan bawaan browser.

## 🗃️ Pilihan dari database

```php
$categories = Category::query()->orderBy('name')->pluck('name', 'id')->all();
return view('admin.products.create', compact('categories'));
```

```blade
<x-ui.select name="category_id" placeholder="Pilih kategori"
    :value="$product?->category_id" :options="$categories" />
```

Model `Category` dan view pada contoh harus Anda buat sendiri. Gunakan validator `exists:categories,id` di server; prop options tidak memvalidasi request.

## 🧰 Slot opsi dan batasan

```blade
<x-ui.select name="period" :include-blank="true" aria-label="Periode">
    <option value="week" @selected(old('period') === 'week')>7 hari</option>
    <option value="month" @selected(old('period') === 'month')>30 hari</option>
</x-ui.select>
```

Opsi dalam slot tidak otomatis diberi `@selected` oleh komponen: atur sendiri. `multiple` hanya meneruskan atribut HTML; logika pemilihan `old()` bawaan dirancang untuk **satu** nilai. Untuk multi-select, sesuaikan komponen sebelum menggunakan array nilai.

## 🔎 Select yang dapat dicari (Choices.js)

```blade
<x-ui.field name="category" label="Kategori">
    <x-ui.select name="category" placeholder="Pilih kategori" :searchable="true"
        :value="old('category', $record?->category)"
        :options="['panduan' => 'Panduan', 'laporan' => 'Laporan', 'arsip' => 'Arsip']" />
</x-ui.field>
```

Choices.js meningkatkan select secara progresif: option dan `name` tetap berasal dari `<select>` Blade, sehingga request Laravel masih berisi `category`. Pencarian berlangsung di browser dan dropdown menggunakan token terang/gelap dashboard. Tanpa JS, select HTML tetap berfungsi. Penggunaan di dalam modal dapat memerlukan penempatan dropdown khusus bila terpotong oleh `<dialog>`; uji konteks tempat Anda menaruhnya.
