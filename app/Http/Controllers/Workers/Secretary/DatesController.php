<?php

namespace App\Http\Controllers\Workers\Secretary;

use App\Http\Controllers\Controller;
use App\Http\Requests\Worker\StoreDateRequest;
use App\Models\Date;
use App\Models\Patient;
use App\Models\Test;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

        return redirect()->back()->with(['status' => 'correcte', 'message' => 'Cita creada correctamente']);
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
        if ($patient) {
            $needs = $patient->needs()->get();
            if ($needs->isEmpty()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Pacient trobat, no té necessitats associades',
                    'data' => [],
                ]);
            }
            $needsTime = (int) $needs->sum('time');

        return response()->json([
            'status' => 'success',
            'message' => $needsTime > 0
                ? 'Pacient trobat, necessitats associades trobades'
                : 'Pacient trobat, no té necessitats associades',
            'available' => true,
            'data' => [
                'id' => $patient->id,
                'number' => $needsTime,
            ],
        ]);
    }
    }


    public function ajaxTest($id)
    {
        $testTime = Test::query()->whereKey($id)->value('time');

        if ($testTime === null) {
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
                'number' => (int) $testTime,
            ],
        ]);
    }

    public function ajaxDoctor($id, Request $request)
    {
        $validate = $request->validate([
            'date' => ['required', 'date_format:Y-m-d'],
            'time' => ['required', 'integer', 'min:1'],
        ]);

        $doctor = User::where('id', $id)
            ->where('role', 'doctor')
            ->first();

        if (! $doctor) {
            return response()->json([
                'status' => 'error',
                'message' => 'Doctor no trobat',
                'data' => [],
            ]);
        }

        $workerId = DB::table('workers')
            ->where('user_id', $doctor->id)
            ->value('id');

        if (! $workerId) {
            return response()->json([
                'status' => 'error',
                'message' => 'Treballador no trobat per aquest doctor',
                'data' => [],
            ]);
        }

        $requestedMinutes = (int) $validate['time'] + 10;
        $day = CarbonImmutable::createFromFormat('Y-m-d', $validate['date']);
        $workStart = $day->setTime(8, 0);
        $workEnd = $day->setTime(15, 0);

        $appointments = Date::query()
            ->where('worker_id', $workerId)
            ->whereDate('date_time', $validate['date'])
            ->where('estat', 'programada')
            ->orderBy('date_time')
            ->get(['date_time', 'time']);

        $availableSlots = [];
        $cursor = $workStart;

        foreach ($appointments as $appointment) {
            $appointmentStart = CarbonImmutable::parse($appointment->date_time);
            $appointmentEnd = $appointmentStart->addMinutes((int) $appointment->time);

            if ($appointmentEnd->lessThanOrEqualTo($workStart) || $appointmentStart->greaterThanOrEqualTo($workEnd)) {
                continue;
            }

            if ($appointmentStart->lessThan($workStart)) {
                $appointmentStart = $workStart;
            }

            if ($appointmentEnd->greaterThan($workEnd)) {
                $appointmentEnd = $workEnd;
            }

            if ($appointmentStart->greaterThan($cursor) && $cursor->diffInMinutes($appointmentStart) >= $requestedMinutes) {
                $availableSlots[] = $cursor->format('G:i').' - '.$appointmentStart->format('G:i');
            }

            if ($appointmentEnd->greaterThan($cursor)) {
                $cursor = $appointmentEnd;
            }

            if ($cursor->greaterThanOrEqualTo($workEnd)) {
                break;
            }
        }

        if ($cursor->lessThan($workEnd) && $cursor->diffInMinutes($workEnd) >= $requestedMinutes) {
            $availableSlots[] = $cursor->format('G:i').' - '.$workEnd->format('G:i');
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Doctor trobat',
            'data' => [
                'required_minutes' => $requestedMinutes,
                'slots' => $availableSlots,
            ],
        ]);
    }
}
