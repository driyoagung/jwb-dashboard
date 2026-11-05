<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Struk {{ $transaction->transaction_number }}</title>
    @vite('resources/css/app.css')
    <style>@page { margin: 4mm; }</style>
</head>
<body class="bg-background text-black print:bg-white">
    <main class="mx-auto w-[80mm] max-w-full bg-white px-4 py-6 font-mono text-[11px] leading-relaxed print:w-[72mm] print:px-0 print:py-0" aria-label="Struk belanja">
        <header class="text-center">
            <div class="mb-3 flex items-center justify-center gap-2"><img src="{{ asset('logo_G.png') }}" alt="" class="h-6 w-6 object-contain grayscale"><h1 class="font-sans text-sm font-bold">{{ $settings->store_name }}</h1></div>
            @if ($settings->address)<p class="whitespace-pre-line text-[10px]">{{ $settings->address }}</p>@endif
            @if ($settings->phone)<p class="text-[10px]">{{ $settings->phone }}</p>@endif
        </header>

        <dl class="mt-4 space-y-1 border-y border-dashed py-3 text-[10px]">
            <div class="flex items-start justify-between gap-2"><dt>No:</dt><dd class="min-w-0 break-all text-right text-[9px]">{{ $transaction->transaction_number }}</dd></div>
            <div class="flex justify-between gap-2"><dt>Waktu:</dt><dd class="text-right">{{ $transaction->completed_at->toImmutable()->timezone($settings->timezone)->format('d M Y H:i T') }}</dd></div>
            <div class="flex items-start justify-between gap-2"><dt>Kasir:</dt><dd class="min-w-0 break-words text-right">{{ $transaction->cashier->name }}</dd></div>
        </dl>

        <table class="table w-full table-fixed text-[10px] [&_td]:break-words [&_td]:px-1 [&_td]:py-1.5 [&_td]:align-top [&_th]:px-1 [&_th]:py-2 [&_th]:text-[9px] [&_th]:text-black">
            <caption class="sr-only">Rincian barang belanja</caption>
            <colgroup><col class="w-[40%]"><col class="w-[10%]"><col class="w-[23%]"><col class="w-[27%]"></colgroup>
            <thead><tr><th scope="col">PRODUK</th><th scope="col" class="text-center">QTY</th><th scope="col" class="text-right">HARGA</th><th scope="col" class="text-right">JUMLAH</th></tr></thead>
            <tbody>@foreach ($transaction->items as $item)<tr><td>{{ $item->product_name }}</td><td class="text-center tabular-nums">{{ $item->qty }}</td><td class="text-right tabular-nums">{{ number_format($item->unit_price, 0, ',', '.') }}</td><td class="text-right tabular-nums">{{ number_format($item->line_total, 0, ',', '.') }}</td></tr>@endforeach</tbody>
        </table>

        <dl class="space-y-1 border-t border-dashed py-3 tabular-nums">
            <div class="flex justify-between gap-2"><dt>Subtotal:</dt><dd>Rp {{ number_format($transaction->subtotal, 0, ',', '.') }}</dd></div>
            <div class="flex justify-between gap-2"><dt>Diskon:</dt><dd>Rp {{ number_format($transaction->discount_amount, 0, ',', '.') }}</dd></div>
            <div class="flex justify-between gap-2"><dt>Pajak:</dt><dd>Rp {{ number_format($transaction->tax_amount, 0, ',', '.') }}</dd></div>
        </dl>
        <dl class="space-y-2 border-t border-black py-3 tabular-nums">
            <div class="flex justify-between gap-2 text-sm font-bold"><dt>TOTAL:</dt><dd>Rp {{ number_format($transaction->grand_total, 0, ',', '.') }}</dd></div>
            <div class="flex justify-between gap-2"><dt>{{ match ($transaction->payment_method) { 'cash' => 'TUNAI', 'transfer' => 'TRANSFER', 'qris' => 'QRIS', 'credit' => 'KASBON', default => $transaction->payment_method_label } }}:</dt><dd>Rp {{ number_format($transaction->amount_received, 0, ',', '.') }}</dd></div>
            <div class="flex justify-between gap-2"><dt>Kembalian:</dt><dd>Rp {{ number_format($transaction->change_amount, 0, ',', '.') }}</dd></div>
        </dl>
        @if ($transaction->receivable)
            <dl class="space-y-1 border-t border-dashed py-3">
                <div class="flex justify-between gap-2"><dt>Pelanggan:</dt><dd class="min-w-0 break-words text-right">{{ $transaction->customer->name }}</dd></div>
                <div class="flex justify-between gap-2"><dt>Sisa kasbon:</dt><dd>Rp {{ number_format($transaction->receivable->balance, 0, ',', '.') }}</dd></div>
                <div class="flex justify-between gap-2"><dt>Jatuh tempo:</dt><dd>{{ $transaction->receivable->due_date?->format('d M Y') ?? '-' }}</dd></div>
            </dl>
        @endif
        <footer class="border-t border-dashed pt-3 text-center text-[10px]">{{ $settings->receipt_footer }}</footer>
    </main>
    <div class="mx-auto my-4 flex w-[80mm] max-w-full justify-center gap-2 px-4 print:hidden">
        <x-ui.button id="print-button" onclick="window.print()"><x-ui.icon name="file-text" />Cetak</x-ui.button>
        <a class="btn btn-outline" href="{{ route('pos.cashier') }}"><x-ui.icon name="cart" />Buka kasir</a>
    </div>
</body>
</html>
