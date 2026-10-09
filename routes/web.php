<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ReferenceRecordController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

Route::view('/dashboard', 'admin.dashboard')->name('admin.dashboard');
Route::view('/analytics', 'admin.analytics')->name('admin.analytics');
Route::view('/settings', 'admin.settings')->name('admin.settings');

if (config('kenanga.showcase')) {
    Route::prefix('components')->name('showcase.')->group(function (): void {
        Route::view('/cards', 'showcase.cards')->name('cards');
        Route::view('/tables', 'showcase.tables')->name('tables');
        Route::view('/table-states', 'showcase.table-states')->name('table-states');
        Route::view('/forms', 'showcase.forms')->name('forms');
        Route::view('/filters', 'showcase.filters')->name('filters');
        Route::view('/charts', 'showcase.charts')->name('charts');
        Route::view('/buttons', 'showcase.buttons')->name('buttons');
        Route::view('/feedback', 'showcase.feedback')->name('feedback');
        Route::view('/navigation', 'showcase.navigation')->name('navigation');
        Route::view('/data-patterns', 'showcase.data-patterns')->name('data-patterns');
        Route::post('/data-patterns/preview', fn () => redirect()->route('showcase.data-patterns')->with('status', 'Pratinjau konfirmasi selesai; tidak ada data yang dihapus.'))->name('data-patterns.preview');
    });

    Route::middleware('auth')->prefix('reference/records')->name('reference.records.')->group(function (): void {
        Route::get('/', [ReferenceRecordController::class, 'index'])->name('index');
        Route::get('/new', [ReferenceRecordController::class, 'create'])->name('create');
        Route::post('/', [ReferenceRecordController::class, 'store'])->name('store');
        Route::get('/{record}', [ReferenceRecordController::class, 'show'])->name('show');
        Route::get('/{record}/edit', [ReferenceRecordController::class, 'edit'])->name('edit');
        Route::put('/{record}', [ReferenceRecordController::class, 'update'])->name('update');
        Route::delete('/{record}', [ReferenceRecordController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('examples/records')->name('examples.records.')->group(function (): void {
        Route::view('/', 'examples.records.index')->name('index');
        Route::view('/new', 'examples.records.create')->name('create');
        Route::view('/detail', 'examples.records.show')->name('show');
        Route::view('/edit', 'examples.records.edit')->name('edit');
    });
}

Route::get('/login', [AuthController::class, 'create'])->name('login');
Route::post('/login', [AuthController::class, 'store'])->middleware('throttle:5,1')->name('login.store');
Route::post('/logout', [AuthController::class, 'destroy'])->middleware('auth')->name('logout');
Route::view('/demo/404', 'errors.404')->name('demo.404');
Route::view('/testing', 'testing')->name('testing');
