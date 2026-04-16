<?php

use App\Http\Controllers\LoginAdminController;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::inertia('/', 'Welcome', [
    'canRegister' => Features::enabled(Features::registration()),
])->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');
});

Route::middleware('guest')->group(function () {
    Route::post('admin/login', [LoginAdminController::class, 'store'])->name('admin.login.store');
});

Route::middleware(['auth', 'isAdmin'])->group(function () {
    Route::post('admin/logout', [LoginAdminController::class, 'destroy'])->name('admin.logout');
});

require __DIR__.'/settings.php';
