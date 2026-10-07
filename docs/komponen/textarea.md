# 📝 Textarea

<ComponentPreview name="textarea" :height="245" />

`<x-ui.textarea>` menampilkan input multi-baris dengan nilai lama `old()` dan status invalid otomatis. Sumber: `resources/views/components/ui/textarea.blade.php`.

## 🧩 API

| Prop | Default | Fungsi |
| --- | --- | --- |
| `name` | `null` | Nama request dan ID otomatis |
| `value` | `null` | Isi awal, dikalahkan `old(name)` |
| `rows` | `4` | Jumlah baris awal |
| `invalid` | `null` | Paksa status invalid |

Tambahkan atribut HTML seperti `placeholder`, `maxlength`, `required`, `readonly` atau `class` langsung pada tag komponen. Isi elemen dihasilkan dari prop `value`, **bukan** isi slot.

## 📝 Form buat dan edit

```blade
<x-ui.field name="summary" label="Ringkasan" help="Satu atau dua kalimat.">
    <x-ui.textarea name="summary" rows="4" :value="$record?->summary"
        placeholder="Tulis ringkasan entri…" />
</x-ui.field>
```

Ketika validasi controller gagal dan redirect, `old('summary')` menggantikan `$record?->summary` sehingga ketikan pengguna tidak hilang.

## 📏 Teks panjang dan read-only

```blade
<x-ui.field name="notes" label="Catatan internal">
    <x-ui.textarea name="notes" rows="8" maxlength="2000" :value="$record?->notes" />
</x-ui.field>
<x-ui.textarea name="preview" :value="$previewText" rows="5" readonly aria-label="Pratinjau" />
```

`maxlength` di browser adalah bantuan, bukan pengganti validasi server seperti `'notes' => ['nullable', 'string', 'max:2000']`. Jika error tersedia pada `$errors`, textarea mendapat `aria-invalid="true"`; teks error ditampilkan oleh [field](/komponen/field).
