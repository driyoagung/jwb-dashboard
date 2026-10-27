<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Pos\CategoryController;
use App\Http\Controllers\Pos\CheckoutController;
use App\Http\Controllers\Pos\CustomerController;
use App\Http\Controllers\Pos\DashboardController;
use App\Http\Controllers\Pos\DraftController;
use App\Http\Controllers\Pos\GlobalSearchController;
use App\Http\Controllers\Pos\NotificationController;
use App\Http\Controllers\Pos\ProductController;
use App\Http\Controllers\Pos\ReceivableController;
use App\Http\Controllers\Pos\ReportController;
use App\Http\Controllers\Pos\SettingController;
use App\Http\Controllers\Pos\StockController;
use App\Http\Controllers\Pos\TransactionController;
use App\Http\Controllers\Pos\UnitController;
use App\Http\Controllers\Pos\UserController;
use App\Http\Controllers\ReferenceRecordController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

Route::middleware(['auth', 'can:pos.sell'])->group(function (): void {
    Route::get('/search', GlobalSearchController::class)->name('pos.search');
    Route::get('/notifications', [NotificationController::class, 'index'])->name('pos.notifications.index');
    Route::get('/notifications/feed', [NotificationController::class, 'feed'])->name('pos.notifications.feed');
    Route::post('/notifications/read-all', [NotificationController::class, 'readAll'])->name('pos.notifications.read-all');
    Route::delete('/notifications', [NotificationController::class, 'destroyAll'])->name('pos.notifications.destroy-all');
    Route::post('/notifications/{notification}/mark', [NotificationController::class, 'mark'])->name('pos.notifications.mark');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'read'])->name('pos.notifications.read');
    Route::delete('/notifications/{notification}', [NotificationController::class, 'destroy'])->name('pos.notifications.destroy');
    Route::get('/dashboard', DashboardController::class)->name('admin.dashboard');
    Route::get('/cashier', [CheckoutController::class, 'create'])->name('pos.cashier');
    Route::resource('/customers', CustomerController::class)->except(['show', 'destroy'])->names('pos.customers');
    Route::get('/receivables', [ReceivableController::class, 'index'])->name('pos.receivables.index');
    Route::get('/receivables/{receivable}', [ReceivableController::class, 'show'])->name('pos.receivables.show');
    Route::post('/receivables/{receivable}/payments', [ReceivableController::class, 'storePayment'])->name('pos.receivables.payments.store');
    Route::get('/cashier/barcode', [CheckoutController::class, 'lookupBarcode'])->name('pos.cashier.barcode');
    Route::post('/cashier/checkout', [CheckoutController::class, 'store'])->name('pos.checkout');
    Route::get('/transactions', [TransactionController::class, 'index'])->name('pos.transactions.index');
    Route::get('/transactions/{transaction}', [TransactionController::class, 'show'])->name('pos.transactions.show');
    Route::get('/transactions/{transaction}/receipt', [TransactionController::class, 'receipt'])->name('pos.transactions.receipt');
    Route::post('/transactions/{transaction}/check-payment', [TransactionController::class, 'checkPayment'])->name('pos.transactions.check-payment');
    Route::post('/transactions/{transaction}/retry-payment', [TransactionController::class, 'retryPayment'])->name('pos.transactions.retry-payment');
    Route::get('/drafts', [DraftController::class, 'index'])->name('pos.drafts.index');
    Route::post('/drafts', [DraftController::class, 'store'])->name('pos.drafts.store');
    Route::get('/drafts/{draft}', [DraftController::class, 'show'])->name('pos.drafts.show');
    Route::put('/drafts/{draft}', [DraftController::class, 'update'])->name('pos.drafts.update');
    Route::delete('/drafts/{draft}', [DraftController::class, 'destroy'])->name('pos.drafts.destroy');
});

Route::middleware(['auth', 'can:pos.admin'])->prefix('manage')->name('pos.manage.')->group(function (): void {
    Route::resource('categories', CategoryController::class)->except('show');
    Route::resource('units', UnitController::class)->except('show');
    Route::resource('products', ProductController::class)->except('show');
    Route::get('stock', [StockController::class, 'index'])->name('stock.index');
    Route::post('stock/{product}/adjust', [StockController::class, 'adjust'])->name('stock.adjust');
    Route::get('stock/movements', [StockController::class, 'movements'])->name('stock.movements');
    Route::get('reports/{report}', [ReportController::class, 'show'])->name('reports.show');
    Route::get('reports/{report}/export', [ReportController::class, 'export'])->name('reports.export');
    Route::resource('users', UserController::class)->except(['show', 'destroy']);
    Route::get('settings', [SettingController::class, 'edit'])->name('settings.edit');
    Route::put('settings', [SettingController::class, 'update'])->name('settings.update');
});

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
