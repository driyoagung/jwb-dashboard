<!DOCTYPE html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Struk {{ $transaction->transaction_number }}</title></head>
<body>
    <h1>{{ $settings->store_name }}</h1><p>{{ $settings->address }}<br>{{ $settings->phone }}</p>
    <p>{{ $transaction->transaction_number }}<br>{{ $transaction->completed_at->timezone($settings->timezone)->format('d M Y H:i') }}<br>Kasir: {{ $transaction->cashier->name }}</p>
    <hr><table><thead><tr><th>Produk</th><th>Qty</th><th>Harga</th><th>Jumlah</th></tr></thead><tbody>
        @foreach ($transaction->items as $item)<tr><td>{{ $item->product_name }}</td><td>{{ $item->qty }}</td><td>Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td><td>Rp {{ number_format($item->line_total, 0, ',', '.') }}</td></tr>@endforeach
    </tbody></table><hr>
    <p>Subtotal: Rp {{ number_format($transaction->subtotal, 0, ',', '.') }}<br>Diskon: Rp {{ number_format($transaction->discount_amount, 0, ',', '.') }}<br>Pajak: Rp {{ number_format($transaction->tax_amount, 0, ',', '.') }}<br><strong>Total: Rp {{ number_format($transaction->grand_total, 0, ',', '.') }}</strong><br>{{ strtoupper($transaction->payment_method) }}: Rp {{ number_format($transaction->amount_received, 0, ',', '.') }}<br>Kembalian: Rp {{ number_format($transaction->change_amount, 0, ',', '.') }}</p>
    @if ($transaction->receivable)<p>Kasbon atas nama: {{ $transaction->customer->name }}<br>Sisa tagihan: Rp {{ number_format($transaction->receivable->balance, 0, ',', '.') }}<br>Jatuh tempo: {{ $transaction->receivable->due_date?->format('d M Y') ?? '-' }}</p>@endif
    <p>{{ $settings->receipt_footer }}</p><button id="print-button" type="button" onclick="window.print()"><svg width="16" height="16" aria-hidden="true"><use href="{{ asset('icons.svg') }}#i-printer" /></svg>Cetak</button>
    <script>window.addEventListener('beforeprint', () => document.getElementById('print-button').hidden = true); window.addEventListener('afterprint', () => document.getElementById('print-button').hidden = false);</script>
</body></html>
