<?php

namespace App\Actions\Workers\Secretary;

use App\Models\Patient;

/**
 * Action to retrieve a patient by NTS and related contextual data
 * (needs and recent reports).
 *
 * Called from: `DatesController::ajaxPatient()` when the frontend needs
 * patient lookup by CIP/NTS.
 */
class GetPatientAction
{
    /**
    * Find patient by NTS and return availability and related data.
    *
    * @param string $nts
    * @return array<string, mixed>
     */
    public function handle(string $nts): array
    {
        $patient = Patient::query()->where('nts', $nts)->first();

        if (! $patient) {
            return [
                'status' => 'error',
                'message' => 'Pacient no trobat',
                'available' => false,
                'data' => [

                ],
            ];
        }

        return [
            'status' => 'success',
            'message' => 'Pacient trobat',
            'available' => true,
            'data' => [
                'patient' => $patient,
                'needs' => $patient->needs,
                'reports' => $patient->reports()->with('worker.user')->latest()->get(),
            ],
        ];
    }
}
