<?php

namespace App\Http\Controllers\Workers\Secretary;

use App\Actions\Workers\Secretary\GetDoctorAvailabilityAction;
use App\Actions\Workers\Secretary\GetDoctorDatesAction;
use App\Actions\Workers\Secretary\GetPatientConsultationAction;
use App\Actions\Workers\Secretary\GetPatientDatesAction;
use App\Actions\Workers\Secretary\GetTestConsultationAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Worker\FilterDatesRequest;
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

        return Inertia::render('Workers/NewDate', [
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

    public function ajaxDoctor(Request $request, GetDoctorAvailabilityAction $getDoctorAvailabilityAction, int $id, ?int $idDate = null): JsonResponse
    {
        $validate = $request->validate([
            'date' => ['required', 'date_format:Y-m-d'],
            'time' => ['required', 'integer', 'min:1'],
        ]);

        return response()->json($getDoctorAvailabilityAction->handle($id, $validate['date'], (int) $validate['time'], $idDate));
    }

    public function seeDates()
    {
        $dates = Date::with(['patient', 'worker.user', 'test'])->where('date_time', '>=', now())->orderBy('date_time')->get();

        return $dates;
    }

    public function seeDoctors()
    {
        return User::query()
            ->select('workers.id as id', 'users.name')
            ->join('workers', 'workers.user_id', '=', 'users.id')
            ->where('users.role', 'doctor')
            ->orderBy('users.name')
            ->get();
    }

    public function filterDates(FilterDatesRequest $request, GetDoctorDatesAction $getDoctorDatesAction): JsonResponse
    {
        $validated = $request->validated();

        $result = $getDoctorDatesAction->handle(
            $validated['date'] ?? null,
            $validated['doctor_id'] ?? null,
        );

        // Ensure consistent response format
        return response()->json([
            'status' => $result['status'] ?? 'success',
            'data' => $result['data'] ?? [],
            'message' => $result['message'] ?? '',
        ]);
    }

    public function filterPatientDates(FilterPatientByNtsRequest $request, GetPatientDatesAction $getPatientDatesAction): JsonResponse
    {
        $validated = $request->validated();

        $result = $getPatientDatesAction->handle($validated['nts']);

        // Ensure consistent response format
        return response()->json([
            'status' => $result['status'] ?? 'success',
            'data' => $result['data'] ?? [],
            'message' => $result['message'] ?? '',
        ]);
    }

    public function dateSchedule(string $date)
    {
        $dates = Date::with(['patient', 'worker.user', 'test'])
            ->whereDate('date_time', $date)
            ->orderBy('date_time')
            ->get();

        return response()->json($dates);
    }

    public function reSchedule(Date $date, Request $request): JsonResponse
    {
        $validated = $request->validate([
            'date_time' => ['required', 'date_format:Y-m-d H:i:s'],
        ]);
        $date->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Cita reprogramada correctament',
            'data' => $date->fresh(),
        ]);
    }
}
