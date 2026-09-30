<x-layouts.admin title="Analitik" group="Platform">
<div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
  <x-ui.page-header title="Analitik" description="Galeri grafik: area, garis, batang, tumpuk, donat, radial, dan sparkline." />
  <div class="tabs-pill" role="tablist" data-tabs>
    <button type="button" role="tab" class="tab" aria-selected="false" tabindex="-1">7 hari</button>
    <button type="button" role="tab" class="tab" aria-selected="true">30 hari</button>
    <button type="button" role="tab" class="tab" aria-selected="false" tabindex="-1">90 hari</button>
  </div>
</div>

<x-ui.card title="Pengunjung dan pesanan" description="Grafik area dengan dua seri. Arahkan kursor untuk melihat tooltip." class="min-w-0">
  <div data-chart="area" data-smooth data-height="300"
    data-labels='["1","4","7","10","13","16","19","22","25","28"]'
    data-series='[{"name":"Pengunjung","data":[420,510,480,620,590,710,680,790,760,880]},{"name":"Pesanan","data":[38,52,47,66,58,79,71,88,82,97],"color":"2"}]'></div>
</x-ui.card>

<section class="grid gap-4 lg:grid-cols-2">
  <x-ui.card title="Tingkat konversi per kanal" description="Grafik garis, tiga seri (%)." class="min-w-0">
    <div data-chart="line" data-smooth data-height="260" data-suffix="%"
      data-labels='["Sen","Sel","Rab","Kam","Jum","Sab","Min"]'
      data-series='[{"name":"Organik","data":[3.1,3.4,3.2,3.8,3.6,4.1,4.4]},{"name":"Sosial media","data":[2.2,2.6,2.4,2.9,3.1,3.5,3.3],"color":"2"},{"name":"Iklan","data":[1.6,1.9,2.1,1.8,2.3,2.2,2.6],"color":"3"}]'></div>
  </x-ui.card>

  <x-ui.card title="Pendapatan vs target" description="Batang berkelompok (juta rupiah)." class="min-w-0">
    <div data-chart="bar" data-height="260" data-prefix="Rp " data-suffix=" jt"
      data-labels='["Apr","Mei","Jun","Jul","Agu","Sep"]'
      data-series='[{"name":"Aktual","data":[39.6,44.3,47.9,45.2,51.6,58.4]},{"name":"Target","data":[40,42,45,48,50,55],"color":"5"}]'></div>
  </x-ui.card>
</section>

<section class="grid gap-4 lg:grid-cols-3">
  <x-ui.card title="Pesanan menurut kanal" description="Batang bertumpuk, 7 hari terakhir." class="min-w-0 lg:col-span-2">
    <div data-chart="stacked" data-height="270"
      data-labels='["Sen","Sel","Rab","Kam","Jum","Sab","Min"]'
      data-series='[{"name":"Situs web","data":[22,26,24,31,29,38,35]},{"name":"Marketplace","data":[14,18,16,21,24,27,25],"color":"2"},{"name":"WhatsApp","data":[8,9,12,10,13,15,14],"color":"3"}]'></div>
  </x-ui.card>

  <x-ui.card title="Perangkat" description="Donat dengan legenda interaktif.">
    <div data-chart="donut" data-size="150" data-total-label="Sesi"
      data-items='[{"label":"Seluler","value":6240},{"label":"Desktop","value":3120},{"label":"Tablet","value":740}]'></div>
  </x-ui.card>
</section>

