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
        Route::view('/forms', 'showcase.forms')->name('forms');
        Route::view('/buttons', 'showcase.buttons')->name('buttons');
        Route::view('/feedback', 'showcase.feedback')->name('feedback');
        Route::view('/navigation', 'showcase.navigation')->name('navigation');
    });
}

Route::view('/login', 'auth.login')->name('login');
Route::view('/demo/404', 'errors.404')->name('demo.404');
