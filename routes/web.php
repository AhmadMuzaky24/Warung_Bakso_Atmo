<?php

use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\MenuController;

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
    Route::get('/', function () {
        return view('dashboard');
    })->name('dashboard');
});