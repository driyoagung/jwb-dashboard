@props(['title'])
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>{{ $title }} · {{ config('kenanga.name') }}</title>
<script>
    (function () {
        var root = document.documentElement;
        var appearance = @json(config('kenanga.appearance'));
        var theme = null;
        try { theme = localStorage.getItem('theme'); } catch (error) {}
        root.dataset.theme = theme === 'dark' || theme === 'light' ? theme : appearance.theme;
        if (appearance.accent === 'indigo') delete root.dataset.accent;
        else root.dataset.accent = appearance.accent;
        root.style.setProperty('--radius', appearance.radius + 'rem');
        delete root.dataset.sbmode;
    })();
</script>
@vite(['resources/css/app.css', 'resources/js/app.js'])

