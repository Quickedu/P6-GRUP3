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

        $dates = [];
        $dateId = 1;

        foreach ($patients as $patientIndex => $patientId) {
            foreach ([1, 2, 3] as $appointmentIndex => $testId) {
                $dates[] = [
                    'id' => $dateId,
                    'patient_id' => $patientId,
                    'worker_id' => $doctorWorkerIds[$appointmentIndex % $doctorWorkerIds->count()],
                    'test_id' => $testId,
                    'date_time' => now()
                        ->addDays(($patientIndex * 3) + $appointmentIndex + 1)
                        ->setTime(9 + $appointmentIndex, $patientIndex * 10)
                        ->format('Y-m-d H:i:s'),
                    'time' => $testTimes[$testId],
                    'estat' => ['programada', 'cancel·lada', 'realitzada'][$appointmentIndex],
                    'urgencia' => ['no urgent', 'preferent', 'urgent'][$appointmentIndex],
                    'description' => sprintf(
                        'Seeded appointment %d for patient %d.',
                        $appointmentIndex + 1,
                        $patientIndex + 1,
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
