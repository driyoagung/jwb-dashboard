<x-layouts.admin title="Pengguna" group="Pengaturan">
    <x-ui.page-header title="Pengguna" description="Kelola admin dan kasir." />
    <div class="card" data-pos-server-table>
        <div class="flex flex-col gap-3 p-6 pb-4 sm:flex-row sm:items-center sm:justify-between"><div><h2 class="card-title">Daftar pengguna</h2><p class="card-desc mt-1.5">Cari pengguna dan lihat aksesnya.</p></div><a class="btn btn-primary btn-sm" href="{{ route('pos.manage.users.create') }}"><x-ui.icon name="plus" />Tambah pengguna</a></div>
        <x-ui.filter-bar :action="route('pos.manage.users.index')" :reset-url="route('pos.manage.users.index')" placeholder="Cari nama atau email" embedded live />
        <div data-pos-results>
            @if ($users->isEmpty()) <div class="border-t"><x-ui.table-state :state="request()->filled('q') ? 'filtered' : 'empty'" title="Belum ada pengguna" /></div>
            @else <div class="overflow-x-auto border-t"><table class="table table-striped"><thead><tr>
                @foreach (['name' => 'Pengguna', 'role' => 'Peran'] as $column => $label)<th aria-sort="{{ request('sort', 'name') === $column ? (request('direction', 'asc') === 'asc' ? 'ascending' : 'descending') : 'none' }}"><a href="{{ request()->fullUrlWithQuery(['sort' => $column, 'direction' => request('sort', 'name') === $column && request('direction', 'asc') === 'asc' ? 'desc' : 'asc', 'page' => 1]) }}" class="inline-flex items-center gap-1">{{ $label }}<x-ui.icon name="chevron-down" class="h-3 w-3" /></a></th>@endforeach
                <th>Status</th><th scope="col">Aksi</th></tr></thead><tbody>
                @foreach ($users as $user)<tr><td><div class="flex items-center gap-3"><span class="avatar avatar-soft">{{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}</span><div class="leading-tight"><p class="font-medium">{{ $user->name }}</p><p class="text-xs text-muted-foreground">{{ $user->email }}</p></div></div></td><td>{{ ucfirst($user->role) }}</td><td><x-ui.badge :variant="$user->is_active ? 'success' : 'neutral'" dot>{{ $user->is_active ? 'Aktif' : 'Nonaktif' }}</x-ui.badge></td><td><a class="btn btn-info btn-sm" href="{{ route('pos.manage.users.edit', $user) }}"><x-ui.icon name="edit" />Ubah</a></td></tr>@endforeach
            </tbody></table></div>@endif
            <x-ui.pagination :paginator="$users" />
        </div>
    </div>
</x-layouts.admin>
