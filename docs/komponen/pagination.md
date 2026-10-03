# 📄 Pagination

`<x-ui.pagination>` adalah rancangan tampilan paginator pada `resources/views/components/ui/pagination.blade.php`. **Saat ini belum siap dipakai langsung**: file menerima `paginator`, tetapi loop nomor halaman merujuk `$elements` tanpa mengisinya. Komponen juga mengharapkan `total()` sehingga lebih dekat ke `paginate()` daripada `simplePaginate()`/`cursorPaginate()`.

## 🧩 API yang dirancang

| Prop | Fungsi |
| --- | --- |
| `paginator` | Objek `LengthAwarePaginator` untuk `hasPages`, item pertama/terakhir, total, URL halaman |

Struktur UI saat pagination muncul: teks “Menampilkan X–Y dari Z”, tombol sebelumnya/berikutnya, angka halaman, elipsis, dan penanda halaman aktif. Ketika `$paginator->hasPages()` false, markup tidak muncul.

## ✅ Pilihan siap pakai: Laravel paginator

```php
$records = Record::query()->latest()->paginate(15)->withQueryString();
return view('examples.records.index', compact('records'));
```

```blade
@foreach ($records as $record)
    <p>{{ $record->title }}</p>
@endforeach

{{ $records->links() }}
```

Ini menggunakan view pagination Laravel, **bukan** `x-ui.pagination`. `withQueryString()` mempertahankan pencarian/filter URL. Model `Record` pada contoh perlu dibuat melalui [panduan CRUD](/integrasi/crud).

## 🛠️ Jika ingin memakai komponen kustom

Setelah Anda menambahkan pembentukan daftar elemen halaman yang benar di komponen tersebut, panggil:

```blade
<x-ui.pagination :paginator="$records" />
```

Contoh di atas **baru berlaku setelah perbaikan** `$elements`; tanpa perbaikan, angka halaman tidak punya data. Alternatifnya terapkan desain ke pagination view Laravel resmi lalu panggil `$records->links('nama-view')`. Jangan aktifkan `data-table` client-side bersamaan dengan paginasi server untuk dataset yang sama; pencarian JS hanya melihat baris yang sedang dirender.
