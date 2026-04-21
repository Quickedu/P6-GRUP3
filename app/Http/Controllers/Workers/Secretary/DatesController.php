<?php

namespace App\Http\Controllers\Workers\Secretary;

use App\Http\Controllers\Controller;
use App\Http\Requests\Worker\StoreDateRequest;
use App\Models\Date;
use App\Models\Need;
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

        return Inertia::render('newDate', [
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

    public function ajaxPatient($nts)
    {
        $patient = Patient::where('nts', $nts)->first();
        if (! $patient) {
            return response()->json([
                'status' => 'error',
                'message' => 'Pacient no trobat',
                'data' => [],
            ]);
        }
        if ($patient) {
            $needs = Patient::where('nts', $nts)->first()->needs()->get();
            if ($needs->isEmpty()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Pacient trobat, no té necessitats associades',
                    'data' => [],
                ]);
            }
            $needsTime = 0;
            foreach ($needs as $need) {
                $times = Need::where('id', $need->id)->time->get();
                $needsTime += $times->sum('time');
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Pacient trobat, necessitats associades trobades',
                'data' => [
                    'number' => $needsTime,
                ],
            ]);
        }
    }

    public function ajaxTest($id) {
      $test = Test::where('id', $id)->time->get();
      if ($test->isEmpty()) {
        return response()->json([
          'status' => 'error',
          'message' => 'Test no trobat',
          'data' => [],
        ]);
      }
      return response()->json([
        'status' => 'success',
        'message' => 'Test trobat',
        'data' => [
          'number' => $test->sum('time'),
        ],
      ]);
    }
}
