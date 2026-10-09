# Alur implementasi halaman dan dashboard role

1. **Cari pola terdekat.** Baca `docs/komponen/index.md`, halaman showcase, dan `resources/views/examples/records/` sebelum menyunting view. Periksa file komponen asli untuk prop, slot, serta batasnya.
2. **Tentukan data dan akses.** Untuk data nyata buat route ke controller, query/model, validasi, dan otorisasi. Sesuaikan middleware/guard/policy dengan role proyek. Login session dan policy kepemilikan CRUD referensi sudah ada; sistem role proyek belum ditentukan. Baca [kontrak role](../standar/kontrak-role.md) dan [referensi CRUD](referensi-crud.md).
3. **Susun view dengan shell Kenanga.** Dashboard admin maupun role lain tetap memakai `<x-layouts.admin>`; bedakan menu, data, dan hak akses tanpa membuat shell desain baru. Pakai `<x-ui.page-header>`, komponen UI yang sesuai, serta pola CSS resmi untuk bagian tanpa wrapper.
4. **Sambungkan navigasi.** Daftarkan named route di `routes/web.php`; sesuaikan `config/kenanga.php` untuk grup/item, `active`, dan ikon yang tersedia. Menyembunyikan menu saja tidak mengamankan route/aksi; periksa izin di server.
5. **Sambungkan aksi nyata.** Form memakai `action`, `method`, `@csrf`, `@method` bila perlu, `name`, dan error Laravel. Lepas `data-demo-form` serta aksi toast palsu. Untuk tabel database besar gunakan filter GET dan paginator server. Kirim array grafik dari controller ke `<x-ui.chart>`.
6. **Verifikasi.** Jalankan pemeriksaan relevan: `php artisan route:list`, `php artisan view:cache`, `php artisan test`, dan `npm run build`. Periksa keadaan kosong, hasil filter kosong, error, serta hak akses role.

Contoh kerangka halaman:

```blade
<x-layouts.admin title="Produk" group="Katalog">
    <x-ui.page-header title="Produk" description="Kelola produk." />

    <x-ui.card title="Daftar produk">
        <div class="overflow-x-auto">
            <table class="table">
                <thead><tr><th>Nama</th></tr></thead>
                <tbody>
                    @forelse ($products as $product)
                        <tr><td>{{ $product->name }}</td></tr>
                    @empty
                        <tr><td><x-ui.table-state state="empty" title="Belum ada produk" /></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-ui.card>
</x-layouts.admin>
```

Untuk implementasi lengkap, ikuti [konvensi kode Laravel](../standar/konvensi-kode.md), [panduan data besar](data-besar.md), `docs/panduan/halaman.md`, `docs/integrasi/crud.md`, `docs/integrasi/form.md`, `docs/integrasi/data.md`, dan `docs/integrasi/auth.md`. Contoh pada dokumentasi integrasi adalah kode yang **perlu ditambahkan**, bukan backend yang sudah tersedia.

## Checklist sebelum menyerahkan perubahan

- Tidak ada komponen UI atau pola visual baru yang menduplikasi koleksi Kenanga.
- View memakai layout dan komponen/pola Kenanga; prop dan slot cocok dengan implementasi aktual.
- Tidak ada library UI baru untuk fungsi yang sudah tersedia; interaksi mengikuti modul JS bawaan.
- Named route, sidebar, ikon, dan breadcrumb sesuai; setiap role mendapat otorisasi server.
- Form/data bukan simulasi demo bila tugas meminta penyimpanan atau autentikasi nyata.
- Pemeriksaan relevan lulus dan keterbatasan komponen dilaporkan dengan jelas.
