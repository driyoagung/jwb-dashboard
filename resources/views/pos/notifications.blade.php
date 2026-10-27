<x-layouts.admin title="Notifikasi" group="Ringkasan">
    <x-ui.page-header title="Notifikasi" description="Informasi transaksi dan aktivitas stok untuk akun Anda." />
    <div class="card" data-pos-server-table>
        <div class="flex flex-wrap items-center justify-between gap-3 p-6 pb-4">
            <div><h2 class="card-title">Riwayat notifikasi</h2><p class="card-desc mt-1.5">Cari pemberitahuan dan lihat statusnya.</p></div>
            <div class="flex flex-wrap gap-2">
                <form method="POST" action="{{ route('pos.notifications.read-all') }}">@csrf<x-ui.button type="submit" variant="outline" size="sm"><x-ui.icon name="check" />Tandai semua dibaca</x-ui.button></form>
                <x-ui.button type="button" variant="destructive" size="sm" data-modal-open="#notification-index-delete-all"><x-ui.icon name="trash" />Hapus semua</x-ui.button>
            </div>
        </div>
        <x-ui.filter-bar :action="route('pos.notifications.index')" :reset-url="route('pos.notifications.index')" placeholder="Cari informasi" embedded live>
            <div class="flex flex-wrap gap-2" role="group" aria-label="Filter status notifikasi" data-pos-filter-group>
                <input type="hidden" name="status" value="{{ request('status', '') }}" data-pos-filter-value>
                @foreach (['' => 'Semua', 'unread' => 'Baru', 'read' => 'Dibaca'] as $value => $label)
                    <button type="button" class="filter-chip" data-pos-filter-option="{{ $value }}" aria-pressed="{{ request('status', '') === $value ? 'true' : 'false' }}">{{ $label }}</button>
                @endforeach
            </div>
        </x-ui.filter-bar>
        <div data-pos-results>
            @if ($notifications->isEmpty())
                <div class="border-t"><x-ui.table-state :state="request()->hasAny(['q', 'status']) ? 'filtered' : 'empty'" title="Belum ada notifikasi" /></div>
            @else
                <div class="overflow-x-auto border-t"><table class="table table-striped"><thead><tr><th>Informasi</th><th>Waktu</th><th>Status</th><th scope="col">Aksi</th></tr></thead><tbody>
                @foreach ($notifications as $notification)
                    <tr>
                        <td><div class="flex items-center gap-3"><span class="avatar avatar-soft avatar-square"><x-ui.icon :name="match ($notification->type) { 'sale' => 'cart', 'draft' => 'file-text', 'credit' => 'card', default => 'package' }" /></span><div class="leading-tight"><p class="font-medium">{{ $notification->title }}</p><p class="text-xs text-muted-foreground">{{ $notification->message }}</p></div></div></td>
                        <td class="text-muted-foreground">{{ $notification->created_at->format('d M Y H:i') }}</td>
                        <td><x-ui.badge :variant="$notification->read_at ? 'neutral' : 'info'" dot>{{ $notification->read_at ? 'Dibaca' : 'Baru' }}</x-ui.badge></td>
                        <td><div class="flex flex-wrap gap-2 whitespace-nowrap">
                            <form method="POST" action="{{ route('pos.notifications.read', $notification) }}">@csrf<x-ui.button type="submit" variant="info" size="sm"><x-ui.icon name="eye" />Buka</x-ui.button></form>
                            @unless($notification->read_at)<form method="POST" action="{{ route('pos.notifications.mark', $notification) }}">@csrf<x-ui.button type="submit" variant="outline" size="sm"><x-ui.icon name="check" />Tandai dibaca</x-ui.button></form>@endunless
                            <form method="POST" action="{{ route('pos.notifications.destroy', $notification) }}">@csrf @method('DELETE')<x-ui.button type="submit" variant="destructive" size="sm"><x-ui.icon name="trash" />Hapus</x-ui.button></form>
                        </div></td>
                    </tr>
                @endforeach
                </tbody></table></div>
            @endif
            <x-ui.pagination :paginator="$notifications" />
        </div>
    </div>
    <x-ui.confirm-action id="notification-index-delete-all" title="Hapus semua notifikasi?" description="Semua notifikasi akun ini akan dihapus." :action="route('pos.notifications.destroy-all')" label="Hapus semua" />
</x-layouts.admin>
