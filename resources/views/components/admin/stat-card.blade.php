@props([
    'label',
    'value',
    'icon' => 'activity',
    'tone' => 'primary',
    'delta' => null,
    'trend' => null,
    'spark' => null,
    'href' => null,
])

@php
    $tones = [
        'primary' => 'bg-soft text-soft-foreground',
        'success' => 'bg-success-soft text-success',
        'danger' => 'bg-danger-soft text-danger',
        'warning' => 'bg-warning-soft text-warning',
        'info' => 'bg-info-soft text-info',
        'neutral' => 'bg-muted text-muted-foreground',
    ];
    $stroke = [
        'primary' => 'var(--primary)',
        'success' => 'var(--positive)',
        'danger' => 'var(--destructive)',
        'warning' => 'var(--caution)',
        'info' => 'var(--notice)',
        'neutral' => 'var(--muted-foreground)',
    ][$tone] ?? 'var(--primary)';
@endphp

<{{ $href ? 'a' : 'div' }} @if ($href) href="{{ $href }}" @endif class="card block p-5">
    <div class="flex items-start justify-between gap-3">
        <div class="min-w-0">
            <p class="truncate text-sm text-muted-foreground">{{ $label }}</p>
            <p class="mt-1.5 text-2xl font-semibold tabular-nums tracking-tight">{{ $value }}</p>
        </div>
        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-md {{ $tones[$tone] ?? $tones['primary'] }}">
            <x-icon :name="$icon" class="h-4 w-4" />
        </span>
    </div>

    @if ($delta !== null || $spark)
        <div class="mt-3 flex items-end justify-between gap-3">
            @if ($delta !== null)
                <span @class([
                    'inline-flex items-center gap-1 text-xs font-medium',
                    'text-success' => str_starts_with((string) $delta, '+') || str_starts_with((string) $delta, '-') === false && $trend !== 'down',
                    'text-danger' => $trend === 'down',
                    'text-muted-foreground' => $trend === null,
                ])>
                    @if ($trend)
                        <x-icon :name="$trend === 'up' ? 'trend-up' : 'trend-down'" class="h-3.5 w-3.5" />
                    @endif
                    {{ $delta }}
                    @if ($slot->isNotEmpty())
                        <span class="font-normal text-muted-foreground">{{ $slot }}</span>
                    @endif
                </span>
            @else
                <span></span>
            @endif

            @if ($spark && count($spark) > 1)
                <svg viewBox="0 0 {{ count($spark) * 4 }} 24" class="h-6 w-24 overflow-visible" aria-hidden="true"
                    preserveAspectRatio="none">
                    @php
                        $max = max($spark);
                        $min = min($spark);
                        $range = $max - $min ?: 1;
                        $points = collect($spark)
                            ->map(fn ($v, $i) => ($i * 4) . ' ' . (22 - (($v - $min) / $range) * 20))
                            ->implode(' ');
                    @endphp
                    <polyline points="{{ $points }}" fill="none" stroke="{{ $stroke }}" stroke-width="1.5"
                        stroke-linecap="round" stroke-linejoin="round" vector-effect="non-scaling-stroke" />
                </svg>
            @endif
        </div>
    @endif
</{{ $href ? 'a' : 'div' }}>
