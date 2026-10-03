# 🛡️ Confirm action

`<x-ui.confirm-action>` membungkus [modal](/komponen/modal) dengan form Laravel untuk tindakan yang memerlukan konfirmasi. Sumber: `resources/views/components/ui/confirm-action.blade.php`.

## 🧩 Prop

| Prop | Default | Kegunaan |
| --- | --- | --- |
| `id`, `title`, `description`, `action` | wajib | Identitas dialog, teks, URL form |
| `method` | `DELETE` | HTTP method lewat `@method` bila bukan POST |
| `label` | `Hapus` | Teks tombol kirim |
| `tone` | `destructive` | `destructive` atau `primary` |
| Slot utama | — | Penjelasan tambahan dalam dialog |

Form selalu memakai method HTML POST dan `@csrf`; untuk DELETE/PUT/PATCH Blade menambahkan spoofing `@method`. Pastikan route sesuai method dan lakukan otorisasi di controller.

## 🗑️ Konfirmasi hapus

```blade
<x-ui.button variant="destructive" data-modal-open="#delete-record">Hapus</x-ui.button>
<x-ui.confirm-action id="delete-record" title="Hapus entri?"
    description="Tindakan ini tidak dapat dibatalkan."
    :action="route('examples.records.destroy', $record)">
    <p>Entri {{ $record->title }} akan dihapus permanen.</p>
</x-ui.confirm-action>
```

## ✅ Konfirmasi tindakan non-destruktif

```blade
<x-ui.button data-modal-open="#publish-record">Terbitkan</x-ui.button>
<x-ui.confirm-action id="publish-record" title="Terbitkan entri?"
    description="Entri akan terlihat oleh tim." :action="route('records.publish', $record)"
    method="POST" label="Ya, terbitkan" tone="primary" />
```

Route `records.publish` ilustratif dan perlu Anda buat. Jangan mengandalkan modal untuk izin atau validasi; lakukan di server. Demo `/components/data-patterns` memakai POST ke route pratinjau yang hanya redirect dan tidak menghapus data.
