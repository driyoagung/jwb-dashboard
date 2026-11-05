<x-layouts.guest title="Masuk">
    <div class="grid min-h-screen lg:grid-cols-2">
        <section class="relative hidden items-center overflow-hidden border-r bg-background px-10 py-16 lg:flex xl:px-20" aria-labelledby="login-intro-title">
            <div class="pointer-events-none absolute -left-40 -top-48 h-96 w-96 rounded-full bg-soft" aria-hidden="true"></div>
            <div class="pointer-events-none absolute -bottom-56 -left-24 h-96 w-96 rounded-full bg-warning-soft" aria-hidden="true"></div>
            <div class="pointer-events-none absolute -bottom-64 right-0 h-96 w-96 rounded-full border border-primary/20" aria-hidden="true"></div>
            <div class="relative mx-auto w-full max-w-2xl">
                <p class="mb-6 flex items-center gap-3 text-xs font-semibold uppercase tracking-widest text-primary"><span class="h-1 w-8 rounded-full bg-warning"></span>Sistem kasir dan manajemen stok</p>
                <h1 id="login-intro-title" class="max-w-2xl text-4xl font-semibold leading-tight tracking-tight text-foreground xl:text-6xl">Kelola penjualan dan stok <span class="text-warning">warung</span> dengan lebih mudah.</h1>
                <p class="mt-6 max-w-lg text-lg leading-relaxed text-muted-foreground xl:text-xl">Pantau transaksi, stok, dan laporan dalam satu tempat.</p>
                <div class="mt-10 grid grid-cols-3 gap-3 xl:gap-5">
                    <x-ui.card class="text-center [&>.card-content]:pt-6">
                        <span class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-lg bg-soft text-primary"><x-ui.icon name="cart" class="h-6 w-6" /></span>
                        <p class="font-semibold leading-snug">Transaksi lebih cepat</p>
                    </x-ui.card>
                    <x-ui.card class="text-center [&>.card-content]:pt-6">
                        <span class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-lg bg-warning-soft text-warning"><x-ui.icon name="package" class="h-6 w-6" /></span>
                        <p class="font-semibold leading-snug">Stok selalu terpantau</p>
                    </x-ui.card>
                    <x-ui.card class="text-center [&>.card-content]:pt-6">
                        <span class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-lg bg-success-soft text-success"><x-ui.icon name="chart" class="h-6 w-6" /></span>
                        <p class="font-semibold leading-snug">Laporan lebih mudah</p>
                    </x-ui.card>
                </div>
            </div>
        </section>

        <section class="flex items-center bg-card px-6 py-12 sm:px-12 lg:px-14 xl:px-20" aria-labelledby="login-form-title">
            <div class="mx-auto w-full max-w-lg">
                <div class="mb-16 flex items-center justify-center gap-3 sm:mb-20">
                    <img src="{{ asset('logo_G.png') }}" alt="" class="h-20 w-20 shrink-0 object-contain">
                    <span class="text-4xl font-bold tracking-tight text-foreground sm:text-5xl">Griyo <span class="text-primary">POS</span></span>
                </div>

                <div>
                    <h2 id="login-form-title" class="text-3xl font-semibold tracking-tight text-foreground">Masuk ke akun Anda</h2>
                    <p class="mt-2 text-muted-foreground">Masukkan email dan kata sandi untuk melanjutkan.</p>
                </div>

                <form method="POST" action="{{ route('login.store') }}" class="mt-8 space-y-5">
                    @csrf
                    <x-ui.field label="Email" name="email" :required="true">
                        <x-ui.input name="email" type="email" icon="mail" autocomplete="email" placeholder="Masukkan email Anda" class="h-12" required autofocus />
                    </x-ui.field>
                    <x-ui.field label="Kata sandi" name="password" :required="true">
                        <div class="relative" data-password-field>
                            <x-ui.input name="password" type="password" icon="lock" autocomplete="current-password" placeholder="Masukkan kata sandi Anda" class="h-12 pr-12" required />
                            <x-ui.button type="button" variant="ghost" size="icon" class="absolute right-1 top-1 h-10 w-10" data-password-toggle aria-label="Tampilkan kata sandi" aria-pressed="false"><x-ui.icon name="eye" /></x-ui.button>
                        </div>
                    </x-ui.field>
                    <label class="flex cursor-pointer items-center gap-3 text-sm font-medium">
                        <input type="checkbox" name="remember" value="1" class="h-4 w-4 rounded border-border accent-primary" @checked(old('remember'))>
                        Ingat saya di perangkat ini
                    </label>
                    <x-ui.button type="submit" size="lg" class="w-full"><x-ui.icon name="arrow-right" />Masuk</x-ui.button>
                </form>
            </div>
        </section>
    </div>
</x-layouts.guest>
