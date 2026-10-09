<x-layouts.admin title="CRUD tersimpan" group="Contoh halaman">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <x-ui.page-header title="CRUD tersimpan" description="Contoh data nyata dengan validasi, otorisasi pemilik, dan pagination server." />
        <div class="flex flex-wrap gap-2">
            <form method="POST" action="{{ route('logout') }}">@csrf<x-ui.button type="submit" variant="outline">Keluar</x-ui.button></form>
            <a href="{{ route('reference.records.create') }}" class="btn btn-primary"><x-ui.icon name="plus" />Tambah entri</a>
        </div>
    </div>

    @if (session('status'))
        <x-ui.alert variant="success" title="Berhasil">{{ session('status') }}</x-ui.alert>
    @endif

    <x-ui.filter-bar :action="route('reference.records.index')" :reset-url="route('reference.records.index')" placeholder="Cari judul entri">
        <div class="min-w-40 space-y-1.5">
            <label class="label" for="status-filter">Status</label>
            <x-ui.select id="status-filter" name="status" placeholder="Semua status"
                :value="request('status')"
                :options="['draft' => 'Draf', 'review' => 'Ditinjau', 'active' => 'Aktif', 'archive' => 'Arsip']"
                :searchable="true" />
        </div>
    </x-ui.filter-bar>

    <x-ui.card title="Daftar entri">
        @if ($records->isEmpty())
            <x-ui.table-state :state="request()->filled('q') || request()->filled('status') ? 'filtered' : 'empty'"
                title="Tidak ada entri" />
        @else
            <div class="overflow-x-auto">
                <table class="table min-w-[640px]">
                    <thead><tr><th>Judul</th><th>Kategori</th><th>Status</th><th>Dibuat</th><th scope="col">Aksi</th></tr></thead>
                    <tbody>
                        @foreach ($records as $record)
                            <tr>
                                <td class="font-medium">{{ $record->title }}</td>
                                <td>{{ ucfirst($record->category) }}</td>
                                <td><x-ui.badge :variant="match ($record->status) { 'active' => 'success', 'review' => 'warning', 'archive' => 'neutral', default => 'info' }">{{ ucfirst($record->status) }}</x-ui.badge></td>
                                <td>{{ $record->created_at->format('d M Y') }}</td>
                                <td><a href="{{ route('reference.records.show', $record) }}" class="btn btn-ghost btn-sm">Lihat</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <x-ui.pagination :paginator="$records" />
        @endif
    </x-ui.card>
</x-layouts.admin>
