<?php

namespace App\Actions\Workers\Secretary;

use App\Models\Date;
use App\Models\Patient;
use Carbon\Carbon;

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
            ->where('estat', 'programada')
            ->where('date_time', '>=', Carbon::now())
            ->orderBy('date_time')
            ->get();

        // Debug: Log the results
        \Log::info('Patient Dates Query:', [
            'nts' => $nts,
            'patient_id' => $patient->id,
            'count' => $dates->count(),
        ]);

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
