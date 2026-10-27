@props(['action', 'searchName' => 'q', 'search' => null, 'placeholder' => 'Cari data…', 'resetUrl' => null, 'embedded' => false, 'live' => false])

<div {{ $attributes->class($embedded ? 'border-t px-6 py-3' : 'card p-4 sm:p-5') }}>
    <form method="GET" action="{{ $action }}" class="flex flex-wrap items-end gap-3" @if($live) data-pos-live-filter @endif>
        <div class="min-w-[12rem] flex-1 @unless($embedded) space-y-1.5 @endunless">
            <label class="{{ $embedded ? 'sr-only' : 'label' }}" for="{{ $searchName }}-filter">Pencarian</label>
            <x-ui.input :id="$searchName.'-filter'" type="search" :name="$searchName" :value="$search ?? request($searchName)" :placeholder="$placeholder" icon="search" />
        </div>
        {{ $slot }}
        @unless($live)<x-ui.button type="submit" size="sm"><x-ui.icon name="search" />Terapkan</x-ui.button>@endunless
        @if($resetUrl)
            <a href="{{ $resetUrl }}" class="btn btn-outline btn-sm"><x-ui.icon name="refresh" />Atur ulang</a>
        @endif
    </form>
    @isset($active)
        <div class="mt-4 flex flex-wrap items-center gap-2 border-t pt-4 text-sm" aria-label="Filter aktif">
            <span class="text-muted-foreground">Filter aktif:</span>{{ $active }}
        </div>
    @endisset
</div>
