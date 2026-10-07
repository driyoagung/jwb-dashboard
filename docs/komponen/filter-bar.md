# 🔎 Filter bar

<ComponentPreview name="filter-bar" :height="310" />

`<x-ui.filter-bar>` adalah form GET reusable untuk pencarian dan filter halaman daftar. Sumber: `resources/views/components/ui/filter-bar.blade.php`.

## 🧩 Prop & slot

| Input | Default | Kegunaan |
| --- | --- | --- |
| `action` | wajib | URL form GET |
| `searchName` | `q` | Nama parameter pencarian |
| `search` | `null` | Nilai awal (fallback `request(searchName)`) |
| `placeholder` | `Cari data…` | Petunjuk input |
| `resetUrl` | `null` | URL tombol Atur ulang; tidak ditampilkan bila kosong |
| Slot utama | — | Filter lain di dalam form |
| `active` | opsional | Ringkasan filter aktif setelah form |

Komponen memakai `x-ui.input` untuk pencarian dan `x-ui.button type="submit"`. Isi slot berada di dalam form; gunakan nama field yang cocok dengan query controller.

## 📝 Pencarian sederhana

```blade
<x-ui.filter-bar :action="route('examples.records.index')"
    :reset-url="route('examples.records.index')" placeholder="Cari judul entri…" />
```

## 🔽 Dengan status dan chip aktif

```blade
<x-ui.filter-bar :action="route('examples.records.index')" :reset-url="route('examples.records.index')">
    <div class="min-w-40 space-y-1.5">
        <label class="label" for="status-filter">Status</label>
        <x-ui.select id="status-filter" name="status" placeholder="Semua status"
            :value="request('status')" :options="['aktif' => 'Aktif', 'draf' => 'Draf']" :searchable="true" />
    </div>
    <x-slot:active>
        @if(request()->filled('status'))<x-ui.badge variant="info">Status: {{ request('status') }}</x-ui.badge>@endif
    </x-slot:active>
</x-ui.filter-bar>
```

## 🗃️ Query pada controller

```php
$records = Record::query()
    ->when($request->filled('q'), fn ($query) => $query->where('title', 'like', '%'.$request->input('q').'%'))
    ->when($request->filled('status'), fn ($query) => $query->where('status', $request->input('status')))
    ->paginate(15)->withQueryString();
```

Validasi status sebelum query jika pilihan berasal dari request. Filter bar tidak melakukan filtering JavaScript otomatis; form GET-lah yang mengirim parameter.
