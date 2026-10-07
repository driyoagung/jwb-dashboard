@php
    $orderSummary = 'Total pesanan: 120';
@endphp
<x-layouts.blank title="Dashboard" group="Platform">

    <div class="max-w-xl mx-auto px-6 py-8 space-y-8">


        <h1 class="text-2xl font-bold">
            Component Testing
        </h1>

        <hr>
        <x-ui.timeline :items="[
            [
                'title' => 'Pesanan selesai',
                'time' => 'Hari ini, 10.30',
                'datetime' => '2026-10-03T10:30:00',
                'tone' => 'success',
            ],
            ['title' => 'Paket dikirim', 'time' => 'Kemarin', 'description' => 'Kurir menerima paket.'],
            ['title' => 'Pembayaran gagal', 'time' => '1 Okt 2026', 'tone' => 'danger'],
        ]" />


        <x-ui.card class="min-w-0">
            <x-slot:header>
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <h2 class="card-title">Pesanan</h2>
                        <p class="card-desc">Periode berjalan</p>
                    </div>
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline btn-sm">Lihat semua</a>
                </div>
            </x-slot:header>
            <p class="text-muted-foreground">{{ $orderSummary }}</p>
        </x-ui.card>
    </div>

    </div>

</x-layouts.blank>
