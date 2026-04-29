<?php

namespace App\Http\Controllers\Patients;

use App\Http\Controllers\Controller;
use App\Models\Date;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardPatientController extends Controller
{
    public function index()
    {
        $dates = Date::with('test')->where('patient_id', Auth::user()->id)->get();

        // dd($dates);
        return Inertia::render('Patient/patientDashboard', [
            'dates' => $dates,
        ]);
    }
}
