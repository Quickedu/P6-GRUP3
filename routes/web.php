<?php

use App\Http\Controllers\LoginAdminController;
use App\Http\Controllers\LoginPatientController;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

Route::inertia('/', 'Welcome', [
    'canRegister' => Features::enabled(Features::registration()),
])->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function (){
        return Inertia::render('Dashboard', [
            'role' => Auth::user()->role
        ]);
    })->name('dashboard');
});

Route::get('/nova-cita', function () {
    return Inertia::render('newDate');
})->name('nova-cita');

Route::middleware('guest')->group(function () {
    Route::post('work/login', [LoginAdminController::class, 'store'])->name('loginworkerStore');
});

Route::middleware(['auth', 'isAdmin'])->group(function () {
    Route::post('work/logout', [LoginAdminController::class, 'destroy'])->name('loginworkerDestroy');
});

Route::middleware('guest:patient')->group(function () {
    Route::post('patient/login', [LoginPatientController::class, 'store'])->name('loginpatientStore');
});

Route::middleware('auth:patient')->group(function () {
    Route::post('patient/logout', [LoginPatientController::class, 'destroy'])->name('loginpatientDestroy');
});

require __DIR__.'/settings.php';
