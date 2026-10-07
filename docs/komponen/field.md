# 🧷 Field

<ComponentPreview name="field" :height="185" />

`<x-ui.field>` menggabungkan label, kontrol pada slot, pesan bantuan, dan error validasi Laravel. Sumber: `resources/views/components/ui/field.blade.php`.

## 🧩 Kontrak prop

| Prop | Default | Fungsi |
| --- | --- | --- |
| `label` | `null` | `<label>` di atas kontrol |
| `name` | `null` | Kunci error pada `$errors` dan asal ID otomatis |
| `help` | `null` | Petunjuk bila tidak ada error |
| `required` | `false` | Tanda bintang visual pada label |
| `class` | `''` | Kelas tambahan pembungkus |
| Slot utama | — | Kontrol input/select/textarea |

ID otomatis `f-{name}` menghapus kurung `[`/`]` menjadi tanda hubung. Jika memberi `id` manual, samakan `id` pada field dan kontrol di dalamnya. Atribut selain `class` **tidak** diteruskan ke pembungkus field karena implementasi memakai `$attributes->only('class')`.

## ⌨️ Input wajib dengan bantuan

```blade
<x-ui.field name="email" label="Alamat email" help="Kami akan mengirim tanda terima ke alamat ini." :required="true">
    <x-ui.input name="email" type="email" autocomplete="email" required />
</x-ui.field>
```

`required` pada field hanya bintang. Atribut `required` pada input dan aturan `required` di controller tetap diperlukan.

## 🔽 Select, textarea, dan grid

```blade
<div class="grid gap-4 sm:grid-cols-2">
    <x-ui.field name="category" label="Kategori">
        <x-ui.select name="category" placeholder="Pilih kategori"
            :options="['panduan' => 'Panduan', 'catatan' => 'Catatan']" />
    </x-ui.field>
    <x-ui.field name="summary" label="Ringkasan" class="sm:col-span-2">
        <x-ui.textarea name="summary" rows="4" />
    </x-ui.field>
</div>
```

## ❗ Pesan validasi server

```php
$request->validate(['email' => ['required', 'email']]);
```

Setelah redirect validasi, `x-ui.field name="email"` otomatis menampilkan `$errors->first('email')` **sebagai pengganti** `help`; [input](/komponen/input) otomatis menerapkan `aria-invalid`. Jika `name` field tidak sesuai dengan `name` kontrol, pesan error tidak terhubung. Untuk input array gunakan ID eksplisit yang unik pada field/kontrol.
