<?php

namespace App\Http\Controllers\Patients;

use App\Http\Controllers\Controller;
use App\Models\Date;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class PatientController extends Controller
{
    public function index()
    {
        $dates = Date::with('test')->where('patient_id', Auth::user()->id)->get();

        return Inertia::render('Patient/patientDashboard', [
            'dates' => $dates,
        ]);
    }

    public function show()
    {
        $reports = auth('patient')->user()->reports()->with('worker.user')->get();

        return Inertia::render('Patient/patientReports', [
            'reports' => $reports,
        ]);
    }

    public function information()
    {
        return Inertia::render('Patient/patientInformation', [
            'patient' => Auth::guard('patient')->user(),
        ]);
    }

    public function update($date)
    {
        Date::where('patient_id', Auth::user()->id)
            ->where('id', $date)
            ->update([
                'estat' => 'cancel·lada',
            ]);

        return redirect()->back()->with([
            'status' => 'correcte',
            'message' => 'Cita cancelada correctament',
        ]);
    }
}
