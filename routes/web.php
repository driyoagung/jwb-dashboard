<?php

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

    Route::prefix('examples/records')->name('examples.records.')->group(function (): void {
        Route::view('/', 'examples.records.index')->name('index');
        Route::view('/new', 'examples.records.create')->name('create');
        Route::view('/detail', 'examples.records.show')->name('show');
        Route::view('/edit', 'examples.records.edit')->name('edit');
    });
}

Route::view('/login', 'auth.login')->name('login');
Route::view('/demo/404', 'errors.404')->name('demo.404');
Route::view('/testing', 'testing')->name('testing');
