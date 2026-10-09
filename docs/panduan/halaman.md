# 🧭 Halaman & navigasi

## 📝 Buat halaman admin

Misalnya menambahkan daftar kategori. Buat `resources/views/admin/categories.blade.php`:

```blade
<x-layouts.admin title="Kategori" group="Katalog">
    <x-ui.page-header title="Kategori" description="Kelola kategori produk." />
    <x-ui.card title="Daftar kategori">
        @forelse ($categories as $category)
            <p>{{ $category->name }}</p>
        @empty
            <x-ui.table-state state="empty" title="Belum ada kategori" />
        @endforelse
    </x-ui.card>
</x-layouts.admin>
```

Jika data berasal dari database, buat controller dan daftarkan route di `routes/web.php` (contoh ini membutuhkan model `Category` yang Anda buat sendiri):

```php
use App\Http\Controllers\CategoryController;

Route::get('/categories', [CategoryController::class, 'index'])
    ->name('admin.categories.index');
```

```php
namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        return view('admin.categories', [
            'categories' => Category::query()->orderBy('name')->get(),
        ]);
    }
}
```

Jika halaman sepenuhnya statis, Anda bisa mengikuti route saat ini: `Route::view('/categories', 'admin.categories')->name('admin.categories.index')`; jangan gunakan `Route::view` untuk contoh yang memerlukan `$categories` dari database.

## 🗺️ Tambahkan menu sidebar

Di `config/kenanga.php`, `navigation` adalah kumpulan grup dan item. `x-admin.sidebar` membaca konfigurasi ini, sedangkan `x-admin.nav-link` membangun tautan memakai `route(...)` dan penanda aktif memakai `request()->routeIs(...)`.

```php
['label' => 'Katalog', 'items' => [
    [
        'label' => 'Kategori',
        'route' => 'admin.categories.index',
        'active' => 'admin.categories.*',
        'icon' => 'grid',
    ],
]],
```

Pastikan nama route benar-benar ada; menu yang menunjuk route yang belum terdaftar akan gagal dirender. Untuk submenu, lihat item `Otentikasi` di konfigurasi: item induk memiliki `id`, `icon`, dan `children` berisi item route. Ikon harus memiliki simbol yang tersedia dalam `public/icons.svg`.

### 🧭 Shell admin dan penanda aktif

`x-admin.sidebar` dan `x-admin.header` dirender otomatis oleh `x-layouts.admin`. Sidebar membentuk kelompok dari `config('kenanga.navigation')`. Setiap item membutuhkan `label` dan `route`; `active` opsional dapat berupa pola nama route seperti `admin.categories.*`. `icon` dan `badge` juga opsional. Item bertingkat memakai `children` dan dapat dibuka/tutup lewat `data-collapse`. Breadcrumb di header berasal dari prop `title`/`group` layout, sehingga mengubah nama menu tidak otomatis mengubah breadcrumb.

Branding workspace dari config tampil di bagian atas sidebar, sedangkan nama/email profil di bawah sidebar dan notifikasi header masih hardcoded sebagai data demo. Untuk aplikasi dengan autentikasi, ganti profil dengan `auth()->user()` dan tentukan sumber notifikasi yang sesuai. Komponen customizer sudah termasuk di layout, jadi tidak perlu dipanggil ulang di setiap view.

## 🧪 Atur halaman demo

Route katalog `/components/*`, `/examples/records/*`, dan CRUD `/reference/records/*` didaftarkan hanya jika `config('kenanga.showcase')` bernilai true. Untuk menyembunyikan showcase dan menu demo, atur `ADMIN_SHOWCASE=false` dalam `.env`. Aplikasi dashboard, analitik, pengaturan, login, dan demo 404 tetap ada sesuai `routes/web.php`.

::: warning Perhatikan tautan ke showcase
Sebelum menonaktifkan showcase, periksa tautan kustom menuju route `showcase.*`. Dashboard bawaan sudah memeriksa `config('kenanga.showcase')` untuk tautan “Lihat semua”.
:::
