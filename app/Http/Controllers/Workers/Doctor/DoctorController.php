<?php

namespace App\Http\Controllers\Workers\Doctor;

use App\Actions\Workers\Secretary\GetPatientAction;
use App\Http\Controllers\Controller;
use App\Models\Date;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DoctorController extends Controller
{
    public function index()
    {
        $userId = auth()->id();

        $worker = User::find($userId)?->worker;

        $dates = Date::with([
            'test',
            'patient',
        ])
            ->where('worker_id', $worker?->id)
            ->get();

        return Inertia::render('Workers/Dashboard', [
            'doctorDates' => $dates,
        ]);
    }

    public function patientSearch(Request $request, GetPatientAction $getPatientAction)
    {
        $nts = $request->query('nts');
        if (! $nts) {
            return Inertia::render('Workers/Doctor/PatientSearch', [
                'patient' => null,
                'needs' => [],
                'reports' => [],
                'searchedNts' => null,
            ]);
        }

        $result = $getPatientAction->handle($nts);

        if (! $result['available']) {
            return back()->with([
                'status' => $result['status'],
                'message' => $result['message'],
            ]);
        }

        return Inertia::render('Workers/Doctor/PatientSearch', [
            'patient' => $result['data']['patient'],
            'needs' => $result['data']['needs'],
            'reports' => $result['data']['reports'],
            'searchedNts' => $nts,
        ]);
    }
}