<x-layouts.guest title="Masuk">
<div class="grid min-h-screen lg:grid-cols-2">
    <div class="relative flex flex-col justify-center px-6 py-12 sm:px-12">
        <x-ui.button variant="outline" size="icon" data-theme-toggle aria-label="Ganti tema" class="absolute right-4 top-4">
            <x-ui.icon name="moon" use-class="js-theme-icon" />
        </x-ui.button>
        <div class="mx-auto w-full max-w-sm">
            <div class="flex items-center gap-2.5">
                <span class="flex h-9 w-9 items-center justify-center rounded-md bg-primary text-primary-foreground"><x-ui.icon name="coffee" class="h-5 w-5" /></span>
                <span class="text-lg font-semibold">{{ config('kenanga.brand.name') }}</span>
            </div>
            <x-ui.page-header title="Masuk ke akun Anda" description="Masukkan email dan kata sandi untuk melanjutkan." class="mt-8" />

            <form method="POST" action="{{ route('login.store') }}" class="mt-6 space-y-4">
                @csrf
                <x-ui.field label="Email" name="email" :required="true">
                    <x-ui.input name="email" type="email" autocomplete="email" required />
                </x-ui.field>
                <x-ui.field label="Kata sandi" name="password" :required="true">
                    <x-ui.input name="password" type="password" autocomplete="current-password" required />
                </x-ui.field>
                <x-ui.switch name="remember" label="Ingat saya di perangkat ini" />
                <x-ui.button type="submit" class="w-full">Masuk</x-ui.button>
            </form>
        </div>
    </div>

    <div class="hidden flex-col justify-between bg-primary p-12 text-primary-foreground lg:flex">
        <div class="flex items-center gap-2 text-sm font-medium opacity-90"><x-ui.icon name="layers" />{{ config('kenanga.brand.name') }}</div>
        <p class="text-2xl font-medium leading-snug">Dashboard yang tertata untuk pekerjaan sehari-hari.</p>
    </div>
</div>
</x-layouts.guest>
