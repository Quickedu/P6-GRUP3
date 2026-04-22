<?php

namespace App\Http\Controllers\Workers\Secretary;

use App\Http\Controllers\Controller;
use App\Http\Requests\Worker\StoreDateRequest;
use App\Models\Date;
use App\Models\Patient;
use App\Models\Test;
use App\Models\User;
use Inertia\Inertia;

class DatesController extends Controller
{
    public function index()
    {
        $doctors = User::where('role', '=', 'doctor')->get();
        $testTypes = Test::get();

        return Inertia::render('Workers/newDate', [
            'doctors' => $doctors,
            'testTypes' => $testTypes,
        ]);
    }

    public function store(StoreDateRequest $request)
    {
        $data = $request->validated();

        Date::create($data);

        return redirect()->back()->with('success', 'Cita creada correctamente');
    }

    public function ajaxPatient(string $nts)
    {
        $patient = Patient::query()->where('nts', $nts)->first();

        if (! $patient) {
            return response()->json([
                'status' => 'error',
                'message' => 'Pacient no trobat',
                'available' => false,
                'data' => [
                    'number' => 0,
                ],
            ]);
        }

        $needsTime = (int) $patient->needs()->sum('time');

        return response()->json([
            'status' => 'success',
            'message' => $needsTime > 0
                ? 'Pacient trobat, necessitats associades trobades'
                : 'Pacient trobat, no té necessitats associades',
            'available' => true,
            'data' => [
                'number' => $needsTime,
            ],
        ]);
    }

    public function ajaxTest(int $id)
    {
        $testType = Test::query()->find($id);

        if (! $testType) {
            return response()->json([
                'status' => 'error',
                'message' => 'Test no trobat',
                'data' => [
                    'number' => 0,
                ],
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Test trobat',
            'data' => [
                'number' => (int) $testType->time,
            ],
        ]);
    }

    public function seeDates()
    {
        $dates = Date::with(['patient', 'worker.user', 'test'])->get();

        return $dates;
    }
}
