<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatesSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        $patients = DB::table('patients')
            ->whereIn('nts', ['NTSS0000000001', 'NTSS0000000002', 'NTSS0000000003'])
            ->orderBy('id')
            ->pluck('id')
            ->values();

        $doctorWorkerIds = DB::table('workers')
            ->join('users', 'users.id', '=', 'workers.user_id')
            ->where('users.role', 'doctor')
            ->orderBy('workers.id')
            ->pluck('workers.id')
            ->values();

        $testTimes = DB::table('test_types')
            ->whereIn('id', [1, 2, 3])
            ->pluck('time', 'id');

        // 6 citas por paciente: los primeros 3 testId se repiten con distinto dia/hora
        $appointments = [
            ['testId' => 1, 'estat' => 'realitzada',   'urgencia' => 'no urgent',  'hourOffset' => 0,  'minuteOffset' => 0],
            ['testId' => 2, 'estat' => 'realitzada',   'urgencia' => 'preferent',  'hourOffset' => 1,  'minuteOffset' => 15],
            ['testId' => 3, 'estat' => 'cancel·lada',  'urgencia' => 'urgent',     'hourOffset' => 2,  'minuteOffset' => 30],
            ['testId' => 1, 'estat' => 'programada',   'urgencia' => 'no urgent',  'hourOffset' => 3,  'minuteOffset' => 0],
            ['testId' => 2, 'estat' => 'programada',   'urgencia' => 'preferent',  'hourOffset' => 4,  'minuteOffset' => 15],
            ['testId' => 3, 'estat' => 'programada',   'urgencia' => 'urgent',     'hourOffset' => 5,  'minuteOffset' => 30],
        ];

        $dates = [];
        $dateId = 1;

        foreach ($patients as $patientIndex => $patientId) {
            foreach ($appointments as $apptIndex => $appt) {
                $dates[] = [
                    'id' => $dateId,
                    'patient_id' => $patientId,
                    'worker_id' => $doctorWorkerIds[$apptIndex % $doctorWorkerIds->count()],
                    'test_id' => $appt['testId'],
                    'date_time' => now()
                        ->addDays(($patientIndex * 6) + $apptIndex + 1)
                        ->setTime(9 + $appt['hourOffset'], $appt['minuteOffset'])
                        ->format('Y-m-d H:i:s'),
                    'time' => $testTimes[$appt['testId']],
                    'estat' => $appt['estat'],
                    'urgencia' => $appt['urgencia'],
                    'description' => sprintf(
                        'Cita %d per al pacient %d (%s).',
                        $apptIndex + 1,
                        $patientIndex + 1,
                        $appt['estat'],
                    ),
                    'created_at' => $now,
                    'updated_at' => $now,
                ];

                $dateId++;
            }
        }

        DB::table('dates')->upsert($dates, ['id'], [
            'patient_id',
            'worker_id',
            'test_id',
            'date_time',
            'time',
            'estat',
            'urgencia',
            'description',
            'updated_at',
        ]);
    }
}
