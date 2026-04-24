<?php

namespace App\Actions\Workers\Secretary;

use App\Models\Date;
use App\Models\User;
use App\Models\Worker;
use Carbon\CarbonImmutable;

class GetDoctorAvailabilityAction
{
    /**
     * @return array<string, mixed>
     */
    public function handle(int $doctorId, string $date, int $time): array
    {
        $doctor = User::query()
            ->whereKey($doctorId)
            ->where('role', 'doctor')
            ->first();

        if (! $doctor) {
            return [
                'status' => 'error',
                'message' => 'Doctor no trobat',
                'data' => [],
            ];
        }

        $workerId = Worker::query()
            ->where('user_id', $doctor->id)
            ->value('id');

        if (! $workerId) {
            return [
                'status' => 'error',
                'message' => 'Treballador no trobat per aquest doctor',
                'data' => [],
            ];
        }

        $requestedMinutes = $time + 10;
        $day = CarbonImmutable::createFromFormat('Y-m-d', $date);
        $workStart = $day->setTime(8, 0);
        $workEnd = $day->setTime(15, 0);

        $appointments = Date::query()
            ->where('worker_id', $workerId)
            ->whereDate('date_time', $date)
            ->where('estat', 'programada')
            ->orderBy('date_time')
            ->get(['date_time', 'time']);

        $availableSlots = [];
        $cursor = $workStart;

        foreach ($appointments as $appointment) {
            $appointmentStart = CarbonImmutable::parse($appointment->date_time);
            $appointmentEnd = $appointmentStart->addMinutes((int) $appointment->time);

            if ($appointmentEnd->lessThanOrEqualTo($workStart) || $appointmentStart->greaterThanOrEqualTo($workEnd)) {
                continue;
            }

            if ($appointmentStart->lessThan($workStart)) {
                $appointmentStart = $workStart;
            }

            if ($appointmentEnd->greaterThan($workEnd)) {
                $appointmentEnd = $workEnd;
            }

            if ($appointmentStart->greaterThan($cursor) && $cursor->diffInMinutes($appointmentStart) >= $requestedMinutes) {
                $availableSlots[] = $cursor->format('G:i').' - '.$appointmentStart->format('G:i');
            }

            if ($appointmentEnd->greaterThan($cursor)) {
                $cursor = $appointmentEnd;
            }

            if ($cursor->greaterThanOrEqualTo($workEnd)) {
                break;
            }
        }

        if ($cursor->lessThan($workEnd) && $cursor->diffInMinutes($workEnd) >= $requestedMinutes) {
            $availableSlots[] = $cursor->format('G:i').' - '.$workEnd->format('G:i');
        }

        return [
            'status' => 'success',
            'message' => 'Doctor trobat',
            'data' => [
                'required_minutes' => $requestedMinutes,
                'slots' => $availableSlots,
            ],
        ];
    }
}
