# Pagination

<ComponentPreview name="pagination" :height="130" />

`<x-ui.pagination>` menggunakan `Illuminate\Pagination\UrlWindow` untuk menampilkan nomor halaman, elipsis, tombol sebelumnya/berikutnya, dan jumlah data dengan gaya Kenanga. Sumber: `resources/views/components/ui/pagination.blade.php`.

## Pemakaian

Komponen membutuhkan `LengthAwarePaginator` dari `paginate()`, bukan hasil `simplePaginate()` atau `cursorPaginate()`, karena tampilan ini memakai `total()`, nomor halaman, dan rentang item.

```php
$records = Record::query()
    ->latest()
    ->paginate(15)
    ->withQueryString();

return view('examples.records.index', compact('records'));
```

```blade
{{-- Render tabel .table dari $records, tanpa data-table client-side. --}}
<x-ui.pagination :paginator="$records" />
```

Ketika hanya ada satu halaman, komponen tidak menampilkan navigasi. `withQueryString()` mempertahankan pencarian/filter pada tautan halaman. Contoh yang berjalan ada di `resources/views/reference/records/index.blade.php`; model `Record` di potongan kode adalah ilustrasi.

Jangan mencampur `data-table` client-side dengan pagination server pada dataset yang sama: JS hanya melihat baris yang sedang dirender. Untuk `cursorPaginate()`, rancang navigasi berbeda karena paginator cursor tidak menyediakan total dan nomor halaman.