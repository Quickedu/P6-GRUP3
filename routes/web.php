<?php

use App\Http\Controllers\LoginAdminController;
use App\Http\Controllers\DashboardPatientController;
use App\Http\Controllers\LoginPatientController;
use App\Http\Controllers\DatesController;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

Route::inertia('/', 'Welcome', [
    'canRegister' => Features::enabled(Features::registration()),
])->name('home');

Route::get('/dashboard', function (){
    $user = Auth::guard('admin')->user() ?? Auth::guard('patient')->user();
    $role = $user?->role ?? 'patient';
    
    return Inertia::render('Dashboard', [
    
        'role' => $role
    ]);
})->middleware('auth:admin,patient')->name('dashboard');

Route::middleware('guest')->group(function () {
    //Patients Login
    Route::get('/login', [LoginPatientController::class, 'show'])->name('login');
    Route::post('/patientLogin', [LoginPatientController::class, 'store'])->name('loginpatientStore');
    
    //Worker Login
    Route::get('/loginWorker', [LoginAdminController::class, 'show'])->name('loginWorker');
    Route::post('/work/login', [LoginAdminController::class, 'store'])->name('loginworkerStore');
});

Route::middleware('auth:patient')->group(function () {
    Route::post('patient/logout', [LoginPatientController::class, 'destroy'])->name('loginpatientDestroy');
});

Route::middleware(['auth', 'Admin'])->group(function () {
    Route::post('work/logout', [LoginAdminController::class, 'destroy'])->name('loginworkerDestroy');
});

//when login is implemented, use middleware to chech that user is a patient
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboardPatient', [DashboardPatientController::class, 'index'])->name('patientDashboard');
});

//New Appointment
Route::get('/nova-cita', [DatesController::class, 'index'])->name('nova-cita');
Route::post('/nova-cita', [DatesController::class, 'store'])->name('nova-cita-store');

require __DIR__.'/settings.php';