<section class="grid gap-4 md:grid-cols-3">
  <div class="card p-6">
    <h2 class="card-title">Target penjualan</h2>
    <p class="card-desc mt-1.5">Bulan September</p>
    <div class="mt-5 flex items-center gap-5">
      <div data-chart="radial" data-value="78" data-label="Tercapai" data-color="1"></div>
      <dl class="space-y-2 text-sm"><div><dt class="text-muted-foreground">Aktual</dt><dd class="font-medium">Rp 58,4 jt</dd></div><div><dt class="text-muted-foreground">Target</dt><dd class="font-medium">Rp 75 jt</dd></div></dl>
    </div>
  </div>
  <div class="card p-6">
    <h2 class="card-title">Kepuasan pelanggan</h2>
    <p class="card-desc mt-1.5">Rata-rata ulasan 30 hari</p>
    <div class="mt-5 flex items-center gap-5">
      <div data-chart="radial" data-value="92" data-label="Puas" data-color="2"></div>
      <dl class="space-y-2 text-sm"><div><dt class="text-muted-foreground">Rating</dt><dd class="font-medium">4,8 / 5</dd></div><div><dt class="text-muted-foreground">Ulasan</dt><dd class="font-medium">1.126</dd></div></dl>
    </div>
  </div>
  <div class="card p-6">
    <h2 class="card-title">Kapasitas gudang</h2>
    <p class="card-desc mt-1.5">Terisi saat ini</p>
    <div class="mt-5 flex items-center gap-5">
      <div data-chart="radial" data-value="64" data-label="Terisi" data-color="3"></div>
      <dl class="space-y-2 text-sm"><div><dt class="text-muted-foreground">Terpakai</dt><dd class="font-medium">640 rak</dd></div><div><dt class="text-muted-foreground">Total</dt><dd class="font-medium">1.000 rak</dd></div></dl>
    </div>
  </div>
</section>

<section class="grid gap-4 lg:grid-cols-2">
  <x-ui.card title="Produk terlaris" description="Batang horizontal (unit terjual).">
    <div data-chart="hbar" data-suffix=" unit" data-items='[{"label":"Arabika Gayo 1 kg","value":412},{"label":"Paket coba 5 varian","value":338},{"label":"Robusta Temanggung 1 kg","value":276},{"label":"French press 600 ml","value":181},{"label":"Toraja Sapan 500 g","value":149}]'></div>
  </x-ui.card>

  <x-ui.card title="Kota teratas" description="Pesanan menurut kota tujuan.">
    <div data-chart="hbar" data-color="2" data-items='[{"label":"Yogyakarta","value":540},{"label":"Jakarta","value":486},{"label":"Bandung","value":312},{"label":"Surabaya","value":254},{"label":"Semarang","value":198}]'></div>
  </x-ui.card>
</section>

<div class="card min-w-0">
  <div class="card-header">
    <h2 class="card-title">Kanal akuisisi</h2>
    <p class="card-desc">Sparkline di dalam tabel (garis, batang, dan area).</p>
  </div>
  <div class="overflow-x-auto border-t">
    <table class="table min-w-[600px]">
      <thead><tr><th>Kanal</th><th class="text-right">Kunjungan</th><th class="text-right">Konversi</th><th class="w-44">Tren 7 hari</th></tr></thead>
      <tbody>
        <tr><td class="font-medium">Pencarian organik</td><td class="text-right tabular-nums">4.200</td><td class="text-right tabular-nums">4,4%</td><td><div data-chart="spark" data-kind="area" data-color="success" data-values="[12,14,13,17,16,19,22]" data-height="32"></div></td></tr>
        <tr><td class="font-medium">Media sosial</td><td class="text-right tabular-nums">2.900</td><td class="text-right tabular-nums">3,3%</td><td><div data-chart="spark" data-kind="bar" data-color="chart-1" data-values="[8,11,9,13,12,15,14]" data-height="32"></div></td></tr>
        <tr><td class="font-medium">Langsung</td><td class="text-right tabular-nums">1.800</td><td class="text-right tabular-nums">2,9%</td><td><div data-chart="spark" data-kind="line" data-color="info" data-values="[9,8,9,10,9,11,10]" data-height="32"></div></td></tr>
        <tr><td class="font-medium">Iklan berbayar</td><td class="text-right tabular-nums">1.100</td><td class="text-right tabular-nums">2,6%</td><td><div data-chart="spark" data-kind="area" data-color="danger" data-values="[14,13,12,12,10,9,8]" data-height="32"></div></td></tr>
      </tbody>
    </table>
  </div>
</div>
</x-layouts.admin>
