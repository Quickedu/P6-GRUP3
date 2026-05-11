<?php

namespace App\Actions\Workers\Secretary;

use App\Models\Date;
use Carbon\Carbon;

class GetDoctorDatesAction
{
    /**
     * @return array<string, mixed>
     */
    public function handle(?string $date = null, ?int $doctorId = null): array
    {
        $query = Date::with(['patient', 'worker.user', 'test']);
        
        // Only apply date filter if a date is provided
        if ($date !== null) {
            $filterDate = Carbon::parse($date);
            $query->whereDate('date_time', $filterDate->format('Y-m-d'));
        }
        
        // Apply doctor filter if provided
        if ($doctorId !== null) {
            $query->where('worker_id', $doctorId);
        }
        
        $dates = $query->orderBy('date_time')->get();
        
        // Debug: Log the query and results
        \Log::info('Filter Dates Query:', [
            'date' => $date ?? 'not filtered',
            'doctor_id' => $doctorId ?? 'not filtered',
            'count' => $dates->count(),
            'sql' => $query->toSql(),
            'bindings' => $query->getBindings()
        ]);
        
        return [
            'status' => 'success',
            'message' => $dates->isEmpty()
                ? 'No hi ha cites amb els filtres seleccionats'
                : 'Cites trobades',
            'available' => true,
            'data' => $dates,
        ];
    }
}