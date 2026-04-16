<?php

use App\Http\Controllers\LoginAdminController;
use App\Http\Controllers\DashboardPatientController;
use App\Http\Controllers\DatesController;
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

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboardPatient', [DashboardPatientController::class, 'index'])->name('patientDashboard');
});

Route::get('/nova-cita', function () {
    return Inertia::render('newDate');
})->name('nova-cita');
// Route::get('/nova-cita', function () {
//     return Inertia::render('newDate');
// })->name('nova-cita');

Route::get('/nova-cita', [DatesController::class, 'index'])->name('nova-cita');
Route::post('/nova-cita', [DatesController::class, 'store'])->name('nova-cita-store');

Route::middleware('guest')->group(function () {
    Route::post('admin/login', [LoginAdminController::class, 'store'])->name('admin.login.store');
});

Route::middleware(['auth', 'isAdmin'])->group(function () {
    Route::post('admin/logout', [LoginAdminController::class, 'destroy'])->name('admin.logout');
});

require __DIR__.'/settings.php';
