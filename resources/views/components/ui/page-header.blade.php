@props(['title', 'description' => null])
<div {{ $attributes }}>
    <h1 class="text-2xl font-semibold tracking-tight">{{ $title }}</h1>
    @if ($description)
        <p class="text-muted-foreground">{{ $description }}</p>
    @endif
</div>

