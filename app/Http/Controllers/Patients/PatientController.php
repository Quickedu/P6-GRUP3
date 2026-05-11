<?php

namespace App\Http\Controllers\Patients;

use App\Http\Controllers\Controller;
use App\Models\Date;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Models\Patient;
use App\Models\Report;
use App\Models\User;

class PatientController extends Controller
{
    public function index()
    {
        $dates = Date::with('test')->where('patient_id', Auth::user()->id)->get();

        return Inertia::render('Patient/patientDashboard', [
            'dates' => $dates,
        ]);
    }

    public function show(){
        $reports = auth('patient')->user()->reports()->with('worker.user')->get();

        return Inertia::render('Patient/patientReports', [
            'reports' => $reports
        ]);
    }
}