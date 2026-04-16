<?php

use App\Http\Controllers\LoginAdminController;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use Inertia\Inertia;

Route::inertia('/', 'Welcome', [
    'canRegister' => Features::enabled(Features::registration()),
])->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');
});

Route::get('/nova-cita', function () {
    return Inertia::render('newDate');
})->name('nova-cita');

Route::middleware('guest')->group(function () {
    Route::post('admin/login', [LoginAdminController::class, 'store'])->name('admin.login.store');
});

Route::get('/loginWorker', [LoginAdminController::class, 'show'])->name('loginWorker');

Route::middleware(['auth', 'isAdmin'])->group(function () {
    Route::post('admin/logout', [LoginAdminController::class, 'destroy'])->name('admin.logout');
});

require __DIR__.'/settings.php';
