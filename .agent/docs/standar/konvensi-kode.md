# Konvensi kode Laravel untuk proyek turunan Kenanga

Aturan ini berlaku ketika starter kit dipakai membangun fitur nyata. Ikuti [aturan UI](../../README.md) untuk seluruh view dashboard. Contoh pada `docs/integrasi/` bersifat edukatif; jika contoh masih memakai validasi langsung di controller atau input HTML mentah, terapkan konvensi ini pada kode baru.

## Gaya dan struktur

- Ikuti `.editorconfig`: UTF-8, LF, indentasi 4 spasi, newline akhir. Format PHP dengan Laravel Pint (`vendor/bin/pint --test` untuk pemeriksaan, `vendor/bin/pint` untuk merapikan).
- Gunakan namespace, nama kelas, dan lokasi standar Laravel: `app/Http/Controllers`, `app/Http/Requests`, `app/Models`, `app/Policies`, `app/Jobs`; tambahkan `app/Actions` atau `app/Services` hanya jika logika domain sudah perlu dipakai ulang/diuji terpisah.
- Nama kelas `PascalCase`, method/variabel `camelCase`, tabel/kolom `snake_case`, nama route hierarkis seperti `admin.products.index`. Beri nama yang menggambarkan domain, bukan `DataController` atau `Helper`.
- Deklarasikan tipe parameter dan return PHP, termasuk `: void`, `: View`, `: RedirectResponse`, dan tipe relasi Eloquent. Gunakan constructor injection untuk dependensi. Hindari service/repository/DTO kosong yang hanya meneruskan satu panggilan Eloquent.
- Controller menangani request dan response; query dan aturan domain yang panjang dipindah ke scope, query object, action, atau service bila ada alasan nyata. Blade menampilkan data dan kondisi presentasi, bukan menjalankan query database.
- Gunakan route model binding dan resource controller untuk CRUD bila cocok. Kelompokkan route berdasarkan `auth` dan kebijakan role; nama route yang dipakai sidebar harus tetap valid.

## PHPDoc dan komentar

Gunakan PHPDoc ketika type hint PHP tidak cukup: bentuk array, generik collection/paginator, factory generic, kontrak callback, exception yang perlu diketahui pemanggil, atau alasan aturan domain yang tidak terlihat dari kode. Tidak perlu PHPDoc satu baris yang hanya mengulang `public function show(Product $product): View`. Komentar biasa menjelaskan **mengapa**, bukan membaca ulang **apa** yang dilakukan kode. Pertahankan gaya yang sudah ada pada `app/Models/User.php` untuk `@var list<string>` dan `@return array<string, string>`.

```php
/** @return array{labels: list<string>, values: list<int>} */
private function monthlyTotals(): array
{
    // ...
}
```

Pada model, beri return type relasi seperti `BelongsTo`/`HasMany`. Tambahkan PHPDoc generik relasi hanya bila alat analisis statis proyek membutuhkannya. Jangan menaruh dokumentasi besar di dalam setiap method; dokumentasikan kontrak di lokasi yang paling dekat dan jelas.

## Input, otorisasi, dan keamanan

- Untuk endpoint tulis dan filter GET yang tidak sepele, buat `Store...Request`, `Update...Request`, atau `Index...Request` di `app/Http/Requests`. Letakkan `authorize(): bool` dan `rules(): array` di sana. Controller memakai `validated()`/`safe()`, bukan `all()`.
- Form Blade memakai `@csrf`, `@method` untuk PUT/PATCH/DELETE, `x-ui.field` dan kontrol `x-ui.*`; gunakan `old()`/error bawaan komponen. Hapus `data-demo-form` saat form mengirim data nyata.
- Lindungi route dengan autentikasi dan policy/gate per aksi. Menyembunyikan menu berdasarkan role hanya mengubah tampilan, bukan otorisasi. Untuk resource bertingkat, gunakan scoped binding dan tetap periksa policy.
- Daftar field yang boleh diisi di model lewat `$fillable` atau pemetaan eksplisit. Jangan mengisi model langsung dari seluruh payload. Gunakan cast untuk boolean, tanggal, enum, dan tipe tersimpan lain. Jangan simpan secret di source code; pakai `config/*` dan env.
- Validasi upload di server (ukuran, tipe/MIME, akses), simpan pada disk yang tepat, dan jangan mengandalkan pratinjau/validasi browser. JS bawaan `x-ui.file-preview` adalah pratinjau lokal; inputnya perlu `name` sebelum dipakai untuk upload sungguhan.

Contoh pola request dan controller:

```php
<?php

namespace App\Http\Requests;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;

final class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Product::class) ?? false;
    }

    /** @return array<string, array<int, string>> */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'integer', 'min:0'],
        ];
    }
}
```

```php
<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;

final class ProductController extends Controller
{
    public function store(StoreProductRequest $request): RedirectResponse
    {
        $product = Product::create($request->validated());

        return redirect()->route('admin.products.show', $product)
            ->with('status', 'Produk berhasil dibuat.');
    }
}
```

`Product`, policy, dan route pada contoh perlu dibuat sesuai proyek. Jika `authorize()` pada Form Request tidak dipakai untuk kebijakan tertentu, beri alasan dan lakukan pemeriksaan otorisasi eksplisit di controller.

## Eloquent dan transaksi

- Hindari query di loop dan N+1: eager load relasi yang dirender dengan `with()`, hitung relasi dengan `withCount()`, dan pilih kolom yang diperlukan sambil tetap mengambil primary/foreign key untuk relasi.
- Buat scope untuk filter domain yang dipakai ulang. Beri indeks pada foreign key dan kolom yang sering difilter/diurutkan; tentukan berdasarkan query nyata, bukan mengindeks semua kolom.
- Gunakan `DB::transaction()` untuk perubahan beberapa tabel yang harus atomik. Perubahan bulk yang tidak memerlukan event per model bisa memakai operasi database set-based. Perhatikan bahwa operasi update/delete massal tidak memicu event per model.
- Gunakan queue untuk laporan, ekspor, email, dan pekerjaan IO berat. Job harus aman bila diproses ulang; jangan memuat seluruh tabel ke memori. Cache hanya pembacaan mahal yang jelas manfaatnya, serta tentukan kapan cache invalid.

## Kualitas perubahan

Tambahkan feature test untuk perilaku penting: akses role/403, validasi, penyimpanan, filter, pagination, dan keadaan kosong. Uji query yang berisiko N+1 atau akses lintas pemilik saat relevan. Jalankan `vendor/bin/pint --test`, `php artisan test`, `php artisan view:cache`, dan `npm run build` sesuai area yang diubah. Jangan menulis test yang sekadar menyalin implementasi.
