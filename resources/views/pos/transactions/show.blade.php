<x-layouts.admin :title="$transaction->transaction_number" group="Penjualan">
    <div class="flex flex-wrap items-start justify-between gap-4"><x-ui.page-header :title="$transaction->transaction_number" description="Rincian dan status transaksi." />@if ($transaction->status === 'completed')<a class="btn btn-outline" target="_blank" rel="noopener" href="{{ route('pos.transactions.receipt', $transaction) }}"><x-ui.icon name="printer" />Cetak struk</a>@endif</div>
    @if (session('cart_storage_key')) <script>try { localStorage.removeItem(@json(session('cart_storage_key'))); } catch (e) {}</script> @endif
    @if ($transaction->status === 'pending')
        <x-ui.alert variant="warning" title="Menunggu pembayaran">Stok produk ditahan sampai status Midtrans diperiksa. Setelah membayar, tekan Cek pembayaran.</x-ui.alert>
        <div class="flex flex-wrap gap-2">
            @if ($transaction->midtrans_snap_token)<x-ui.button data-snap-pay data-snap-token="{{ $transaction->midtrans_snap_token }}" data-check-url="{{ route('pos.transactions.check-payment', $transaction) }}"><x-ui.icon name="card" />Buka pembayaran</x-ui.button>
            @elseif ($transaction->midtrans_redirect_url)<a class="btn btn-primary" href="{{ $transaction->midtrans_redirect_url }}"><x-ui.icon name="external" />Buka pembayaran</a>
            @else<form method="POST" action="{{ route('pos.transactions.retry-payment', $transaction) }}">@csrf<x-ui.button type="submit"><x-ui.icon name="refresh" />Buat ulang sesi pembayaran</x-ui.button></form>@endif
            <form method="POST" action="{{ route('pos.transactions.check-payment', $transaction) }}">@csrf<x-ui.button type="submit" variant="outline"><x-ui.icon name="refresh" />Cek pembayaran</x-ui.button></form>
        </div>
    @elseif ($transaction->status === 'failed')
        <x-ui.alert variant="danger" title="Pembayaran gagal">Transaksi tidak selesai dan stok produk sudah dikembalikan.</x-ui.alert>
    @endif
    <x-ui.detail-list :items="[['label' => 'Kasir', 'value' => $transaction->cashier->name], ['label' => 'Waktu', 'value' => ($transaction->completed_at ?? $transaction->created_at)->timezone($zone)->format('d M Y H:i')], ['label' => 'Metode', 'value' => strtoupper($transaction->payment_method)], ['label' => 'Status', 'value' => match ($transaction->status) { 'completed' => 'Selesai', 'pending' => 'Menunggu pembayaran', default => 'Gagal' }], ['label' => 'Status Midtrans', 'value' => $transaction->midtrans_status ?: '-']]" />
    @if ($transaction->receivable)
        <x-ui.card title="Kasbon" class="my-6"><p class="mb-3">Pelanggan: <strong>{{ $transaction->customer->name }}</strong></p><p class="mb-3">Sisa tagihan: <strong>Rp {{ number_format($transaction->receivable->balance, 0, ',', '.') }}</strong></p><a href="{{ route('pos.receivables.show', $transaction->receivable) }}" class="btn btn-info btn-sm"><x-ui.icon name="eye" />Lihat kasbon</a></x-ui.card>
    @endif
    <x-ui.card title="Item penjualan"><div class="overflow-x-auto"><table class="table"><thead><tr><th>Produk</th><th>Harga</th><th>Qty</th><th>Subtotal</th></tr></thead><tbody>
        @foreach ($transaction->items as $item)<tr><td>{{ $item->product_name }} <span class="text-xs text-muted-foreground">{{ $item->product_sku }}</span></td><td>Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td><td>{{ $item->qty }} {{ $item->unit_name }}</td><td>Rp {{ number_format($item->line_total, 0, ',', '.') }}</td></tr>@endforeach
    </tbody></table></div></x-ui.card>
    <x-ui.detail-list :items="[['label' => 'Subtotal', 'value' => 'Rp '.number_format($transaction->subtotal, 0, ',', '.')], ['label' => 'Diskon', 'value' => 'Rp '.number_format($transaction->discount_amount, 0, ',', '.')], ['label' => 'Pajak ('.$transaction->tax_rate.'%)', 'value' => 'Rp '.number_format($transaction->tax_amount, 0, ',', '.')], ['label' => 'Total', 'value' => 'Rp '.number_format($transaction->grand_total, 0, ',', '.')], ['label' => 'Dibayar', 'value' => 'Rp '.number_format($transaction->amount_received, 0, ',', '.')], ['label' => 'Kembalian', 'value' => 'Rp '.number_format($transaction->change_amount, 0, ',', '.')]]" />
    @if ($transaction->status === 'pending' && $transaction->midtrans_snap_token && config('services.midtrans.client_key'))<script src="{{ config('services.midtrans.is_production') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}" data-client-key="{{ config('services.midtrans.client_key') }}"></script>@endif
</x-layouts.admin>
