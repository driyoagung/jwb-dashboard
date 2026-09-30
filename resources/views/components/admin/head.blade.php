@php
    $site = app('admin.site');
    $accent = $site->accent();
    $radius = $site->radius();
@endphp
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>{{ $title ?? 'Dashboard' }} &middot; {{ $site->name }}</title>

{{-- Terapkan preferensi sebelum render agar tidak berkedip --}}
<script>
    (function () {
        try {
            var d = document.documentElement,
                g = function (k) { return localStorage.getItem(k); },
                seed = { accent: @json($accent), radius: @json($radius) };
            var t = g('theme');
            if (t === 'light' || t === 'dark') d.dataset.theme = t;

            var a = g('accent') || seed.accent;
            if (a && a !== 'indigo') d.dataset.accent = a;

            var r = g('radius') || seed.radius;
            if (r) d.style.setProperty('--radius', r + 'rem');

            if (g('sbmode') === 'mini') d.dataset.sbmode = 'mini';
        } catch (e) {}
    })();
</script>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

@vite(['resources/css/app.css', 'resources/js/admin/interactions.js', 'resources/js/admin/charts.js'])
