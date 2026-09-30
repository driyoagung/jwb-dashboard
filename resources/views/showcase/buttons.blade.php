<x-layouts.admin title="Tombol dan lencana" group="Komponen">
<x-ui.page-header title="Tombol dan lencana" description="Tombol, grup tombol, lencana, avatar, chip, dan tooltip." />

<x-ui.card title="Varian tombol" description="Setiap varian punya class sendiri, misalnya btn-primary.">
  <div class="flex flex-wrap items-center gap-3">
    <button type="button" class="btn btn-primary">Primary</button>
    <button type="button" class="btn btn-secondary">Secondary</button>
    <button type="button" class="btn btn-outline">Outline</button>
    <button type="button" class="btn btn-ghost">Ghost</button>
    <button type="button" class="btn btn-soft">Soft</button>
    <button type="button" class="btn btn-destructive">Destructive</button>
    <button type="button" class="btn btn-success">Success</button>
    <button type="button" class="btn btn-warning">Warning</button>
    <button type="button" class="btn btn-info">Info</button>
    <button type="button" class="btn btn-link">Link</button>
  </div>
</x-ui.card>

<section class="grid gap-4 lg:grid-cols-2">
  <x-ui.card title="Ukuran" description="btn-sm, default, dan btn-lg.">
    <div class="flex flex-wrap items-center gap-3">
      <button type="button" class="btn btn-primary btn-sm">Kecil</button>
      <button type="button" class="btn btn-primary">Default</button>
      <button type="button" class="btn btn-primary btn-lg">Besar</button>
    </div>
    <div class="mt-4 flex flex-wrap items-center gap-3">
      <button type="button" class="btn btn-outline btn-icon btn-sm" aria-label="Tambah"><x-ui.icon name="plus" /></button>
      <button type="button" class="btn btn-outline btn-icon" aria-label="Tambah"><x-ui.icon name="plus" /></button>
      <button type="button" class="btn btn-outline btn-icon btn-lg" aria-label="Tambah"><x-ui.icon name="plus" class="h-5 w-5" /></button>
      <button type="button" class="btn btn-primary btn-icon rounded-full" aria-label="Edit"><x-ui.icon name="edit" /></button>
    </div>
  </x-ui.card>

  <x-ui.card title="Dengan ikon" description="Ikon di kiri atau kanan teks.">
    <div class="flex flex-wrap items-center gap-3">
      <button type="button" class="btn btn-primary"><x-ui.icon name="plus" />Tambah produk</button>
      <button type="button" class="btn btn-outline"><x-ui.icon name="download" />Unduh</button>
      <button type="button" class="btn btn-secondary">Lanjut<x-ui.icon name="arrow-right" /></button>
      <button type="button" class="btn btn-destructive"><x-ui.icon name="trash" />Hapus</button>
    </div>
  </x-ui.card>

  <x-ui.card title="Status" description="Memuat (klik untuk mencoba), nonaktif, dan lebar penuh.">
    <div class="flex flex-wrap items-center gap-3">
      <button type="button" class="btn btn-primary" data-load><x-ui.icon name="refresh" />Muat ulang</button>
      <button type="button" class="btn btn-primary" disabled><span class="spinner"></span>Menyimpan</button>
      <button type="button" class="btn btn-outline" disabled>Nonaktif</button>
    </div>
    <button type="button" class="btn btn-primary mt-4 w-full">Lebar penuh</button>
  </x-ui.card>

  <x-ui.card title="Grup tombol" description="btn-group, tombol terpisah, dan tombol dengan menu.">
    <div class="flex flex-wrap items-center gap-4">
      <div class="btn-group">
        <button type="button" class="btn btn-outline">Hari</button>
        <button type="button" class="btn btn-outline">Minggu</button>
        <button type="button" class="btn btn-outline">Bulan</button>
      </div>
      <div data-dropdown>
        <div class="btn-group">
          <button type="button" class="btn btn-primary">Simpan</button>
          <button type="button" class="btn btn-primary btn-icon border-l border-primary-foreground/25" data-dropdown-toggle aria-expanded="false" aria-label="Opsi lain"><x-ui.icon name="chevron-down" /></button>
        </div>
        <div class="menu" hidden>
          <button type="button" class="menu-item">Simpan sebagai draf</button>
          <button type="button" class="menu-item">Simpan dan tutup</button>
        </div>
      </div>
    </div>
  </x-ui.card>
</section>

