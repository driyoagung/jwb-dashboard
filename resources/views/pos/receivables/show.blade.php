<x-layouts.admin :title="'Kasbon '.$receivable->transaction->transaction_number" group="Penjualan">
    <x-ui.page-header :title="'Kasbon '.$receivable->transaction->transaction_number" description="Rincian tagihan dan riwayat pembayaran." />
    <x-ui.detail-list :items="[['label' => 'Pelanggan', 'value' => $receivable->customer->name], ['label' => 'Telepon', 'value' => $receivable->customer->phone ?: '-'], ['label' => 'Total tagihan', 'value' => 'Rp '.number_format($receivable->total_amount, 0, ',', '.')], ['label' => 'Sudah dibayar', 'value' => 'Rp '.number_format($receivable->paid_amount, 0, ',', '.')], ['label' => 'Sisa tagihan', 'value' => 'Rp '.number_format($receivable->balance, 0, ',', '.')], ['label' => 'Jatuh tempo', 'value' => $receivable->due_date?->format('d M Y') ?? '-'], ['label' => 'Catatan', 'value' => $receivable->note ?: '-']]" />
    <div class="my-4"><a href="{{ route('pos.transactions.show', $receivable->transaction) }}" class="btn btn-outline btn-sm"><x-ui.icon name="eye" />Lihat transaksi</a></div>
    @if ($receivable->balance > 0)<x-ui.card title="Catat pembayaran" class="mb-6">
        <form method="POST" action="{{ route('pos.receivables.payments.store', $receivable) }}" class="space-y-4">@csrf<input type="hidden" name="idempotency_key" value="{{ $key }}">
            <x-ui.field label="Jumlah dibayar (Rp)" name="amount"><x-ui.input name="amount" type="number" min="1" :max="$receivable->balance" placeholder="Masukkan nominal cicilan" data-grouped-number required /></x-ui.field>
            <x-ui.field label="Metode" name="method"><x-ui.select name="method" placeholder="Pilih metode pembayaran" :options="['cash' => 'Tunai', 'transfer' => 'Transfer', 'qris' => 'QRIS']" required /></x-ui.field>
            <x-ui.field label="Catatan (opsional)" name="note"><x-ui.input name="note" placeholder="Contoh: cicilan pertama" /></x-ui.field>
            <x-ui.button type="submit"><x-ui.icon name="check" />Catat pembayaran</x-ui.button>
        </form>
    </x-ui.card>@endif
    <x-ui.card title="Riwayat pembayaran">
        @if ($receivable->payments->isEmpty())<x-ui.table-state state="empty" title="Belum ada pembayaran" />
        @else<div class="overflow-x-auto"><table class="table table-striped"><thead><tr><th>Waktu</th><th>Kasir</th><th>Metode</th><th>Nominal</th><th>Catatan</th></tr></thead><tbody>@foreach ($receivable->payments as $payment)<tr><td>{{ $payment->created_at->format('d M Y H:i') }}</td><td>{{ $payment->user->name }}</td><td><x-ui.badge variant="info">{{ strtoupper($payment->method) }}</x-ui.badge></td><td>Rp {{ number_format($payment->amount, 0, ',', '.') }}</td><td>{{ $payment->note ?: '-' }}</td></tr>@endforeach</tbody></table></div>@endif
    </x-ui.card>
</x-layouts.admin>
