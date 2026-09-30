@props(['title', 'group'])
<header class="app-header sticky z-30 flex h-14 items-center gap-2 border-b bg-card px-4">
    <button id="sidebarToggle" type="button" aria-label="Buka atau tutup sidebar" class="btn btn-ghost btn-icon btn-sm text-muted-foreground"><x-ui.icon name="panel" /></button>
    <div class="mx-1 hidden h-4 w-px bg-border sm:block"></div>
    <nav aria-label="Breadcrumb" class="hidden min-w-0 items-center gap-1.5 text-sm sm:flex">
        <span class="text-muted-foreground">{{ $group }}</span>
        <x-ui.icon name="chevron-right" class="h-3.5 w-3.5 text-muted-foreground" />
        <span class="truncate font-medium">{{ $title }}</span>
    </nav>
    <div class="ml-auto flex items-center gap-2">
        <label class="relative hidden md:block">
            <span class="sr-only">Cari</span>
            <x-ui.icon name="search" class="pointer-events-none absolute left-2.5 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
            <input class="input w-72 bg-background pl-8" type="search" placeholder="Cari pesanan, produk, pelanggan" />
        </label>
        <div class="relative" data-dropdown>
            <button type="button" data-dropdown-toggle aria-expanded="false" aria-label="Notifikasi" class="btn btn-outline btn-icon relative">
                <x-ui.icon name="bell" /><span class="absolute right-1.5 top-1.5 h-1.5 w-1.5 rounded-full bg-danger"></span>
            </button>
            <div class="menu hidden w-80" role="menu">
                <div class="menu-label">Notifikasi terbaru</div>
                <div class="menu-sep"></div>
                <button type="button" class="menu-item items-start py-2"><span class="avatar avatar-success avatar-sm mt-0.5"><x-ui.icon name="check-circle" /></span><span><span class="block font-medium">Pesanan selesai</span><span class="text-xs text-muted-foreground">#KK-2841 · 10 menit lalu</span></span></button>
                <button type="button" class="menu-item items-start py-2"><span class="avatar avatar-warning avatar-sm mt-0.5"><x-ui.icon name="alert-triangle" /></span><span><span class="block font-medium">Stok menipis</span><span class="text-xs text-muted-foreground">Toraja Sapan · 2 jam lalu</span></span></button>
            </div>
        </div>
        <button type="button" data-modal-open="#customizer" aria-label="Kustomisasi tampilan" data-tip="Kustomisasi" data-tip-pos="bottom left" class="btn btn-outline btn-icon"><x-ui.icon name="palette" /></button>
        <button type="button" data-theme-toggle aria-label="Ganti tema" class="btn btn-outline btn-icon"><svg class="h-4 w-4" aria-hidden="true"><use class="js-theme-icon" href="{{ asset('icons.svg') }}#i-moon"/></svg></button>
    </div>
</header>

