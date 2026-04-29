<?php

namespace App\Http\Controllers\Workers\Doctor;

use App\Actions\Workers\Doctor\GeneratePatientReportPdf;
use App\Http\Controllers\Controller;
use App\Http\Requests\Worker\DownloadPdfRequest;
use App\Models\Patient;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class downloadpdfController extends Controller
{ 

public function index(Patient $patient)
    {
        $user = Auth::user();

        return Inertia::render('Workers/Doctor/ReportPdf', [
            'patient' => [
                'id' => $patient->id,
                'name' => $patient->name,
                'address' => $patient->address,
                'birth_date' => $this->formatBirthDate($patient->birth_date),
                'nts' => $patient->nts,

            ],
            'currentUser' => Auth::user(),
        ]);
    }

    public function download(DownloadPdfRequest $request, GeneratePatientReportPdf $generatePdf)
    {
        dd('hola');
        return $generatePdf($request->validated());
    }

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

    private function formatBirthDate(mixed $birthDate): ?string
    {
        if (! $birthDate) {
            return null;
        }

        return Carbon::parse($birthDate)->format('Y-m-d');
    }
}