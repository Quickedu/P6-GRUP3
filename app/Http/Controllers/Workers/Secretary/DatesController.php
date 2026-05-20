<?php

namespace App\Http\Controllers\Workers\Secretary;

use App\Actions\Workers\Secretary\GetDoctorAvailabilityAction;
use App\Actions\Workers\Secretary\GetDoctorDatesAction;
use App\Actions\Workers\Secretary\GetPatientConsultationAction;
use App\Actions\Workers\Secretary\GetPatientDatesAction;
use App\Actions\Workers\Secretary\GetTestConsultationAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Worker\DoctorAvailabilityRequest;
use App\Http\Requests\Worker\FilterDatesRequest;
use App\Http\Requests\Worker\FilterPatientByNtsRequest;
use App\Http\Requests\Worker\RescheduleDateRequest;
use App\Http\Requests\Worker\StoreDateRequest;
use App\Models\Date;
use App\Models\Test;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Inertia\Inertia;

/**
 * Controller for worker/secretary operations related to appointment
 * management (dates). Exposes pages and AJAX endpoints consumed by the
 * frontend (Workers/NewDate Vue page and related components).
 */
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

    /**
     * Store a new `Date` (appointment).
     *
     * Called from: the NewDate form POST action. Uses `StoreDateRequest`
     * for validation.
     *
     * @param StoreDateRequest $request
     */
    public function store(StoreDateRequest $request)
    {
        $data = $request->validated();
        Date::create($data);

        return redirect()->back()->with(['status' => 'correcte', 'message' => 'Cita creada correctamente']);
    }

    /**
     * AJAX endpoint: lookup patient consultation metadata by NTS.
     * Delegates to `GetPatientConsultationAction`.
     *
     * @param string $nts
     * @param GetPatientConsultationAction $getPatientConsultationAction
     * @return JsonResponse
     */
    public function ajaxPatient(string $nts, GetPatientConsultationAction $getPatientConsultationAction): JsonResponse
    {
        return response()->json($getPatientConsultationAction->handle($nts));
    }

    /**
     * AJAX endpoint: get test duration by id. Delegates to
     * `GetTestConsultationAction`.
     *
     * @param int $id
     * @param GetTestConsultationAction $getTestConsultationAction
     * @return JsonResponse
     */
    public function ajaxTest(int $id, GetTestConsultationAction $getTestConsultationAction): JsonResponse
    {
        return response()->json($getTestConsultationAction->handle($id));
    }

    /**
     * AJAX endpoint: compute doctor availability for a given date and time.
     * Uses `DoctorAvailabilityRequest` for validation and delegates to
     * `GetDoctorAvailabilityAction`.
     *
     * @param DoctorAvailabilityRequest $request
     * @param GetDoctorAvailabilityAction $getDoctorAvailabilityAction
     * @param int $id doctor user id
     * @param int|null $idDate optional date id to exclude (reschedule case)
     * @return JsonResponse
     */
    public function ajaxDoctor(DoctorAvailabilityRequest $request, GetDoctorAvailabilityAction $getDoctorAvailabilityAction, int $id, ?int $idDate = null): JsonResponse
    {
        $validated = $request->validated();

        return response()->json($getDoctorAvailabilityAction->handle($id, $validated['date'], (int) $validated['time'], $idDate));
    }

    /**
     * Return all upcoming dates (raw list) for administrative views.
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function seeDates()
    {
        $dates = Date::with(['patient', 'worker.user', 'test'])->where('date_time', '>=', now())->orderBy('date_time')->get();

        return $dates;
    }

    /**
     * Return a simple list of doctors for select controls.
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function seeDoctors()
    {
        return User::query()
            ->select('workers.id as id', 'users.name')
            ->join('workers', 'workers.user_id', '=', 'users.id')
            ->where('users.role', 'doctor')
            ->orderBy('users.name')
            ->get();
    }

    /**
     * AJAX endpoint: filter appointments by date and/or doctor.
     * Delegates to `GetDoctorDatesAction` and normalizes the response shape.
     *
     * @param FilterDatesRequest $request
     * @param GetDoctorDatesAction $getDoctorDatesAction
     * @return JsonResponse
     */
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

    /**
     * AJAX endpoint: return future appointments for a patient (by NTS).
     * Delegates to `GetPatientDatesAction`.
     *
     * @param FilterPatientByNtsRequest $request
     * @param GetPatientDatesAction $getPatientDatesAction
     * @return JsonResponse
     */
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

    /**
     * Return all appointments for a specific date.
     *
     * @param string $date date string (Y-m-d)
     * @return JsonResponse
     */
    public function dateSchedule(string $date)
    {
        $dates = Date::with(['patient', 'worker.user', 'test'])
            ->whereDate('date_time', $date)
            ->orderBy('date_time')
            ->get();

        return response()->json($dates);
    }

    /**
     * Re-schedule (update) an existing Date record.
     *
     * @param Date $date
     * @param RescheduleDateRequest $request
     * @return JsonResponse
     */
    public function reSchedule(Date $date, RescheduleDateRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $date->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Cita reprogramada correctament',
            'data' => $date->fresh(),
        ]);
    }

    public function cancel(Date $date): JsonResponse
    {
        // Validate that the date is in the future
        if ($date->date_time <= now()) {
            return response()->json([
                'status' => 'error',
                'message' => 'No es pot cancel·lar una cita passada',
            ], 422);
        }

        // Cancel the date
        $date->update([
            'estat' => 'cancel·lada',
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Cita cancel·lada correctament',
            'data' => $date->fresh(),
        ]);
    }
}