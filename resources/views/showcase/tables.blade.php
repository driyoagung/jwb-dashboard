<x-layouts.admin title="Tabel" group="Komponen">
<x-ui.page-header title="Tabel" description="Tabel interaktif, bergaris, padat dengan total, produk dengan progres, dan status kosong." />

<!-- 1. Tabel data interaktif -->
<div class="card" data-table data-page-size="8">
  <div class="flex flex-col gap-3 p-6 pb-4 sm:flex-row sm:items-center sm:justify-between">
    <div>
      <h2 class="card-title">Pelanggan</h2>
      <p class="card-desc mt-1.5">Cari, filter, urutkan (klik judul kolom), pilih baris, dan paginasi.</p>
    </div>
    <button type="button" class="btn btn-primary btn-sm"><x-ui.icon name="plus" />Tambah pelanggan</button>
  </div>

  <div class="flex flex-col gap-2 border-t px-6 py-3 sm:flex-row sm:items-center">
    <div class="relative sm:w-64">
      <x-ui.icon name="search" class="pointer-events-none absolute left-2.5 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
      <input type="search" class="input pl-8" placeholder="Cari nama atau email" aria-label="Cari pelanggan" data-table-search>
    </div>
    <select class="select sm:w-40" aria-label="Filter status" data-table-filter>
      <option value="">Semua status</option>
      <option value="aktif">Aktif</option>
      <option value="menunggu">Menunggu</option>
      <option value="nonaktif">Nonaktif</option>
    </select>
    <div data-bulk hidden class="flex items-center gap-2 text-sm sm:ml-auto">
      <span class="text-muted-foreground"><span data-bulk-count class="font-medium text-foreground">0</span> dipilih</span>
      <button type="button" class="btn btn-outline btn-sm"><x-ui.icon name="download" />Ekspor</button>
      <button type="button" class="btn btn-destructive btn-sm" data-toast="danger" data-toast-title="Contoh: data dihapus"><x-ui.icon name="trash" />Hapus</button>
    </div>
  </div>

  <div class="overflow-x-auto border-t">
    <table class="table min-w-[760px]">
      <thead>
        <tr>
          <th class="w-10"><input type="checkbox" data-check-all aria-label="Pilih semua"></th>
          <th data-sort>Pelanggan</th>
          <th data-sort>Peran</th>
          <th>Status</th>
          <th data-sort="num" class="text-right">Pesanan</th>
          <th data-sort>Terakhir aktif</th>
          <th class="w-12"><span class="sr-only">Aksi</span></th>
        </tr>
      </thead>
      <tbody>
        @foreach (config('kenanga.demo.users') as $item)<tr data-status="{{ $item['statusKey'] }}">
          <td><input type="checkbox" data-check-row aria-label="Pilih {{ $item['name'] }}"></td>
          <td><div class="flex items-center gap-3"><span class="avatar avatar-{{ $item['avTone'] }}">{{ $item['initials'] }}</span><div class="leading-tight"><p class="font-medium">{{ $item['name'] }}</p><p class="text-xs text-muted-foreground">{{ $item['email'] }}</p></div></div></td>
          <td>{{ $item['role'] }}</td>
          <td><span class="badge badge-{{ $item['tone'] }} badge-dot">{{ $item['status'] }}</span></td>
          <td class="text-right tabular-nums" data-value="{{ $item['orders'] }}">{{ $item['orders'] }}</td>
          <td class="text-muted-foreground">{{ $item['seen'] }}</td>
          <td class="text-right">
            <div data-dropdown>
              <button type="button" data-dropdown-toggle aria-expanded="false" aria-label="Aksi untuk {{ $item['name'] }}" class="btn btn-ghost btn-icon btn-sm text-muted-foreground"><x-ui.icon name="more" /></button>
              <div class="menu menu-right" hidden>
                <button type="button" class="menu-item"><x-ui.icon name="eye" />Lihat detail</button>
                <button type="button" class="menu-item"><x-ui.icon name="edit" />Ubah</button>
                <button type="button" class="menu-item"><x-ui.icon name="mail" />Kirim email</button>
                <div class="menu-sep"></div>
                <button type="button" class="menu-item menu-item-danger"><x-ui.icon name="trash" />Hapus</button>
              </div>
            </div>
          </td>
        </tr>@endforeach
      </tbody>
    </table>
    <div data-table-empty hidden class="p-10 text-center text-muted-foreground">Tidak ada data yang cocok dengan pencarian.</div>
  </div>

  <div class="flex flex-col items-center justify-between gap-3 border-t px-6 py-3 sm:flex-row">
    <p data-table-info class="text-sm text-muted-foreground"></p>
    <div data-table-pager class="flex items-center gap-1"></div>
  </div>
</div>

