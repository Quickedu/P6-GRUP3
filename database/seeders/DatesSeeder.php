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

        $testTypes = DB::table('test_types')
            ->orderBy('id')
            ->limit(3)
            ->get(['id', 'time'])
            ->values();

        // 6 citas por paciente: las primeras 3 pruebas se repiten con distinto dia/hora
        $appointments = [
            ['testIndex' => 0, 'estat' => 'realitzada',   'urgencia' => 'no urgent',  'hourOffset' => 0,  'minuteOffset' => 0],
            ['testIndex' => 1, 'estat' => 'realitzada',   'urgencia' => 'preferent',  'hourOffset' => 1,  'minuteOffset' => 15],
            ['testIndex' => 2, 'estat' => 'cancel·lada',  'urgencia' => 'urgent',     'hourOffset' => 2,  'minuteOffset' => 30],
            ['testIndex' => 0, 'estat' => 'programada',   'urgencia' => 'no urgent',  'hourOffset' => 3,  'minuteOffset' => 0],
            ['testIndex' => 1, 'estat' => 'programada',   'urgencia' => 'preferent',  'hourOffset' => 4,  'minuteOffset' => 15],
            ['testIndex' => 2, 'estat' => 'programada',   'urgencia' => 'urgent',     'hourOffset' => 5,  'minuteOffset' => 30],
        ];

        $appointmentCount = count($appointments);

        $dates = [];
        $dateId = 1;

        foreach ($patients as $patientIndex => $patientId) {
            foreach ($appointments as $apptIndex => $appt) {
                $testType = $testTypes[$appt['testIndex']];

                $dates[] = [
                    'id' => $dateId,
                    'patient_id' => $patientId,
                    'worker_id' => $doctorWorkerIds[$apptIndex % $doctorWorkerIds->count()],
                    'test_id' => $testType->id,
                    'date_time' => now()
                        ->addDays(($patientIndex * $appointmentCount) + $apptIndex + 1)
                        ->setTime(9 + $appt['hourOffset'], $appt['minuteOffset'])
                        ->format('Y-m-d H:i:s'),
                    'time' => $testType->time,
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
