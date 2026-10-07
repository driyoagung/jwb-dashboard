# 🪟 Modal

<ComponentPreview name="modal" :height="170" />

`<x-ui.modal>` membuat `<dialog>` untuk konfirmasi atau tindakan yang perlu perhatian khusus. Sumber: `resources/views/components/ui/modal.blade.php`; buka/tutup melalui `resources/js/admin/interactions.js`.

## 🧩 Prop dan slot

| Prop/slot | Default | Fungsi |
| --- | --- | --- |
| `id` | wajib | ID unik dialog dan target tombol pembuka |
| `title` | wajib | Judul dan label dialog |
| `description` | `null` | Keterangan; terhubung via `aria-describedby` |
| `icon` | `null` | Nama ikon di sisi judul |
| `tone` | `info` | Warna bidang ikon, misalnya `info`, `danger`, `success` |
| `size` | `lg` | `sm` → `max-w-md`, `lg` → `max-w-lg`, `xl` → `max-w-2xl` |
| Slot utama/`footer` | opsional | Isi dan kelompok tombol bawah |

Atribut lain seperti `class` diterapkan ke `<dialog>`. `tone` hanya dipakai saat `icon` ada; ukuran selain `sm`/`xl` menjadi ukuran default.

## 📖 Modal informasi dan ukuran

```blade
<x-ui.button variant="outline" data-modal-open="#info">Lihat informasi</x-ui.button>
<x-ui.modal id="info" title="Jadwal pembaruan" description="Aplikasi tetap dapat diakses." icon="info" size="sm">
    <p>Pembaruan dijadwalkan Minggu pukul 01.00 WIB.</p>
    <x-slot:footer>
        <x-ui.button data-modal-close>Mengerti</x-ui.button>
    </x-slot:footer>
</x-ui.modal>
```

## 🗑️ Konfirmasi hapus yang sungguhan

```blade
<x-ui.button variant="destructive" data-modal-open="#hapus-entri">Hapus</x-ui.button>
<x-ui.modal id="hapus-entri" title="Hapus entri?" description="Tindakan ini tidak dapat dibatalkan."
    icon="trash" tone="danger" size="sm">
    <p>Entri {{ $record->title }} akan dihapus.</p>
    <x-slot:footer>
        <x-ui.button variant="outline" data-modal-close>Batal</x-ui.button>
        <form method="POST" action="{{ route('examples.records.destroy', $record) }}">
            @csrf
            @method('DELETE')
            <x-ui.button type="submit" variant="destructive">Ya, hapus</x-ui.button>
        </form>
    </x-slot:footer>
</x-ui.modal>
```

Route `destroy` mengikuti [contoh CRUD](/integrasi/crud). Tombol submit **tidak** diberi `data-modal-close` atau toast sukses sebelum respons server. Setelah redirect, tampilkan pesan flash dari controller.

## ⚙️ Perilaku dialog

Klik elemen dengan `data-modal-open="#id"` memanggil `showModal()`; `data-modal-close`, Esc, dan klik backdrop menutup dengan animasi. Pastikan `id` unik. Fokus/penutupan bawaan `<dialog>` dibantu browser; dialog sendiri tidak otomatis membuat submit form ataupun validasi server. Untuk panel samping gunakan [drawer](/komponen/drawer).
