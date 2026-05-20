<?php

namespace App\Actions\Workers\Secretary;

use App\Models\Date;
use Carbon\Carbon;

/**
 * Action that retrieves scheduled dates (appointments) filtered by date
 * and/or doctor. Returns structured data suitable for JSON responses.
 *
 * Called from: `App\Http\Controllers\Workers\Secretary\DatesController::filterDates()`
 */
class GetDoctorDatesAction
{
    /**
    * Handle the query for doctor dates.
    *
    * @param string|null $date Optional date string to filter (Y-m-d).
    * @param int|null $doctorId Optional worker/doctor id to filter results.
    * @param bool $defaultToToday When true and no date/doctor provided, default to today.
    * @return array<string, mixed>
     */
    public function handle(?string $date = null, ?int $doctorId = null, bool $defaultToToday = true): array
    {
        $query = Date::with(['patient', 'worker.user', 'test'])->where('estat', 'programada');

        // only apply date filter if a date is provided
        if ($date !== null) {
            $filterDate = Carbon::parse($date);
            $query->whereDate('date_time', $filterDate->format('Y-m-d'));
        } elseif ($defaultToToday && $doctorId === null) {
            // default to today's date
            $query->whereDate('date_time', Carbon::today()->format('Y-m-d'));
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
            'bindings' => $query->getBindings(),
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
