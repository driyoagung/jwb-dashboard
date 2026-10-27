<x-layouts.guest title="Masuk">
<div class="grid min-h-screen lg:grid-cols-2">
    <div class="relative flex flex-col justify-center px-6 py-12 sm:px-12">
        <div class="mx-auto w-full max-w-sm">
            <div class="flex w-fit items-center gap-2.5">
                <img src="{{ asset('logo.png') }}" alt="{{ config('kenanga.brand.name') }}" class="h-14 max-w-full object-contain">
            </div>
            <x-ui.page-header title="Masuk ke akun Anda" description="Masukkan email dan kata sandi untuk melanjutkan." class="mt-8" />

            <form method="POST" action="{{ route('login.store') }}" class="mt-6 space-y-4">
                @csrf
                <x-ui.field label="Email" name="email" :required="true">
                    <x-ui.input name="email" type="email" autocomplete="email" placeholder="Masukkan email" required />
                </x-ui.field>
                <x-ui.field label="Kata sandi" name="password" :required="true">
                    <x-ui.input name="password" type="password" autocomplete="current-password" placeholder="Masukkan kata sandi" required />
                </x-ui.field>
                <x-ui.switch name="remember" label="Ingat saya di perangkat ini" />
                <x-ui.button type="submit" class="w-full"><x-ui.icon name="arrow-right" />Masuk</x-ui.button>
            </form>
        </div>
    </div>

    <div class="hidden flex-col justify-end bg-primary p-12 text-primary-foreground lg:flex">
        <p class="text-2xl font-medium leading-snug">Kelola penjualan dan stok toko dalam satu dashboard.</p>
    </div>
</div>
</x-layouts.guest>
