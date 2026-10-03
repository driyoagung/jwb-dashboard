# 🔀 Switch

`<x-ui.switch>` adalah checkbox berpenampilan toggle untuk nilai on/off. Sumber: `resources/views/components/ui/switch.blade.php`.

## 🧩 Prop dan HTML yang dikirim

| Prop | Default | Fungsi |
| --- | --- | --- |
| `name` | wajib | Nama hidden input dan checkbox |
| `label` | `null` | Teks label klik |
| `checked` | `false` | Kondisi awal; `old(name)` lebih diprioritaskan |
| `value` | `1` | Nilai checkbox jika aktif |
| `hint` | `null` | Penjelasan di bawah label |

Komponen menghasilkan `<input type="hidden" name="..." value="0">` diikuti checkbox dengan `name` sama. Saat mati server menerima `0`; saat aktif browser mengirim `0` dan nilai checkbox (`1` bawaan), lalu Laravel membaca nilai terakhir untuk nama tersebut. ID otomatis `f-{name}` bisa diganti dengan atribut `id`.

## ✅ Dasar dan status dari model

```blade
<x-ui.switch name="notifications" label="Terima notifikasi" hint="Kami akan mengirim pembaruan penting." />
<x-ui.switch name="published" label="Terbitkan entri" :checked="(bool) $record->published" />
```

Di controller:

```php
$data = $request->validate(['published' => ['required', 'boolean']]);
$record->update(['published' => $request->boolean('published')]);
```

## 🧾 Dalam form dan ID khusus

```blade
<form method="POST" action="{{ route('settings.update') }}">
    @csrf
    <x-ui.switch id="email-updates" name="email_updates" label="Pembaruan email"
        :checked="(bool) ($settings->email_updates ?? false)" />
    <x-ui.button type="submit">Simpan</x-ui.button>
</form>
```

Route `settings.update` adalah contoh route yang perlu Anda buat; route pengaturan bawaan saat ini `Route::view` (GET). Jika ingin mematikan kontrol, atribut `disabled` pada komponen hanya diteruskan ke checkbox; hidden input tetap terkirim `0`. Tangani konsekuensi itu secara eksplisit saat mengembangkan form.
