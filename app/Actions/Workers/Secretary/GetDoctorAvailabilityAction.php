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
    public function handle(int $doctorId, string $date, int $time, ?int $idDate = null): array
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
        $day = CarbonImmutable::createFromFormat('Y-m-d', $date)->startOfDay();
        $workStart = $day->setTime(8, 0);
        $workEnd = $day->setTime(15, 0);
        $dayStart = $day->startOfDay();
        $dayEnd = $day->endOfDay();

        $appointments = Date::query()
            ->where('worker_id', $workerId)
            ->whereBetween('date_time', [$dayStart, $dayEnd])
            ->where('estat', '!=', 'cancel·lada')
            ->where('id', '!=', $idDate)
            ->orderBy('date_time')
            ->get(['date_time', 'time']);

        $availableSlots = [];
        $startTimes = [];
        $cursor = $workStart;

        foreach ($appointments as $appointment) {
            $appointmentStart = CarbonImmutable::parse($appointment->date_time);
            $appointmentEnd = $appointmentStart->addMinutes(((int) $appointment->time) + 10);

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
                $startTimes = array_merge(
                    $startTimes,
                    $this->buildStartTimes($cursor, $appointmentStart, $requestedMinutes)
                );
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
            $startTimes = array_merge(
                $startTimes,
                $this->buildStartTimes($cursor, $workEnd, $requestedMinutes)
            );
        }

        return [
            'status' => 'success',
            'message' => 'Doctor trobat',
            'data' => [
                'required_minutes' => $requestedMinutes,
                'slots' => $availableSlots,
                'start_times' => array_values(array_unique($startTimes)),
            ],
        ];
    }

    /**
     * @return array<int, string>
     */
    private function buildStartTimes(
        CarbonImmutable $slotStart,
        CarbonImmutable $slotEnd,
        int $requiredMinutes
    ): array {
        $startTimes = [];
        $first = $this->roundUpToMinutes($slotStart, 5);

        if ($this->fitsInSlot($first, $slotEnd, $requiredMinutes)) {
            $startTimes[] = $first->format('G:i');
        }

        $cursor = $this->roundUpToMinutes($first, 15);

        if ($cursor->equalTo($first)) {
            $cursor = $first->addMinutes(15);
        }

        while ($this->fitsInSlot($cursor, $slotEnd, $requiredMinutes)) {
            $startTimes[] = $cursor->format('G:i');
            $cursor = $cursor->addMinutes(15);
        }

        return $startTimes;
    }

    private function roundUpToMinutes(CarbonImmutable $time, int $interval): CarbonImmutable
    {
        $remainder = $time->minute % $interval;

        if ($remainder === 0) {
            return $time;
        }

        return $time->addMinutes($interval - $remainder);
    }

    private function fitsInSlot(CarbonImmutable $start, CarbonImmutable $slotEnd, int $requiredMinutes): bool
    {
        return $start->addMinutes($requiredMinutes)->lessThanOrEqualTo($slotEnd);
    }
}
