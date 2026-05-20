<?php

namespace App\Actions\Workers\Secretary;

use App\Models\Patient;

/**
 * Action used to compute consultation-related metadata for a patient
 * identified by NTS. Primarily returns aggregated "needs" time so the
 * appointment flow can account for extra minutes.
 *
 * Called from: `DatesController::ajaxPatient()` in controller context.
 */
class GetPatientConsultationAction
{
    /**
    * Handle patient consultation lookup.
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

        $needs = $patient->needs()->get();

        if ($needs->isEmpty()) {
            return [
                'status' => 'success',
                'message' => 'Pacient trobat, no té necessitats associades',
                'available' => true,
                'data' => [
                    'id' => $patient->id,
                    'number' => 0,
                ],
            ];
        }

        $needsTime = (int) $needs->sum('time');

        return [
            'status' => 'success',
            'message' => $needsTime > 0
                ? 'Pacient trobat, necessitats associades trobades'
                : 'Pacient trobat, no té necessitats associades',
            'available' => true,
            'data' => [
                'id' => $patient->id,
                'number' => $needsTime,
            ],
        ];
    }
}
