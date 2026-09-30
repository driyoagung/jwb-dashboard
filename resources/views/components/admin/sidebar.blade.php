<aside id="sidebar" class="fixed inset-y-0 left-0 z-50 flex w-64 flex-col overflow-hidden border-r bg-sidebar" aria-label="Navigasi utama">
    <div class="p-3">
        <button type="button" class="flex w-full items-center gap-2.5 rounded-md p-2 text-left hover:bg-accent focus-ring">
            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-md bg-primary text-primary-foreground"><x-ui.icon name="coffee" /></span>
            <span class="sb-label min-w-0 flex-1 leading-tight">
                <span class="block truncate text-sm font-medium">{{ config('kenanga.brand.workspace') }}</span>
                <span class="block truncate text-xs text-muted-foreground">{{ config('kenanga.brand.description') }}</span>
            </span>
            <x-ui.icon name="updown" class="sb-label h-4 w-4 shrink-0 text-muted-foreground" />
        </button>
    </div>

    <nav class="flex-1 space-y-5 overflow-y-auto overflow-x-hidden px-3 pb-3 pt-1">
        @foreach (config('kenanga.navigation') as $group)
            @continue(($group['showcase'] ?? false) && ! config('kenanga.showcase'))
            <div>
                <p class="sb-label mb-1 px-2 text-xs font-medium text-muted-foreground">{{ $group['label'] }}</p>
                <ul class="space-y-0.5">
                    @foreach ($group['items'] as $item)
                        @if (! empty($item['children']))
                            @php
                                $submenuId = 'sub-'.($item['id'] ?? \Illuminate\Support\Str::slug($item['label']));
                                $submenuActive = collect($item['children'])->contains(fn (array $child): bool => request()->routeIs($child['active'] ?? $child['route']));
                            @endphp
                            <li>
                                <button type="button" class="nav-link" data-collapse="#{{ $submenuId }}" aria-expanded="{{ $submenuActive ? 'true' : 'false' }}">
                                    <x-ui.icon :name="$item['icon']" class="h-4 w-4 shrink-0" />
                                    <span class="sb-label flex-1 truncate">{{ $item['label'] }}</span>
                                    <x-ui.icon name="chevron-right" @class(['nav-chevron sb-label h-4 w-4 transition-transform', 'rotate-90' => $submenuActive]) />
                                </button>
                                <ul id="{{ $submenuId }}" class="nav-sub sb-label" @if(! $submenuActive) hidden @endif>
                                    @foreach ($item['children'] as $child)
                                        <li><x-admin.nav-link :item="$child" /></li>
                                    @endforeach
                                </ul>
                            </li>
                        @else
                            <li><x-admin.nav-link :item="$item" /></li>
                        @endif
                    @endforeach
                </ul>
            </div>
        @endforeach
    </nav>

    <div class="border-t p-3">
        <a href="#" class="nav-link mb-1">
            <x-ui.icon name="help" class="h-4 w-4 shrink-0" /><span class="sb-label">Bantuan</span>
        </a>
        <a href="{{ route('admin.settings') }}" class="flex items-center gap-2.5 rounded-md p-2 hover:bg-accent focus-ring">
            <span class="avatar avatar-sm">AR</span>
            <span class="sb-label min-w-0 flex-1 leading-tight"><span class="block truncate text-sm font-medium">Ayu Rahmawati</span><span class="block truncate text-xs text-muted-foreground">ayu@kopikenanga.id</span></span>
            <x-ui.icon name="updown" class="sb-label h-4 w-4 shrink-0 text-muted-foreground" />
        </a>
    </div>
</aside>

