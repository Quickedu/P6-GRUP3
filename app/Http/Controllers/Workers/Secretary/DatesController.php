<?php

namespace App\Http\Controllers\Workers\Secretary;

use App\Actions\Workers\Secretary\GetDoctorAvailabilityAction;
use App\Actions\Workers\Secretary\GetPatientConsultationAction;
use App\Actions\Workers\Secretary\GetTestConsultationAction;
use App\Http\Controllers\Controller;
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
        $dates = Date::with(['patient', 'worker.user', 'test'])->get();

        return $dates;
    }
}
