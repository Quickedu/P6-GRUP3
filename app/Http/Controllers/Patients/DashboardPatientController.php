<?php

namespace App\Http\Controllers\Patients;

use App\Http\Controllers\Controller;
use App\Models\Date;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardPatientController extends Controller
{
    public function index()
    {
        $dates = Date::get(); /* where('user_id', Auth::user()->id); */

        return Inertia::render('Patient/patientDashboard', [
            'dates' => $dates,
        ]);
    }
}
