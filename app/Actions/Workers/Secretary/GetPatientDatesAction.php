<?php

namespace App\Actions\Workers\Secretary;

use App\Models\Date;
use App\Models\Patient;

class GetPatientDatesAction
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
                'data' => [],
            ];
        }

        $dates = Date::with(['patient', 'worker.user', 'test'])
            ->where('patient_id', $patient->id)
            ->where('date_time', '>=', now())
            ->orderBy('date_time')
            ->get();

        return [
            'status' => 'success',
            'message' => $dates->isEmpty()
                ? 'Pacient trobat, no té cites programades'
                : 'Cites del pacient trobades',
            'available' => true,
            'data' => $dates,
        ];
    }
}
