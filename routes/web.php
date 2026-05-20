<?php

use App\Http\Controllers\LoginAdminController;
use App\Http\Controllers\LoginPatientController;
use App\Http\Controllers\Patients\PatientController;
use App\Http\Controllers\Workers\Admin\DashboardAdminController;
use App\Http\Controllers\Workers\Admin\NeedAdminController;
use App\Http\Controllers\Workers\Admin\TestAdminController;
use App\Http\Controllers\Workers\Admin\WorkerAdminController;
use App\Http\Controllers\Workers\Doctor\DoctorController;
use App\Http\Controllers\Workers\Doctor\downloadpdfController;
use App\Http\Controllers\Workers\Secretary\DatesController;
use App\Http\Controllers\Workers\Secretary\PatientsListController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::inertia('/', 'HomePage', [
    'canRegister' => Features::enabled(Features::registration()),
])->name('home');

Route::inertia('/privacitat', 'PrivacyPolicy')->name('privacy');
Route::get('/cookies', fn () => Inertia::render('Cookies'))->name('cookies');

Route::get('/dashboard', function (DatesController $datesController) {
    $user = Auth::guard('admin')->user() ?? Auth::guard('patient')->user();
    $role = $user?->role ?? 'patient';

    if ($role === 'admin') {
        return app(DashboardAdminController::class)->index();
    }

    if ($role === 'doctor') {
        return app(DoctorController::class)->index();
    }

    $dates = $role === 'secretary' ? $datesController->seeDates() : [];
    $doctors = $role === 'secretary' ? $datesController->seeDoctors() : [];

    return Inertia::render('Workers/Dashboard', [
        'role' => $role,
        'dates' => $dates,
        'doctors' => $doctors,
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
    Route::get('/dashboardPatient', [PatientController::class, 'index'])->name('patientDashboard');
    Route::post('patient/logout', [LoginPatientController::class, 'destroy'])->name('loginpatientDestroy');
    Route::get('patient/reports', [PatientController::class, 'show'])->name('patientReports');
    Route::get('patient/information', [PatientController::class, 'information'])->name('patientInformation');
    Route::post('patient/cancelDate/{date}', [PatientController::class, 'update'])->name('cancelDate');
});

// WORKERS COMMON AREA
Route::middleware(['auth:admin', 'Worker', 'verified'])->group(function () {
    // Logout
    Route::post('work/logout', [LoginAdminController::class, 'destroy'])->name('loginworkerDestroy');
    // Patients List
    Route::get('/patientsList', [PatientsListController::class, 'index'])->name('patientsList');
    // Patient Detail
    Route::get('/patientDetail/{patient}', [PatientsListController::class, 'patientDetail'])->name('patientDetail');
});

// ADMIN AREA
Route::middleware(['auth:admin', 'Admin', 'verified'])->group(function () {
    Route::resource('tests', TestAdminController::class);
    Route::resource('needs', NeedAdminController::class);
    Route::resource('workers', WorkerAdminController::class);
});

// SECRETARY AREA
Route::middleware(['auth:admin', 'Secretary', 'verified'])->group(function () {
    // New Appointment
    Route::get('/nova-cita', [DatesController::class, 'index'])->name('nova-cita');
    Route::post('/nova-cita', [DatesController::class, 'store'])->name('nova-cita-store');
    // ajax for new appointment
    Route::get('/patientConsult/{nts}', [DatesController::class, 'ajaxPatient'])->name('ajax-patient');
    Route::get('/testConsult/{id}', [DatesController::class, 'ajaxTest'])->name('ajax-test');
    // reSchedule appointment
    Route::get('/dateSchedule/{date}', [DatesController::class, 'dateSchedule'])->name('dateSchedule');
    Route::put('/dateSchedule/{date}', [DatesController::class, 'reSchedule'])->name('dateSchedule.update');
    //
    Route::get('/patientConsult/{nts}', [DatesController::class, 'ajaxPatient'])->name('ajax-patient');
    Route::get('/testConsult/{id}', [DatesController::class, 'ajaxTest'])->name('ajax-test');
    Route::get('/doctorConsult/{id}/{idDate?}', [DatesController::class, 'ajaxDoctor'])->name('ajax-doctor');
    // Filter dates
    Route::get('/filter-dates', [DatesController::class, 'filterDates'])->name('filter-dates');
    Route::get('/filter-patient-dates', [DatesController::class, 'filterPatientDates'])->name('filter-patient-dates');

    // EDIT PATIENT INFO
    // General info
    Route::post('/patients/{patient}', [PatientsListController::class, 'update']);

    // Patient needs
    Route::get('/patients/{patient}/needs', [PatientsListController::class, 'patientsNeeds']);
    Route::post('/patients/{patient}/needs', [PatientsListController::class, 'addPatientNeed']);
    Route::delete('/patients/{patient}/needs/{need}', [PatientsListController::class, 'deletePatientNeed']);

    // Patient reports
    Route::get('/patients/{patient}/reports', [PatientsListController::class, 'patientsReports']);
});

// DOCTOR AREA
Route::middleware(['auth:admin', 'Doctor', 'verified'])->group(function () {
    Route::get('/formReport', [downloadpdfController::class, 'index'])->name('formReport');
    Route::post('/downloadReport', [downloadpdfController::class, 'download'])->name('downloadReport');
    Route::get('/formReport/patient/{nts}', [downloadpdfController::class, 'ajaxPatient'])->name('formReport.patient');
    Route::get('/doctor/patient-search', [DoctorController::class, 'patientSearch'])->name('patientSearch');
});
require __DIR__.'/settings.php';