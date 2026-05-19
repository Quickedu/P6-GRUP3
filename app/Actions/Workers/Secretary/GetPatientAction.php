<?php

namespace App\Actions\Workers\Secretary;

use App\Models\Patient;

class GetPatientAction
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
