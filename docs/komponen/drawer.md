# 🗄️ Drawer

<ComponentPreview name="drawer" :height="170" />

`<x-ui.drawer>` adalah dialog berbentuk panel samping dengan header, area isi yang bisa discroll, dan footer opsional. Sumber: `resources/views/components/ui/drawer.blade.php`.

## 🧩 API

| Prop/slot | Default | Fungsi |
| --- | --- | --- |
| `id` | wajib | ID dialog/selector trigger |
| `title` | wajib | Judul terhubung `aria-labelledby` |
| `description` | `null` | Teks di bawah judul |
| Slot utama | — | Konten scrollable |
| `footer` | kosong | Area tindakan bawah |

Drawer sudah memiliki tombol tutup X di header dengan `data-modal-close` dan label aksesibel. Tidak ada prop `size`/`tone` seperti [modal](/komponen/modal).

## 🔎 Filter sederhana

```blade
<x-ui.button variant="outline" data-modal-open="#filter-entri">Atur filter</x-ui.button>
<x-ui.drawer id="filter-entri" title="Filter entri" description="Pilih kriteria pencarian.">
    <p>Gunakan form untuk menerapkan filter pada server.</p>
    <x-slot:footer>
        <x-ui.button variant="outline" data-modal-close>Batal</x-ui.button>
    </x-slot:footer>
</x-ui.drawer>
```

## 📝 Form GET di dalam drawer

```blade
<x-ui.drawer id="filter-data" title="Filter data">
    <form id="filter-records" method="GET" action="{{ route('examples.records.index') }}" class="space-y-4">
        <x-ui.field name="status" label="Status">
            <x-ui.select name="status" placeholder="Semua status" :value="request('status')"
                :options="['aktif' => 'Aktif', 'draf' => 'Draf']" />
        </x-ui.field>
    </form>
    <x-slot:footer>
        <a href="{{ route('examples.records.index') }}" class="btn btn-outline">Hapus filter</a>
        <x-ui.button type="submit" form="filter-records">Terapkan</x-ui.button>
    </x-slot:footer>
</x-ui.drawer>
```

`form="filter-records"` menghubungkan tombol di footer yang berada di luar tag `<form>` ke form. Jangan memberi `data-modal-close` pada tombol Terapkan sehingga request GET selesai normal. Route dan variabel form sesuai [panduan tabel server](/integrasi/data).

## ⚙️ Buka/tutup dan batasan

Mekanismenya sama dengan modal: `data-modal-open="#filter-data"`, `data-modal-close`, Esc, dan klik backdrop. Konten tidak disimpan otomatis; URL query atau state JS adalah sumber filter sesungguhnya. `id` harus unik pada halaman.
