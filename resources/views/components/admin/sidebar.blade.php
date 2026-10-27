<aside id="sidebar" class="fixed inset-y-0 left-0 z-50 flex w-64 flex-col overflow-hidden border-r bg-sidebar" aria-label="Navigasi utama">
    <div class="p-3">
        <div class="flex w-full items-center">
            <img src="{{ asset('logo.png') }}" alt="{{ config('kenanga.brand.name') }}" class="h-9 max-w-full object-contain">
        </div>
    </div>

    <nav class="flex-1 space-y-5 overflow-y-auto overflow-x-hidden px-3 pb-3 pt-1">
        @foreach (config('kenanga.navigation') as $group)
            @continue(($group['showcase'] ?? false) && ! config('kenanga.showcase'))
            @continue(($group['ability'] ?? null) && ! auth()->user()?->can($group['ability']))
            <div>
                <p class="sb-label mb-1 px-2 text-xs font-medium text-muted-foreground">{{ $group['label'] }}</p>
                <ul class="space-y-0.5">
                    @foreach ($group['items'] as $item)
                        @continue(($item['ability'] ?? null) && ! auth()->user()?->can($item['ability']))
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
        @auth
            <div class="flex items-center gap-2.5 rounded-md p-2">
                <span class="avatar avatar-sm"><x-ui.icon name="user" /></span>
                <span class="sb-label min-w-0 flex-1 leading-tight"><span class="block truncate text-sm font-medium">{{ auth()->user()->name }}</span><span class="block truncate text-xs text-muted-foreground">{{ auth()->user()->email }}</span></span>
            </div>
        @endauth
    </div>
</aside>

