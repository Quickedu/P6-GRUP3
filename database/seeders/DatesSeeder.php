<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatesSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('dates')->insert([
            [
                'patient_id' => 1,
                'worker_id' => 1,
                'test_id' => 1,
                'date_time' => '2026-06-15 15:20:00',
                'time' => 15,
                'estat' => 'programada',
                'urgencia' => 'no urgent',
                'description' => 'El pacient presenta una lesió a la mà dreta. El pacient presenta una lesió a la mà dreta. 
                                  El pacient presenta una lesió a la mà dreta. El pacient presenta una lesió a la mà dreta. 
                                  El pacient presenta una lesió a la mà dreta. El pacient presenta una lesió a la mà dreta. 
                                  El pacient presenta una lesió a la mà dreta. El pacient presenta una lesió a la mà dreta. 
                                  El pacient presenta una lesió a la mà dreta.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'patient_id' => 1,
                'worker_id' => 1,
                'test_id' => 2,
                'date_time' => '2026-04-10 12:30:00',
                'time' => 5,
                'estat' => 'cancel·lada',
                'urgencia' => 'preferent',
                'description' => 'El pacient presenta una lesió a la mà dreta. El pacient presenta una lesió a la mà dreta. 
                                  El pacient presenta una lesió a la mà dreta. El pacient presenta una lesió a la mà dreta. 
                                  El pacient presenta una lesió a la mà dreta. El pacient presenta una lesió a la mà dreta. 
                                  El pacient presenta una lesió a la mà dreta. El pacient presenta una lesió a la mà dreta. 
                                  El pacient presenta una lesió a la mà dreta.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'patient_id' => 1,
                'worker_id' => 1,
                'test_id' => 3,
                'date_time' => '2026-04-01 17:15:00',
                'time' => 8,
                'estat' => 'realitzada',
                'urgencia' => 'urgent',
                'description' => 'El pacient presenta una lesió a la mà dreta. El pacient presenta una lesió a la mà dreta. 
                                  El pacient presenta una lesió a la mà dreta. El pacient presenta una lesió a la mà dreta. 
                                  El pacient presenta una lesió a la mà dreta. El pacient presenta una lesió a la mà dreta. 
                                  El pacient presenta una lesió a la mà dreta. El pacient presenta una lesió a la mà dreta. 
                                  El pacient presenta una lesió a la mà dreta.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
