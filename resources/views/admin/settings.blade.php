<x-layouts.admin title="Pengaturan akun" group="Halaman">
<x-ui.page-header title="Pengaturan akun" description="Kelola profil, keamanan, dan preferensi notifikasi." />

<div data-tabs>
  <div class="tabs-underline" role="tablist" aria-label="Pengaturan">
    <button type="button" role="tab" class="tab" id="st-1" aria-selected="true" aria-controls="sp-1"><x-ui.icon name="user" />Profil</button>
    <button type="button" role="tab" class="tab" id="st-2" aria-selected="false" tabindex="-1" aria-controls="sp-2"><x-ui.icon name="lock" />Keamanan</button>
    <button type="button" role="tab" class="tab" id="st-3" aria-selected="false" tabindex="-1" aria-controls="sp-3"><x-ui.icon name="bell" />Notifikasi</button>
  </div>

  <!-- Profil -->
  <div id="sp-1" role="tabpanel" aria-labelledby="st-1" class="grid gap-4 pt-6 lg:grid-cols-3">
    <div class="card h-fit p-6 text-center">
      <span class="avatar avatar-xl mx-auto">AR</span>
      <h2 class="mt-3 font-semibold">Ayu Rahmawati</h2>
      <p class="text-sm text-muted-foreground">Manajer toko</p>
      <div class="mt-3 flex justify-center gap-2"><span class="badge badge-success badge-dot">Aktif</span><span class="badge badge-soft">Admin</span></div>
      <button type="button" class="btn btn-outline mt-5 w-full"><x-ui.icon name="upload" />Ganti foto</button>
    </div>
    <div class="card lg:col-span-2">
      <div class="card-header"><h2 class="card-title">Informasi profil</h2><p class="card-desc">Perbarui data pribadi Anda.</p></div>
      <div class="card-content grid gap-4 sm:grid-cols-2">
        <div class="space-y-2"><label class="label" for="pf-first">Nama depan</label><input id="pf-first" class="input" value="Ayu"></div>
        <div class="space-y-2"><label class="label" for="pf-last">Nama belakang</label><input id="pf-last" class="input" value="Rahmawati"></div>
        <div class="space-y-2 sm:col-span-2"><label class="label" for="pf-mail">Email</label><input id="pf-mail" type="email" class="input" value="ayu@kopikenanga.id"></div>
        <div class="space-y-2"><label class="label" for="pf-phone">Telepon</label><input id="pf-phone" class="input" value="0812-3456-7890"></div>
        <div class="space-y-2"><label class="label" for="pf-city">Kota</label><select id="pf-city" class="select"><option>Yogyakarta</option><option>Jakarta</option><option>Bandung</option></select></div>
        <div class="space-y-2 sm:col-span-2"><label class="label" for="pf-bio">Bio</label><textarea id="pf-bio" class="textarea">Mengelola operasional Kopi Kenanga sejak 2022.</textarea></div>
      </div>
      <div class="card-footer justify-end"><button type="button" class="btn btn-outline">Batal</button><button type="button" class="btn btn-primary" data-toast="success" data-toast-title="Profil diperbarui">Simpan perubahan</button></div>
    </div>
  </div>

  <!-- Keamanan -->
  <div id="sp-2" role="tabpanel" aria-labelledby="st-2" class="space-y-4 pt-6" hidden>
    <div class="card">
      <div class="card-header"><h2 class="card-title">Ubah kata sandi</h2><p class="card-desc">Gunakan minimal 8 karakter.</p></div>
      <div class="card-content grid max-w-xl gap-4">
        <div class="space-y-2"><label class="label" for="pw-old">Kata sandi saat ini</label><input id="pw-old" type="password" class="input"></div>
        <div class="space-y-2"><label class="label" for="pw-new">Kata sandi baru</label><input id="pw-new" type="password" class="input"></div>
        <div class="space-y-2"><label class="label" for="pw-cf">Konfirmasi kata sandi</label><input id="pw-cf" type="password" class="input"></div>
      </div>
      <div class="card-footer justify-end"><button type="button" class="btn btn-primary" data-toast="success" data-toast-title="Kata sandi diperbarui">Perbarui</button></div>
    </div>
    <div class="card p-6">
      <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-start gap-4"><span class="avatar avatar-success avatar-lg"><x-ui.icon name="shield" class="h-5 w-5" /></span><div><h2 class="font-semibold">Verifikasi dua langkah</h2><p class="text-sm text-muted-foreground">Tambahkan lapisan keamanan dengan aplikasi autentikator.</p></div></div>
        <label class="switch"><input type="checkbox" class="peer sr-only" checked aria-label="Verifikasi dua langkah"><span class="switch-track"></span></label>
      </div>
    </div>
    <div class="rounded-xl border border-danger/30 bg-danger-soft p-6">
      <h2 class="font-semibold text-danger">Zona berbahaya</h2>
      <p class="mt-1 text-sm text-muted-foreground">Menghapus akun akan menghilangkan seluruh data secara permanen.</p>
      <button type="button" class="btn btn-destructive mt-4">Hapus akun</button>
    </div>
  </div>

  <!-- Notifikasi -->
  <div id="sp-3" role="tabpanel" aria-labelledby="st-3" class="pt-6" hidden>
    <div class="card">
      <div class="card-header"><h2 class="card-title">Preferensi notifikasi</h2><p class="card-desc">Pilih kabar yang ingin Anda terima.</p></div>
      <ul class="divide-y border-t">
        <li class="flex items-center justify-between gap-4 px-6 py-4"><div><p class="font-medium">Pesanan baru</p><p class="text-sm text-muted-foreground">Kabari saya setiap ada pesanan masuk.</p></div><label class="switch"><input type="checkbox" class="peer sr-only" checked aria-label="Pesanan baru"><span class="switch-track"></span></label></li>
        <li class="flex items-center justify-between gap-4 px-6 py-4"><div><p class="font-medium">Stok menipis</p><p class="text-sm text-muted-foreground">Peringatan saat stok di bawah batas minimum.</p></div><label class="switch"><input type="checkbox" class="peer sr-only" checked aria-label="Stok menipis"><span class="switch-track"></span></label></li>
        <li class="flex items-center justify-between gap-4 px-6 py-4"><div><p class="font-medium">Ringkasan mingguan</p><p class="text-sm text-muted-foreground">Laporan performa dikirim tiap Senin pagi.</p></div><label class="switch"><input type="checkbox" class="peer sr-only" aria-label="Ringkasan mingguan"><span class="switch-track"></span></label></li>
        <li class="flex items-center justify-between gap-4 px-6 py-4"><div><p class="font-medium">Promo dan berita</p><p class="text-sm text-muted-foreground">Info fitur baru dan penawaran.</p></div><label class="switch"><input type="checkbox" class="peer sr-only" aria-label="Promo dan berita"><span class="switch-track"></span></label></li>
      </ul>
    </div>
  </div>
</div>
</x-layouts.admin>
