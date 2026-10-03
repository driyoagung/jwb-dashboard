# 📰 Page header

`<x-ui.page-header>` merender pembungkus berisi `<h1>` dan deskripsi opsional. Sumber: `resources/views/components/ui/page-header.blade.php`.

## 🧩 API

| Prop | Kewajiban | Fungsi |
| --- | --- | --- |
| `title` | wajib | Judul `<h1>` |
| `description` | opsional | Paragraf penjelas |
| Atribut HTML | opsional | Diteruskan ke `<div>` pembungkus |

Komponen ini tidak memiliki slot aksi dan tidak menyediakan varian visual. Untuk toolbar/aksi, letakkan komponen dalam flex layout bersama tautan/tombol.

## 📝 Penggunaan sederhana

```blade
<x-layouts.admin title="Produk" group="Katalog">
    <x-ui.page-header title="Produk" description="Kelola harga dan stok." />
</x-layouts.admin>
```

## 🖱️ Dengan aksi dan kelas tambahan

```blade
<div class="flex flex-wrap items-start justify-between gap-4">
    <x-ui.page-header title="Daftar entri" description="Cari dan kelola catatan tim." class="min-w-0" />
    <a href="{{ route('examples.records.create') }}" class="btn btn-primary">Tambah entri</a>
</div>
```

## 🗃️ Judul dari data server

```blade
<x-layouts.admin :title="$record->title" group="Entri">
    <x-ui.page-header :title="$record->title" :description="'Kategori: '.$record->category" />
</x-layouts.admin>
```

`title` layout dipakai untuk tab browser dan breadcrumb, sedangkan `title` page header untuk isi halaman. Teks dari model di-escape Blade. Pastikan hanya satu `<h1>` untuk isi setiap halaman.
