<x-layouts.admin title="Riwayat stok" group="Inventori">
    <x-ui.page-header title="Riwayat stok" description="Jejak perubahan stok yang tersimpan." />
    <div class="card" data-pos-server-table>
        <div class="p-6 pb-4"><h2 class="card-title">Pergerakan stok</h2><p class="card-desc mt-1.5">Cari jejak perubahan berdasarkan produk.</p></div>
        <x-ui.filter-bar :action="route('pos.manage.stock.movements')" :reset-url="route('pos.manage.stock.movements')" placeholder="Cari nama atau SKU produk" embedded live />
        <div data-pos-results>
            @if ($movements->isEmpty()) <div class="border-t"><x-ui.table-state :state="request()->filled('q') ? 'filtered' : 'empty'" title="Belum ada perubahan stok" /></div>
            @else <div class="overflow-x-auto border-t"><table class="table table-striped min-w-[760px]"><thead><tr><th>Waktu</th><th>Produk</th><th>Jenis</th><th>Perubahan</th><th>Sebelum → Sesudah</th><th>Aktor</th><th>Alasan</th></tr></thead><tbody>
                @foreach ($movements as $movement)<tr><td class="text-muted-foreground">{{ $movement->created_at->format('d M Y H:i') }}</td><td class="font-medium">{{ $movement->product->name }}</td><td><x-ui.badge :variant="$movement->delta_qty < 0 ? 'warning' : 'success'" dot>{{ $movement->type }}</x-ui.badge></td><td class="tabular-nums">{{ $movement->delta_qty > 0 ? '+' : '' }}{{ $movement->delta_qty }}</td><td class="tabular-nums">{{ $movement->before_qty }} → {{ $movement->after_qty }}</td><td>{{ $movement->user->name }}</td><td>{{ $movement->reason }}</td></tr>@endforeach
            </tbody></table></div>@endif
            <x-ui.pagination :paginator="$movements" />
        </div>
    </div>
</x-layouts.admin>
