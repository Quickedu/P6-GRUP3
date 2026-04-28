<?php

namespace App\Actions\Workers\Secretary;

use App\Models\Patient;

class GetPatientConsultationAction
{
    /**
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
