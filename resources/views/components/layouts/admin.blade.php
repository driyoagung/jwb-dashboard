@props(['title', 'group' => 'Platform'])
<!DOCTYPE html>
<html lang="id">
<head>
    <x-layouts.head :title="$title" />
</head>
<body data-sidebar="closed" class="font-sans text-sm antialiased">
<script>document.body.dataset.sidebar = window.innerWidth >= 1024 ? 'open' : 'closed';</script>
<div id="overlay" class="fixed inset-0 z-40 bg-black/50" aria-hidden="true"></div>
<x-admin.sidebar />

<div id="main" class="min-h-screen">
    <x-admin.header :title="$title" :group="$group" />
    <main id="page-root" class="mx-auto max-w-7xl space-y-6 p-4 sm:p-6">
        {{ $slot }}
    </main>
    <x-admin.footer />
</div>

<div id="toasts" class="pointer-events-none fixed right-4 top-4 z-[70] flex w-[calc(100%-2rem)] max-w-sm flex-col gap-2" aria-live="polite" aria-atomic="true" data-flash-status="{{ session('status') }}" data-flash-error="{{ $errors->first() }}"></div>
</body>
</html>

