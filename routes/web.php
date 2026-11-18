<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Public\HomeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Public\AboutController;
use App\Http\Controllers\Public\CatalogController;

Route::get('/', [HomeController::class, 'index'])->name('public.home');


Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])
        ->name('login');

    Route::post('/login', [AuthController::class, 'login'])
        ->name('login.store');
});

Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::resource('users', UserController::class)
            ->except(['show']);
        Route::resource('categories', CategoryController::class)
            ->except(['show']);
        Route::resource('menus', MenuController::class)
            ->except(['show']);
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