<?php

use App\Http\Controllers\LegacyController;
use App\Http\Controllers\KantinAuthController;
use App\Http\Controllers\KantinHomeController;
use App\Http\Controllers\KantinMenuController;
use App\Http\Controllers\KantinOrderController;
use App\Http\Controllers\KantinAdminController;
use App\Http\Controllers\KantinReportController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect('/app/login'));
Route::redirect('/home', '/app/home');
Route::redirect('/login', '/app/login');
Route::redirect('/logout', '/app/login');
Route::redirect('/menu', '/app/menu');
Route::redirect('/order', '/app/orders');
Route::redirect('/user', '/app/users');
Route::redirect('/kios', '/app/kios');
Route::redirect('/laporan', '/app/reports/orders');
Route::redirect('/laporanrs', '/app/reports/rs');
Route::redirect('/laporantoko', '/app/reports/toko');
Route::redirect('/history', '/app/reports/menu');
Route::redirect('/rekaprs', '/app/reports/rekap-rs');
Route::redirect('/rekapmenurs', '/app/reports/rekap-menu-rs');
Route::redirect('/rekapkeuangan', '/app/reports/finance-detail');
Route::redirect('/rekapkeuanganmenu', '/app/reports/finance-menu');
Route::get('/orderitem', function (\Illuminate\Http\Request $request) {
    $id = $request->query('kode_order');
    if ($id) {
        return redirect('/app/orders/' . $id);
    }

    return redirect('/app/orders');
});

Route::get('/app/login', [KantinAuthController::class, 'showLogin'])->name('app.login');
Route::post('/app/login', [KantinAuthController::class, 'login'])->name('app.login.submit');
Route::post('/app/logout', [KantinAuthController::class, 'logout'])->name('app.logout');
Route::middleware('kantin.auth')->group(function (): void {
    Route::get('/app/home', [KantinHomeController::class, 'index'])->name('app.home');
    Route::get('/app/menu', [KantinMenuController::class, 'index'])->name('app.menu.index');
    Route::post('/app/menu', [KantinMenuController::class, 'store'])->name('app.menu.store');
    Route::post('/app/menu/{id}', [KantinMenuController::class, 'update'])->name('app.menu.update');
    Route::delete('/app/menu/{id}', [KantinMenuController::class, 'destroy'])->name('app.menu.destroy');

    Route::get('/app/orders', [KantinOrderController::class, 'index'])->name('app.orders.index');
    Route::post('/app/orders', [KantinOrderController::class, 'store'])->name('app.orders.store');
    Route::post('/app/orders/{id}', [KantinOrderController::class, 'update'])->name('app.orders.update');
    Route::delete('/app/orders/{id}', [KantinOrderController::class, 'destroy'])->name('app.orders.destroy');
    Route::get('/app/orders/{id}', [KantinOrderController::class, 'show'])->name('app.orders.show');
    Route::get('/app/orders/{id}/receipt', [KantinOrderController::class, 'receipt'])->name('app.orders.receipt');
    Route::post('/app/orders/{id}/items', [KantinOrderController::class, 'addItem'])->name('app.orders.items.store');
    Route::post('/app/orders/{id}/items/{itemId}', [KantinOrderController::class, 'updateItem'])->name('app.orders.items.update');
    Route::delete('/app/orders/{id}/items/{itemId}', [KantinOrderController::class, 'deleteItem'])->name('app.orders.items.destroy');
    Route::post('/app/orders/{id}/pay', [KantinOrderController::class, 'pay'])->name('app.orders.pay');

    Route::get('/app/reports/orders', [KantinReportController::class, 'orders'])->name('app.reports.orders');
    Route::get('/app/reports/rs', [KantinReportController::class, 'rs'])->name('app.reports.rs');
    Route::get('/app/reports/toko', [KantinReportController::class, 'toko'])->name('app.reports.toko');
    Route::get('/app/reports/menu', [KantinReportController::class, 'menuSales'])->name('app.reports.menu');
    Route::get('/app/reports/rekap-rs', [KantinReportController::class, 'rekapRs'])->name('app.reports.rekap_rs');
    Route::get('/app/reports/rekap-menu-rs', [KantinReportController::class, 'rekapMenuRs'])->name('app.reports.rekap_menu_rs');
    Route::get('/app/reports/finance-detail', [KantinReportController::class, 'financeDetail'])->name('app.reports.finance_detail');
    Route::get('/app/reports/finance-menu', [KantinReportController::class, 'financeMenu'])->name('app.reports.finance_menu');
    Route::get('/app/reports/rekap-rs/export', [KantinReportController::class, 'exportRekapRsCsv'])->name('app.reports.rekap_rs.export');
    Route::get('/app/reports/rekap-menu-rs/export', [KantinReportController::class, 'exportRekapMenuRsCsv'])->name('app.reports.rekap_menu_rs.export');
    Route::get('/app/reports/finance-detail/export', [KantinReportController::class, 'exportFinanceDetailCsv'])->name('app.reports.finance_detail.export');
    Route::get('/app/reports/finance-menu/export', [KantinReportController::class, 'exportFinanceMenuCsv'])->name('app.reports.finance_menu.export');
    Route::get('/app/reports/orders/export', [KantinReportController::class, 'exportOrdersCsv'])->name('app.reports.orders.export');
    Route::get('/app/reports/rs/export', [KantinReportController::class, 'exportRsCsv'])->name('app.reports.rs.export');
    Route::get('/app/reports/toko/export', [KantinReportController::class, 'exportTokoCsv'])->name('app.reports.toko.export');
    Route::get('/app/reports/menu/export', [KantinReportController::class, 'exportMenuCsv'])->name('app.reports.menu.export');

    Route::get('/app/users', [KantinAdminController::class, 'users'])->name('app.users.index');
    Route::post('/app/users', [KantinAdminController::class, 'storeUser'])->name('app.users.store');
    Route::post('/app/users/{id}', [KantinAdminController::class, 'updateUser'])->name('app.users.update');
    Route::delete('/app/users/{id}', [KantinAdminController::class, 'deleteUser'])->name('app.users.destroy');

    Route::get('/app/kios', [KantinAdminController::class, 'kios'])->name('app.kios.index');
    Route::post('/app/kios', [KantinAdminController::class, 'storeKios'])->name('app.kios.store');
    Route::post('/app/kios/{id}', [KantinAdminController::class, 'updateKios'])->name('app.kios.update');
    Route::delete('/app/kios/{id}', [KantinAdminController::class, 'deleteKios'])->name('app.kios.destroy');
});

Route::match(['GET', 'POST'], '/legacy/{path?}', [LegacyController::class, 'legacyPath'])->where('path', '.*');
Route::get('/{x}', [LegacyController::class, 'page'])->where('x', '(home|menu|order|orderitem|user|kios|laporan|history|laporanrs|laporantoko|rekapmenurs|rekaprs|rekapkeuangan|rekapkeuanganmenu|login|logout)');