<section class="grid gap-4 lg:grid-cols-2">
  <x-ui.card title="Lencana" description="Soft, solid, outline, titik, dan pil.">
    <div class="space-y-4">
      <div class="flex flex-wrap items-center gap-2">
        <span class="badge badge-primary">Primary</span><span class="badge badge-soft">Soft</span><span class="badge badge-neutral">Neutral</span>
        <span class="badge badge-success">Success</span><span class="badge badge-warning">Warning</span><span class="badge badge-danger">Danger</span><span class="badge badge-info">Info</span>
      </div>
      <div class="flex flex-wrap items-center gap-2">
        <span class="badge badge-solid-success">Solid</span><span class="badge badge-solid-warning">Solid</span><span class="badge badge-solid-danger">Solid</span><span class="badge badge-solid-info">Solid</span><span class="badge badge-outline">Outline</span>
      </div>
      <div class="flex flex-wrap items-center gap-2">
        <span class="badge badge-success badge-dot">Aktif</span><span class="badge badge-warning badge-dot">Menunggu</span><span class="badge badge-danger badge-dot">Gagal</span>
        <span class="badge badge-soft badge-pill">Pil</span><span class="badge badge-primary badge-pill">12</span>
      </div>
      <div class="flex flex-wrap items-center gap-3">
        <button type="button" class="btn btn-outline">Pesan <span class="badge badge-primary badge-pill">8</span></button>
        <button type="button" class="btn btn-secondary">Notifikasi <span class="badge badge-solid-danger badge-pill">3</span></button>
      </div>
    </div>
  </x-ui.card>

  <x-ui.card title="Avatar" description="Ukuran, warna, status, dan grup bertumpuk.">
    <div class="space-y-4">
      <div class="flex flex-wrap items-center gap-3">
        <span class="avatar avatar-sm">AR</span><span class="avatar">AR</span><span class="avatar avatar-lg">AR</span><span class="avatar avatar-xl">AR</span>
        <span class="avatar avatar-square avatar-lg avatar-info">KK</span>
      </div>
      <div class="flex flex-wrap items-center gap-3">
        <span class="avatar avatar-lg avatar-success">DA<span class="avatar-dot"></span></span>
        <span class="avatar avatar-lg avatar-warning">BP</span><span class="avatar avatar-lg avatar-danger">MK</span><span class="avatar avatar-lg avatar-neutral"><x-ui.icon name="user" /></span>
      </div>
      <div class="flex items-center gap-3">
        <div class="avatar-group"><span class="avatar avatar-soft">DA</span><span class="avatar avatar-info">BP</span><span class="avatar avatar-success">MK</span><span class="avatar avatar-warning">RH</span><span class="avatar avatar-neutral">+4</span></div>
        <span class="text-sm text-muted-foreground">8 anggota tim</span>
      </div>
    </div>
  </x-ui.card>
</section>

<section class="grid gap-4 lg:grid-cols-2">
  <x-ui.card title="Chip dan tag" description="Chip dengan tombol hapus (klik untuk menghapus).">
    <div class="flex flex-wrap gap-2" data-dismissible-group>
      <span class="chip" data-dismissible>Arabika<button type="button" data-dismiss class="rounded-full p-0.5 text-muted-foreground hover:bg-muted hover:text-foreground" aria-label="Hapus Arabika"><x-ui.icon name="x" class="h-3 w-3" /></button></span>
      <span class="chip" data-dismissible>Robusta<button type="button" data-dismiss class="rounded-full p-0.5 text-muted-foreground hover:bg-muted hover:text-foreground" aria-label="Hapus Robusta"><x-ui.icon name="x" class="h-3 w-3" /></button></span>
      <span class="chip" data-dismissible>Single origin<button type="button" data-dismiss class="rounded-full p-0.5 text-muted-foreground hover:bg-muted hover:text-foreground" aria-label="Hapus Single origin"><x-ui.icon name="x" class="h-3 w-3" /></button></span>
      <span class="chip" data-dismissible>Medium roast<button type="button" data-dismiss class="rounded-full p-0.5 text-muted-foreground hover:bg-muted hover:text-foreground" aria-label="Hapus Medium roast"><x-ui.icon name="x" class="h-3 w-3" /></button></span>
    </div>
    <p class="mt-4 text-sm text-muted-foreground">Pintasan papan ketik: <span class="kbd">Ctrl</span> <span class="kbd">K</span> untuk mencari, <span class="kbd">Esc</span> untuk menutup.</p>
  </x-ui.card>

  <x-ui.card title="Tooltip" description="Tanpa JavaScript, cukup atribut data-tip.">
    <div class="flex flex-wrap items-center gap-3">
      <button type="button" class="btn btn-outline btn-icon" data-tip="Ubah" aria-label="Ubah"><x-ui.icon name="edit" /></button>
      <button type="button" class="btn btn-outline btn-icon" data-tip="Salin tautan" aria-label="Salin tautan"><x-ui.icon name="copy" /></button>
      <button type="button" class="btn btn-outline btn-icon" data-tip="Unduh" aria-label="Unduh"><x-ui.icon name="download" /></button>
      <button type="button" class="btn btn-outline" data-tip="Tooltip pada tombol teks">Arahkan kursor</button>
    </div>
  </x-ui.card>
</section>
</x-layouts.admin>
