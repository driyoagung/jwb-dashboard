<x-layouts.admin title="Dashboard" group="Platform">
<div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
  <x-ui.page-header title="Ringkasan" description="Performa toko Anda selama 30 hari terakhir." />
  <div class="grid gap-2 sm:flex sm:items-center">
    <button type="button" class="btn btn-outline w-full sm:w-auto"><x-ui.icon name="calendar" class="h-4 w-4 text-muted-foreground" />29 Agu – 28 Sep 2026</button>
    <button type="button" class="btn btn-primary w-full sm:w-auto" data-toast="success" data-toast-title="Laporan sedang disiapkan" data-toast-desc="Berkas akan diunduh otomatis."><x-ui.icon name="download" />Unduh laporan</button>
  </div>
</div>

<!-- Statistik -->
<section aria-label="Statistik utama" class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
  <x-ui.stat-card title="Total pendapatan" value="Rp 58,4 jt" icon="dollar" tile="bg-soft text-soft-foreground" tone="success" direction="up" delta="+13,2%" spark="[41,45,44,48,47,52,58]" />
  <x-ui.stat-card title="Pelanggan baru" value="1.284" icon="userplus" tile="bg-success-soft text-success" tone="success" direction="up" delta="+8,1%" spark="[30,34,33,38,36,41,44]" />
  <x-ui.stat-card title="Pesanan aktif" value="356" icon="cart" tile="bg-warning-soft text-warning" tone="danger" direction="down" delta="−2,4%" spark="[60,58,61,57,59,55,54]" />
  <x-ui.stat-card title="Tingkat konversi" value="3,42%" icon="activity" tile="bg-info-soft text-info" tone="success" direction="up" delta="+0,3 poin" spark="[2.8,3,2.9,3.1,3.2,3.3,3.4]" />
</section>

<!-- Grafik + penjualan -->
<section class="grid gap-4 lg:grid-cols-7">
  <div class="card min-w-0 lg:col-span-4">
    <div class="card-header">
      <h2 class="card-title">Pendapatan</h2>
      <p class="card-desc">12 bulan terakhir. Total Rp 521 juta.</p>
    </div>
    <div class="card-content">
      <div data-chart="bar" data-height="280" data-prefix="Rp " data-suffix=" jt"
        data-labels='["Okt","Nov","Des","Jan","Feb","Mar","Apr","Mei","Jun","Jul","Agu","Sep"]'
        data-series='[{"name":"Pendapatan","data":[31.2,38.5,52.1,36.4,34.8,41,39.6,44.3,47.9,45.2,51.6,58.4]}]'></div>
    </div>
  </div>

  <div class="card lg:col-span-3">
    <div class="card-header">
      <h2 class="card-title">Penjualan terbaru</h2>
      <p class="card-desc">356 pesanan masuk bulan ini.</p>
    </div>
    <ul class="space-y-5 p-6 pt-0">
      @foreach (config('kenanga.demo.sales') as $item)<li class="flex items-center gap-3">
        <span class="avatar avatar-{{ $item['tone'] }}">{{ $item['initials'] }}</span>
        <div class="min-w-0 flex-1 leading-tight"><p class="truncate font-medium">{{ $item['name'] }}</p><p class="truncate text-xs text-muted-foreground">{{ $item['email'] }}</p></div>
        <span class="font-medium tabular-nums">{{ $item['amount'] }}</span>
      </li>@endforeach
    </ul>
  </div>
</section>

<!-- Pesanan + sumber trafik -->
<section class="grid gap-4 lg:grid-cols-5">
  <div class="card min-w-0 lg:col-span-3">
    <div class="flex items-center justify-between gap-4 p-6 pb-4">
      <div>
        <h2 class="card-title">Pesanan terbaru</h2>
        <p class="card-desc mt-1.5">Enam pesanan terakhir dari toko Anda.</p>
      </div>
      @if (config('kenanga.showcase'))
        <a href="{{ route('showcase.tables') }}" class="btn btn-outline btn-sm">Lihat semua</a>
      @endif
    </div>
    <div class="overflow-x-auto border-t">
      <table class="table min-w-[640px]">
        <thead><tr><th>Pesanan</th><th>Pelanggan</th><th class="hidden lg:table-cell">Produk</th><th>Status</th><th class="hidden md:table-cell">Tanggal</th><th class="text-right">Total</th></tr></thead>
        <tbody>
          @foreach (config('kenanga.demo.orders') as $item)<tr>
            <td class="font-medium">{{ $item['id'] }}</td>
            <td>{{ $item['customer'] }}</td>
            <td class="hidden text-muted-foreground lg:table-cell">{{ $item['product'] }}</td>
            <td><span class="badge badge-{{ $item['tone'] }} badge-dot">{{ $item['status'] }}</span></td>
            <td class="hidden text-muted-foreground md:table-cell">{{ $item['date'] }}</td>
            <td class="text-right tabular-nums {{ $item['struck'] }}">{{ $item['total'] }}</td>
          </tr>@endforeach
        </tbody>
      </table>
    </div>
  </div>

  <div class="card min-w-0 lg:col-span-2">
    <div class="card-header">
      <h2 class="card-title">Sumber trafik</h2>
      <p class="card-desc">Kunjungan 30 hari terakhir.</p>
    </div>
    <div class="card-content">
      <div data-chart="donut" data-size="160" data-total-label="Kunjungan"
        data-items='[{"label":"Pencarian organik","value":4200},{"label":"Media sosial","value":2900},{"label":"Langsung","value":1800},{"label":"Iklan berbayar","value":1100}]'></div>
    </div>
  </div>
</section>
</x-layouts.admin>
