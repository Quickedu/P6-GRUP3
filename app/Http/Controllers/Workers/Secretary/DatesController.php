<?php

namespace App\Http\Controllers\Workers\Secretary;

use App\Actions\Workers\Secretary\GetDoctorAvailabilityAction;
use App\Actions\Workers\Secretary\GetPatientConsultationAction;
use App\Actions\Workers\Secretary\GetPatientDatesAction;
use App\Actions\Workers\Secretary\GetTestConsultationAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Worker\FilterPatientByNtsRequest;
use App\Http\Requests\Worker\StoreDateRequest;
use App\Models\Date;
use App\Models\Test;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DatesController extends Controller
{
    public function index()
    {
        $doctors = User::query()
            ->select('users.id', 'users.name', 'users.email', 'users.role', 'workers.id as worker_id')
            ->join('workers', 'workers.user_id', '=', 'users.id')
            ->where('users.role', 'doctor')
            ->orderBy('users.name')
            ->get();
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

    public function ajaxPatient(string $nts, GetPatientConsultationAction $getPatientConsultationAction): JsonResponse
    {
        return response()->json($getPatientConsultationAction->handle($nts));
    }

    public function ajaxTest(int $id, GetTestConsultationAction $getTestConsultationAction): JsonResponse
    {
        return response()->json($getTestConsultationAction->handle($id));
    }

    public function ajaxDoctor(int $id, Request $request, GetDoctorAvailabilityAction $getDoctorAvailabilityAction): JsonResponse
    {
        $validate = $request->validate([
            'date' => ['required', 'date_format:Y-m-d'],
            'time' => ['required', 'integer', 'min:1'],
        ]);

        return response()->json($getDoctorAvailabilityAction->handle($id, $validate['date'], (int) $validate['time']));
    }

    public function seeDates()
    {
        $dates = Date::with(['patient', 'worker.user', 'test'])->where('date_time', '>=', now())->orderBy('date_time')->get();

        return $dates;
    }

    public function filterDates(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'date' => ['nullable', 'date_format:Y-m-d'],
            'doctorName' => ['nullable', 'string', 'max:255'],
        ]);

        $query = Date::with(['patient', 'worker.user', 'test'])
            ->where('date_time', '>=', now());

        if ($validated['date'] ?? null) {
            $query->whereDate('date_time', $validated['date']);
        }

        if ($validated['doctorName'] ?? null) {
            $query->whereHas('worker.user', function ($q) use ($validated) {
                $q->where('name', 'like', '%'.$validated['doctorName'].'%');
            });
        }

        $dates = $query->orderBy('date_time')->get();

        return response()->json($dates);
    }

    public function filterPatientDates(FilterPatientByNtsRequest $request, GetPatientDatesAction $getPatientDatesAction): JsonResponse
    {
        $validated = $request->validated();

        return response()->json($getPatientDatesAction->handle($validated['nts']));
    }
}
