<?php

namespace App\Http\Controllers\Workers\Doctor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Date;
use App\Models\User;
use App\Actions\Workers\Secretary\GetPatientAction;

class DoctorController extends Controller
{
    public function index(){
        $userId = auth()->id();

        $worker = User::find($userId)?->worker;

        $dates = Date::with(['test', 'patient'])
            ->where('worker_id', $worker?->id)
            ->get();

        return Inertia::render('Workers/Dashboard', [
            'doctorDates' => $dates,
        ]);
    }

    public function patientSearch(Request $request, GetPatientAction $getPatientAction)
    {
        $nts = $request->query('nts');

        if (!$nts) {
            return Inertia::render('Doctor/PatientSearch', [
                'patient' => null,
                'needs' => [],
                'reports' => [],
                'searchedNts' => null,
            ]);
        }

        $result = $getPatientAction->handle($nts);

        if(!$result['available']){
            return redirect()->route('patientSearch')->with([
                'status' => $result['status'],
                'message' => $result['message'],
            ]);
        }

        return Inertia::render('Doctor/PatientSearch', [
            'patient' => $result['patient'],
            'needs' => $result['needs'],
            'reports' => $result['reports'],
            'searchedNts' => $nts,
        ]);
    }
}