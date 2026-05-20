<?php

namespace App\Http\Controllers\Workers\Doctor;

use App\Actions\Workers\Doctor\GeneratePatientReportPdf;
use App\Http\Controllers\Controller;
use App\Http\Requests\Worker\Doctor\DownloadPdfRequest;
use App\Models\Patient;
use App\Models\Worker;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\StreamedResponse;

class downloadpdfController extends Controller
{
    /**
     * Render the report PDF page with patient and worker data.
     */
    public function index(Patient $patient)
    {
        $workerId = Worker::query()
            ->where('user_id', Auth::id())
            ->value('id');

        return Inertia::render('Workers/Doctor/ReportPdf', [
            'patient' => [
                'id' => $patient->id,
                'name' => $patient->name,
                'address' => $patient->address,
                'birth_date' => $this->formatBirthDate($patient->birth_date),
                'nts' => $patient->nts,

            ],
            'workerId' => $workerId,
            'currentUser' => Auth::user(),
        ]);
    }

    /**
     * Stream the generated patient report PDF.
     */
    public function download(DownloadPdfRequest $request, GeneratePatientReportPdf $generatePdf): StreamedResponse
    {
        return $generatePdf->pdf($request->validated());
    }

    /**
     * Fetch patient data by NTS for the PDF form.
     */
    public function ajaxPatient(string $nts): JsonResponse
    {
        $patient = Patient::query()
            ->select('id', 'name', 'address', 'birth_date', 'nts')
            ->where('nts', $nts)
            ->first();

        if (! $patient) {
            return response()->json([
                'status' => 'error',
                'message' => 'Pacient no trobat',
            ], 404);
        }

        return response()->json([
            'id' => $patient->id,
            'name' => $patient->name,
            'address' => $patient->address,
            'birth_date' => $this->formatBirthDate($patient->birth_date),
            'nts' => $patient->nts,
        ]);
    }

    /**
     * Normalize a birth date value to Y-m-d for UI usage.
     */
    private function formatBirthDate(mixed $birthDate): ?string
    {
        if (! $birthDate) {
            return null;
        }

        return Carbon::parse($birthDate)->format('Y-m-d');
    }
}
