# 🗃️ Data & halaman CRUD

Contoh ini mengubah tampilan demo `examples/records` menjadi entri tersimpan. Sebagai ilustrasi, kita gunakan model `Record` dengan kolom `title`, `category`, `status`, dan `summary`. Sesuaikan skema, kebijakan akses, dan nama route dengan aplikasi Anda. Route demo lama di `routes/web.php` berada di dalam blok `if (config('kenanga.showcase'))`; hapus/ubah blok route `examples.records.*` lama sebelum mendaftarkan route yang sama.

## 🧱 Siapkan model dan tabel

```bash
php artisan make:model Record -m
php artisan make:controller RecordController
```

Di migrasi baru (`database/migrations/...create_records_table.php`), isi definisi tabel:

```php
Schema::create('records', function (Blueprint $table) {
    $table->id();
    $table->string('title');
    $table->string('category');
    $table->string('status')->default('draf');
    $table->text('summary')->nullable();
    $table->timestamps();
});
```

Lalu jalankan `php artisan migrate`. Bila memakai `Record::create()` dan `$record->update()`, tentukan field yang dapat diisi di `app/Models/Record.php`:

```php
protected $fillable = ['title', 'category', 'status', 'summary'];
```

## 🛣️ Daftarkan route

Gunakan nama route yang telah dipakai navigasi contoh agar tautan sidebar berfungsi. Route statis `/new` didefinisikan sebelum parameter `{record}` agar tidak tertangkap sebagai ID; model binding menghasilkan 404 untuk entri yang tidak ditemukan.

```php
use App\Http\Controllers\RecordController;

Route::prefix('examples/records')->name('examples.records.')->group(function () {
    Route::get('/', [RecordController::class, 'index'])->name('index');
    Route::get('/new', [RecordController::class, 'create'])->name('create');
    Route::post('/', [RecordController::class, 'store'])->name('store');
    Route::get('/{record}', [RecordController::class, 'show'])->name('show');
    Route::get('/{record}/edit', [RecordController::class, 'edit'])->name('edit');
    Route::put('/{record}', [RecordController::class, 'update'])->name('update');
    Route::delete('/{record}', [RecordController::class, 'destroy'])->name('destroy');
});
```

Jika alur entri ini harus tetap tersedia saat showcase dimatikan, letakkan route baru **di luar** blok `if (config('kenanga.showcase'))`, lalu sesuaikan konfigurasi navigasi bila menu tersebut juga perlu tampil.

## 📤 Kirim data dari controller

```php
namespace App\Http\Controllers;

use App\Models\Record;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RecordController extends Controller
{
    public function index(Request $request): View
    {
        $records = Record::query()
            ->when($request->filled('q'), fn ($query) => $query->where('title', 'like', '%'.$request->input('q').'%'))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('examples.records.index', compact('records'));
    }

    public function create(): View
    {
        return view('examples.records.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'in:panduan,laporan,catatan,arsip'],
            'status' => ['required', 'string', 'in:draf,ditinjau,aktif,arsip'],
            'summary' => ['nullable', 'string'],
        ]);

        $record = Record::create($data);

        return redirect()->route('examples.records.show', $record)
            ->with('status', 'Entri berhasil dibuat.');
    }

    public function show(Record $record): View
    {
        return view('examples.records.show', compact('record'));
    }

    public function edit(Record $record): View
    {
        return view('examples.records.edit', compact('record'));
    }

    public function update(Request $request, Record $record): RedirectResponse
    {
        $record->update($request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'in:panduan,laporan,catatan,arsip'],
            'status' => ['required', 'string', 'in:draf,ditinjau,aktif,arsip'],
            'summary' => ['nullable', 'string'],
        ]));

        return redirect()->route('examples.records.show', $record)
            ->with('status', 'Entri berhasil diperbarui.');
    }

    public function destroy(Record $record): RedirectResponse
    {
        $record->delete();

        return redirect()->route('examples.records.index')
            ->with('status', 'Entri berhasil dihapus.');
    }
}
```

## 🧩 Sesuaikan Blade demo

View bawaan memakai `config('kenanga.demo.records')` dan link detail statis `/detail`. Ganti loop dan tautan dengan `$records` serta ID model:

```blade
@forelse ($records as $record)
    <tr>
        <td>
            <a href="{{ route('examples.records.show', $record) }}">
                {{ $record->title }}
            </a>
        </td>
        <td>{{ $record->category }}</td>
        <td>{{ $record->status }}</td>
    </tr>
@empty
    <tr><td colspan="3"><x-ui.table-state state="empty" /></td></tr>
@endforelse
```

Setelah tabel, tampilkan hasil `$records->links()` dan lepaskan `data-table` untuk paginasi server. Di view detail/edit, ganti konten statis dan link `/detail`/`/edit` dengan `$record` dan `route('examples.records.edit', $record)`. Contoh form tersambung ada di [form & validasi](/integrasi/form).
