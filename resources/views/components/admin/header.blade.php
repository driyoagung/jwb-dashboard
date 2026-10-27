@props(['title', 'group'])
<header class="app-header sticky z-30 flex h-14 items-center gap-2 border-b bg-card px-4">
    <button id="sidebarToggle" type="button" aria-label="Buka atau tutup sidebar" class="btn btn-ghost btn-icon btn-sm text-muted-foreground"><x-ui.icon name="panel" /></button>
    <div class="mx-1 hidden h-4 w-px bg-border sm:block"></div>
    <x-ui.button variant="outline" size="sm" data-pos-fullscreen aria-label="Layar penuh" title="Layar penuh"><x-ui.icon name="monitor" /><span data-fullscreen-label>Layar penuh</span></x-ui.button>
    <nav aria-label="Breadcrumb" class="hidden min-w-0 items-center gap-1.5 text-sm sm:flex"><span class="text-muted-foreground">{{ $group }}</span><x-ui.icon name="chevron-right" class="h-3.5 w-3.5 text-muted-foreground" /><span class="truncate font-medium">{{ $title }}</span></nav>
    <div class="ml-auto flex items-center gap-1.5">
        <form method="GET" action="{{ route('pos.search') }}" class="hidden lg:block"><x-ui.input name="q" type="search" icon="search" placeholder="Cari produk, transaksi, draft" aria-label="Pencarian global" class="w-64" /></form>
        <a href="{{ route('pos.search') }}" class="btn btn-outline btn-icon lg:hidden" aria-label="Pencarian global"><x-ui.icon name="search" /></a>
        <x-ui.button variant="outline" size="icon" data-theme-toggle aria-label="Ganti tema" title="Ganti tema"><svg class="h-4 w-4" aria-hidden="true"><use class="js-theme-icon" href="{{ asset('icons.svg') }}#i-moon" /></svg></x-ui.button>
        <div class="relative" data-dropdown data-notifications data-feed-url="{{ route('pos.notifications.feed') }}" data-csrf="{{ csrf_token() }}">
            <x-ui.button variant="outline" size="icon" data-dropdown-toggle aria-expanded="false" aria-label="Notifikasi" title="Notifikasi"><x-ui.icon name="bell" /><span data-notification-indicator @if ($unreadNotificationCount === 0) hidden @endif class="absolute right-1 top-1 h-2 w-2 rounded-full bg-danger"></span></x-ui.button>
            <div class="menu hidden w-96 max-w-[calc(100vw-2rem)]" role="menu">
                <div class="menu-label" data-notification-count>Notifikasi @if ($unreadNotificationCount > 0)({{ $unreadNotificationCount }} baru)@endif</div>
                <div class="flex flex-wrap gap-2 px-3 py-2">
                    <form method="POST" action="{{ route('pos.notifications.read-all') }}">@csrf<x-ui.button type="submit" variant="outline" size="sm" data-notification-read-all :disabled="$unreadNotificationCount === 0"><x-ui.icon name="check" />Tandai semua</x-ui.button></form>
                    <x-ui.button type="button" variant="destructive" size="sm" data-modal-open="#notification-delete-all" data-notification-delete-all><x-ui.icon name="trash" />Hapus semua</x-ui.button>
                </div>
                <div class="menu-sep"></div>
                <div data-notification-list>
                @forelse ($recentNotifications as $notification)
                    <div class="border-b px-3 py-2 last:border-b-0"><p class="text-sm font-medium">{{ $notification->title }} @unless($notification->read_at)<span class="text-info">•</span>@endunless</p><p class="text-xs text-muted-foreground">{{ $notification->message }}</p>
                        <div class="mt-2 flex flex-wrap gap-1">
                            <form method="POST" action="{{ route('pos.notifications.read', $notification) }}">@csrf<x-ui.button type="submit" variant="info" size="sm"><x-ui.icon name="eye" />Buka</x-ui.button></form>
                            @unless($notification->read_at)<form method="POST" action="{{ route('pos.notifications.mark', $notification) }}">@csrf<x-ui.button type="submit" variant="outline" size="sm"><x-ui.icon name="check" />Tandai dibaca</x-ui.button></form>@endunless
                            <form method="POST" action="{{ route('pos.notifications.destroy', $notification) }}">@csrf @method('DELETE')<x-ui.button type="submit" variant="destructive" size="sm"><x-ui.icon name="trash" />Hapus</x-ui.button></form>
                        </div>
                    </div>
                @empty
                    <p class="px-3 py-2 text-sm text-muted-foreground">Belum ada notifikasi.</p>
                @endforelse
                </div>
                <div class="menu-sep"></div>
                <a href="{{ route('pos.notifications.index') }}" class="menu-item"><x-ui.icon name="eye" />Lihat semua</a>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}">@csrf<x-ui.button type="submit" variant="destructive" size="sm" title="Keluar"><x-ui.icon name="arrow-left" /><span class="hidden sm:inline">Keluar</span></x-ui.button></form>
    </div>
</header>
<x-ui.confirm-action id="notification-delete-all" title="Hapus semua notifikasi?" description="Semua notifikasi akun ini akan dihapus." :action="route('pos.notifications.destroy-all')" label="Hapus semua" />
