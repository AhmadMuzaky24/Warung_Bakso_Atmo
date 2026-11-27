<?php

use App\Http\Controllers\Admin\CateringOrderController as AdminCateringOrderController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Staff\CateringOrderController as StaffCateringOrderController;
use App\Http\Controllers\Staff\DashboardController as StaffDashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Public\AboutController;
use App\Http\Controllers\Public\CatalogController;

Route::get('/', [HomeController::class, 'index'])->name('public.home');


Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])
        ->name('login');

    Route::post('/login', [AuthController::class, 'login'])
        ->name('login.store');

    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');

    Route::post('/register', [AuthController::class, 'register'])
        ->middleware('throttle:10,1')
        ->name('register.store');
});

Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', AdminDashboardController::class)
            ->name('dashboard');
        Route::resource('users', UserController::class)
            ->except(['show']);
        Route::resource('categories', CategoryController::class)
            ->except(['show']);
        Route::resource('menus', MenuController::class)
            ->except(['show']);
        // Sprint 5 — Admin: daftar/detail pesanan + verifikasi pembayaran (payment_status saja).
        Route::get('/catering', [AdminCateringOrderController::class, 'index'])
            ->name('catering.index');
        Route::get('/catering/{cateringOrder}', [AdminCateringOrderController::class, 'show'])
            ->name('catering.show');
        Route::patch('/catering/{cateringOrder}/payment-status', [AdminCateringOrderController::class, 'updatePaymentStatus'])
            ->name('catering.update-payment-status');
        // Sprint 6 — Admin: laporan rentang tanggal & ekspor XLSX.
        Route::get('/reports', [ReportController::class, 'index'])
            ->name('reports.index');
        Route::get('/reports/export', [ReportController::class, 'export'])
            ->name('reports.export');
    });

Route::middleware(['auth', 'role:admin,staff,customer'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('logout');
    Route::get('/change-password', [AuthController::class, 'showChangePasswordForm'])
        ->name('password.edit');
    Route::put('/change-password', [AuthController::class, 'updatePassword'])
        ->name('password.update');

    // Dipindah dari '/' ke '/dashboard' (nama route tetap 'dashboard')
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

Route::get('/', [HomeController::class, 'index'])->name('public.home');
Route::get('/perjalanan-kami', [AboutController::class, 'index'])->name('public.about');

// Dilebur ke beranda; URL lama tetap jalan lewat redirect
Route::get('/menu/{menu}', [CatalogController::class, 'show'])->name('public.menus.show');
Route::redirect('/lokasi', '/#lokasi')->name('public.locations');

// Sprint 4 — Customer Catering (Contributor 3). Append murni, grup lama tidak diubah.
Route::middleware(['auth', 'role:customer'])->prefix('customer')->name('customer.')->group(function () {
    Route::get('/dashboard', \App\Http\Controllers\Customer\DashboardController::class)->name('dashboard');
    Route::get('/catering', [\App\Http\Controllers\Customer\CateringOrderController::class, 'index'])->name('catering.index');
    Route::get('/catering/create', [\App\Http\Controllers\Customer\CateringOrderController::class, 'create'])->name('catering.create');
    Route::post('/catering', [\App\Http\Controllers\Customer\CateringOrderController::class, 'store'])->name('catering.store');
    Route::get('/catering/{cateringOrder}', [\App\Http\Controllers\Customer\CateringOrderController::class, 'show'])->name('catering.show');
});

// Sprint 5 — Staff Operations (Contributor 4). Staff hanya mengubah `status`;
// tidak ada route untuk `payment_status` di area ini.
Route::middleware(['auth', 'role:staff'])->prefix('staff')->name('staff.')->group(function () {
    Route::get('/dashboard', StaffDashboardController::class)->name('dashboard');
    Route::get('/catering', [StaffCateringOrderController::class, 'index'])->name('catering.index');
    Route::get('/catering/{cateringOrder}', [StaffCateringOrderController::class, 'show'])->name('catering.show');
    Route::patch('/catering/{cateringOrder}/status', [StaffCateringOrderController::class, 'updateStatus'])
        ->name('catering.update-status');
});
