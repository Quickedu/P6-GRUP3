<?php

use App\Http\Controllers\LoginAdminController;
use App\Http\Controllers\LoginPatientController;
use App\Http\Controllers\Patients\DashboardPatientController;
use App\Http\Controllers\Workers\Secretary\DatesController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::inertia('/', 'Welcome', [
    'canRegister' => Features::enabled(Features::registration()),
])->name('home');

Route::get('/dashboard', function (DatesController $datesController) {
    $user = Auth::guard('admin')->user() ?? Auth::guard('patient')->user();
    $role = $user?->role ?? 'patient';

    $dates = $role === 'secretary' ? $datesController->seeDates() : [];

    return Inertia::render('Workers/Dashboard', [
        'role' => $role,
        'dates' => $dates,
    ]);


})->middleware('auth')->name('dashboard');

// PUBLIC
Route::middleware('guest')->group(function () {
    // Patients Login
    Route::get('/login', [LoginPatientController::class, 'show'])->name('login');
    Route::post('/patientLogin', [LoginPatientController::class, 'store'])->name('loginpatientStore');

    // Worker Login
    Route::get('/loginWorker', [LoginAdminController::class, 'show'])->name('loginWorker');
    Route::post('/work/login', [LoginAdminController::class, 'store'])->name('loginworkerStore');
});

// PRIVATE

// PATIENT AREA
Route::middleware(['auth:patient'])->group(function () {
    Route::get('/dashboardPatient', [DashboardPatientController::class, 'index'])->name('patientDashboard');
    Route::post('patient/logout', [LoginPatientController::class, 'destroy'])->name('loginpatientDestroy');
});

// WORKERS COMMON AREA
Route::middleware(['auth:admin', 'Worker', 'verified'])->group(function () {
    // New Appointment
    Route::get('/nova-cita', [DatesController::class, 'index'])->name('nova-cita');
    Route::post('/nova-cita', [DatesController::class, 'store'])->name('nova-cita-store');
    // Logout (disponible para todos los workers: admin, doctor, secretary)
    Route::get('/patientConsult/{nts}', [DatesController::class, 'ajaxPatient'])->name('ajax-patient');
    Route::get('/testConsult/{id}', [DatesController::class, 'ajaxTest'])->name('ajax-test');
    Route::post('work/logout', [LoginAdminController::class, 'destroy'])->name('loginworkerDestroy');
});

// ADMIN AREA
Route::middleware(['auth:admin', 'Admin', 'verified'])->group(function () {});

// SECRETARY AREA
Route::middleware(['auth:admin', 'Secretary', 'verified'])->group(function () {
   
});

// DOCTOR AREA
Route::middleware(['auth:admin', 'Doctor', 'verified'])->group(function () {});
require __DIR__.'/settings.php';
