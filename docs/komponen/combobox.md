# 🔎 Combobox

`<x-ui.combobox>` menyediakan pencarian opsi lokal dengan input teks dan input tersembunyi untuk nilai form. Sumber Blade: `resources/views/components/ui/combobox.blade.php`; perilaku: `resources/js/admin/advanced-inputs.js`.

## 🧩 Prop dan bentuk opsi

| Prop | Default | Fungsi |
| --- | --- | --- |
| `id` | wajib | ID unik untuk input, list, dan opsi |
| `label` | wajib | Label input dan listbox |
| `options` | `[]` | Array string atau array berisi `value`/`label` |
| `name` | `null` | Nama input tersembunyi yang dikirim ke server |
| `placeholder` | `Cari dan pilih…` | Teks input pencarian |

`class` dan atribut root lain diteruskan ke pembungkus `data-combobox`. Atribut `required` pada root **tidak** otomatis memberi validasi pada hidden input; validasi server tetap wajib.

## 📝 Opsi string atau value/label

```blade
<x-ui.combobox id="kota" label="Kota" name="city"
    :options="['Bandung', 'Yogyakarta', 'Surabaya']" />

<x-ui.combobox id="kategori" label="Kategori" name="category_id"
    placeholder="Cari kategori…" :options="[
        ['value' => '1', 'label' => 'Panduan'],
        ['value' => '2', 'label' => 'Laporan'],
    ]" />
```

Pada array string, teks dipakai sebagai nilai sekaligus label. Pada array objek PHP, `value` disimpan ke input tersembunyi dan `label` tampil di input teks. Jangan gunakan ID `id` yang sama untuk dua combobox pada satu halaman.

## ⌨️ Navigasi keyboard dan form

Fokus/ketikan membuka daftar. Ketik untuk menyaring, gunakan Arrow Up/Down untuk opsi, Enter untuk memilih, Esc/Tab untuk menutup. Memilih opsi memicu `change` pada hidden input; mengetik ulang mengosongkan nilai sebelumnya. Di server, contoh validasi kategori: `['category_id' => ['required', 'exists:categories,id']]`.

## 🛠️ Opsi database dan keterbatasan edit

```php
$options = Category::query()->orderBy('name')->get(['id', 'name'])
    ->map(fn ($category) => ['value' => (string) $category->id, 'label' => $category->name])
    ->all();
```

```blade
<x-ui.combobox id="kategori-produk" label="Kategori" name="category_id" :options="$options" />
```

Model `Category` contoh perlu Anda buat. Komponen belum menerima `value` awal/`old()`; untuk halaman edit, perluas Blade dan skrip agar label serta hidden value awal sinkron. Semua opsi dirender di HTML lalu disaring di browser, bukan dicari dari API; untuk ribuan pilihan pertimbangkan pencarian server tersendiri.
