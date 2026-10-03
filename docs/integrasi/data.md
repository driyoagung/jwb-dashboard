# 📊 Tabel & grafik dari server

## 📄 Pilih jenis paginasi

`data-table` menyaring, mengurutkan, dan memaginasi **baris HTML yang sudah ada di halaman**. Jika controller hanya merender 15 dari ribuan entri, pencarian browser tidak akan menemukan entri di halaman server lainnya. Untuk dataset besar gunakan query, paginasi, serta parameter URL dari Laravel dan lepaskan atribut `data-table` agar dua mekanisme tidak saling bertabrakan.

```php
public function index(Request $request): View
{
    $records = Record::query()
        ->when($request->filled('q'), fn ($query) => $query->where('title', 'like', '%'.$request->input('q').'%'))
        ->when($request->filled('status'), fn ($query) => $query->where('status', $request->input('status')))
        ->orderByDesc('created_at')
        ->paginate(15)
        ->withQueryString();

    return view('examples.records.index', compact('records'));
}
```

```blade
<form method="GET" action="{{ route('examples.records.index') }}" class="flex gap-2">
    <input class="input" type="search" name="q" value="{{ request('q') }}" aria-label="Cari entri" />
    <select class="select" name="status" aria-label="Filter status">
        <option value="">Semua status</option>
        <option value="aktif" @selected(request('status') === 'aktif')>Aktif</option>
        <option value="draf" @selected(request('status') === 'draf')>Draf</option>
    </select>
    <x-ui.button type="submit">Terapkan</x-ui.button>
</form>

{{-- Render tabel HTML memakai $records; jangan beri data-table pada wrapper. --}}
{{ $records->links() }}
```

Jika memakai `x-ui.table-state` sebagai fallback, bedakan tabel yang belum berisi data (`empty`) dengan tabel yang kosong karena pencarian (`filtered`). Untuk sorting dari server, terima hanya nama kolom yang diizinkan (allowlist) sebelum meneruskannya ke `orderBy`; jangan gunakan nama kolom bebas dari input request. `x-ui.pagination` yang tersedia belum terhubung benar ke `$elements` paginator; gunakan `$records->links()` sampai komponen itu disempurnakan.

## 📈 Siapkan grafik dari data nyata

Prop `x-ui.chart` menerima array PHP. Misalnya hitung jumlah entri per status di controller:

```php
$counts = Record::query()
    ->selectRaw('status, COUNT(*) as total')
    ->groupBy('status')
    ->pluck('total', 'status');

$chartItems = collect(['draf', 'ditinjau', 'aktif', 'arsip'])
    ->map(fn ($status, $index) => [
        'label' => ucfirst($status),
        'value' => (int) ($counts[$status] ?? 0),
        'color' => $index + 1,
    ])
    ->all();

return view('admin.analytics', compact('chartItems'));
```

```blade
@if (collect($chartItems)->sum('value') > 0)
    <x-ui.chart type="donut" aria="Komposisi status entri"
        :items="$chartItems" total-label="Entri" />
@else
    <x-ui.table-state state="empty" title="Belum ada entri" />
@endif
```

Contoh mengasumsikan model/tabel `Record` dari [panduan CRUD](/integrasi/crud). Untuk grafik tren, bentuk `labels` dan `series` dengan urutan periode yang sama; siapkan seri lengkap termasuk periode bernilai nol. Data pada `admin/dashboard.blade.php` saat ini masih angka statis, jadi ganti juga nilai kartu statistik dan grafik di sana setelah membuat query aplikasi Anda.
