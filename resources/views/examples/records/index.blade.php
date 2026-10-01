<x-layouts.admin title="Daftar entri" group="Contoh halaman">
<div class="flex flex-wrap items-start justify-between gap-4">
    <x-ui.page-header title="Daftar entri" description="Pola halaman daftar yang bisa diadaptasi untuk data proyek apa pun." />
    <a href="{{ route('examples.records.create') }}" class="btn btn-primary"><x-ui.icon name="plus" />Tambah entri</a>
</div>

<div class="card" data-table data-page-size="6">
    <div class="flex flex-col gap-3 p-5 sm:flex-row sm:items-center sm:justify-between sm:p-6">
        <div><h2 class="card-title">Semua entri</h2><p class="card-desc mt-1.5">Cari dan saring data contoh di browser.</p></div>
        <div class="flex flex-col gap-2 sm:flex-row">
            <input type="search" class="input sm:w-56" placeholder="Cari judul atau pemilik" aria-label="Cari entri" data-table-search>
            <select class="select sm:w-36" aria-label="Filter status" data-table-filter><option value="">Semua status</option><option value="aktif">Aktif</option><option value="draf">Draf</option><option value="ditinjau">Ditinjau</option><option value="arsip">Arsip</option></select>
        </div>
    </div>
    <div class="overflow-x-auto border-t">
        <table class="table min-w-[620px]"><thead><tr><th data-sort>Judul</th><th data-sort>Kategori</th><th data-sort>Pemilik</th><th>Status</th><th data-sort>Diperbarui</th><th class="text-right" scope="col" aria-label="Aksi"></th></tr></thead>
            <tbody>
                @foreach (config('kenanga.demo.records') as $record)
                    <tr data-status="{{ $record['statusKey'] }}">
                        <td><a href="{{ route('examples.records.show') }}" class="font-medium text-primary hover:underline">{{ $record['title'] }}</a><p class="mt-0.5 text-xs text-muted-foreground">{{ $record['code'] }}</p></td>
                        <td>{{ $record['category'] }}</td><td>{{ $record['owner'] }}</td>
                        <td><span class="badge badge-{{ $record['tone'] }}">{{ $record['status'] }}</span></td>
                        <td class="text-muted-foreground">{{ $record['updated'] }}</td>
                        <td class="text-right"><a href="{{ route('examples.records.show') }}" class="btn btn-ghost btn-sm">Lihat</a></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div data-table-empty hidden><x-ui.table-state state="filtered"><button type="button" class="btn btn-outline" data-demo-reset-table>Hapus pencarian</button></x-ui.table-state></div>
    </div>
    <div class="flex flex-col items-center justify-between gap-3 border-t px-5 py-3 sm:flex-row sm:px-6"><p data-table-info class="text-sm text-muted-foreground"></p><div data-table-pager class="flex items-center gap-1"></div></div>
</div>
</x-layouts.admin>