<!-- 2 dan 3 -->
<section class="grid gap-4 xl:grid-cols-2">
  <div class="card min-w-0">
    <div class="card-header"><h2 class="card-title">Tabel bergaris selang-seling</h2><p class="card-desc">Varian <code class="rounded bg-muted px-1 py-0.5 text-xs">table-striped</code>.</p></div>
    <div class="overflow-x-auto border-t">
      <table class="table table-striped min-w-[480px]">
        <thead><tr><th>Pesanan</th><th>Pelanggan</th><th>Status</th><th class="text-right">Total</th></tr></thead>
        <tbody>
          @foreach (config('kenanga.demo.orders') as $item)<tr><td class="font-medium">{{ $item['id'] }}</td><td>{{ $item['customer'] }}</td><td><span class="badge badge-{{ $item['tone'] }}">{{ $item['status'] }}</span></td><td class="text-right tabular-nums {{ $item['struck'] }}">{{ $item['total'] }}</td></tr>@endforeach
        </tbody>
      </table>
    </div>
  </div>

  <div class="card min-w-0">
    <div class="card-header"><h2 class="card-title">Tabel padat dengan total</h2><p class="card-desc">Varian <code class="rounded bg-muted px-1 py-0.5 text-xs">table-compact table-bordered</code> dan <code class="rounded bg-muted px-1 py-0.5 text-xs">tfoot</code>.</p></div>
    <div class="card-content overflow-x-auto">
      <table class="table table-compact table-bordered min-w-[440px]">
        <thead><tr><th>Item</th><th class="text-right">Jumlah</th><th class="text-right">Harga</th><th class="text-right">Subtotal</th></tr></thead>
        <tbody>
          <tr><td>Arabika Gayo 1 kg</td><td class="text-right tabular-nums">2</td><td class="text-right tabular-nums">170.000</td><td class="text-right tabular-nums">340.000</td></tr>
          <tr><td>Toraja Sapan 500 g</td><td class="text-right tabular-nums">1</td><td class="text-right tabular-nums">128.000</td><td class="text-right tabular-nums">128.000</td></tr>
          <tr><td>French press 600 ml</td><td class="text-right tabular-nums">1</td><td class="text-right tabular-nums">265.000</td><td class="text-right tabular-nums">265.000</td></tr>
          <tr><td>Ongkos kirim</td><td class="text-right tabular-nums">1</td><td class="text-right tabular-nums">22.000</td><td class="text-right tabular-nums">22.000</td></tr>
        </tbody>
        <tfoot><tr><td colspan="3" class="text-right">Total</td><td class="text-right tabular-nums">Rp 755.000</td></tr></tfoot>
      </table>
    </div>
  </div>
</section>

<!-- 4. Produk -->
<div class="card">
  <div class="flex items-center justify-between gap-4 p-6 pb-4">
    <div><h2 class="card-title">Inventaris produk</h2><p class="card-desc mt-1.5">Bilah progres stok, rating, dan status.</p></div>
    <button type="button" class="btn btn-outline btn-sm"><x-ui.icon name="filter" />Filter</button>
  </div>
  <div class="overflow-x-auto border-t">
    <table class="table min-w-[760px]">
      <thead><tr><th>Produk</th><th>Kategori</th><th class="w-48">Stok</th><th>Rating</th><th>Status</th><th class="text-right">Harga</th></tr></thead>
      <tbody>
        @foreach (config('kenanga.demo.products') as $item)<tr>
          <td><div class="flex items-center gap-3"><span class="avatar avatar-square avatar-neutral"><x-ui.icon name="coffee" /></span><div class="leading-tight"><p class="font-medium">{{ $item['name'] }}</p><p class="text-xs text-muted-foreground">{{ $item['sku'] }}</p></div></div></td>
          <td>{{ $item['category'] }}</td>
          <td><div class="flex items-center gap-3"><div class="progress"><div class="progress-bar {{ $item['bar'] }}" style="width:{{ $item['pct'] }}%"></div></div><span class="w-8 text-right text-xs tabular-nums text-muted-foreground">{{ $item['stock'] }}</span></div></td>
          <td><span class="inline-flex items-center gap-1"><x-ui.icon name="star" class="h-3.5 w-3.5 text-caution" />{{ $item['rating'] }}</span></td>
          <td><span class="badge badge-{{ $item['tone'] }} badge-dot">{{ $item['status'] }}</span></td>
          <td class="text-right font-medium tabular-nums">{{ $item['price'] }}</td>
        </tr>@endforeach
      </tbody>
    </table>
  </div>
</div>

<!-- 5. Status kosong -->
<div class="card">
  <div class="flex flex-col items-center gap-3 px-6 py-14 text-center">
    <span class="flex h-12 w-12 items-center justify-center rounded-full bg-muted text-muted-foreground"><x-ui.icon name="package" class="h-6 w-6" /></span>
    <div>
      <h2 class="font-semibold">Belum ada pesanan</h2>
      <p class="mx-auto mt-1 max-w-sm text-muted-foreground">Pesanan yang masuk akan tampil di sini. Bagikan tautan toko Anda untuk mulai menerima pesanan.</p>
    </div>
    <div class="mt-2 flex gap-2"><button type="button" class="btn btn-outline">Salin tautan toko</button><button type="button" class="btn btn-primary"><x-ui.icon name="plus" />Tambah produk</button></div>
  </div>
</div>
</x-layouts.admin>
