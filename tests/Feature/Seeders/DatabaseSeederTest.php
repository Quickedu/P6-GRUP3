<?php

use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

test('database seeder creates workers patients and patient dates', function () {
    $this->seed(DatabaseSeeder::class);

    foreach (['admin', 'doctor', 'technician', 'secretary'] as $role) {
        expect(DB::table('users')->where('role', $role)->count())->toBe(3);
    }

    expect(DB::table('patients')->count())->toBe(3)
        ->and(DB::table('workers')->count())->toBe(12)
        ->and(DB::table('dates')->count())->toBe(9);

    $doctorWorkerIds = DB::table('workers')
        ->join('users', 'users.id', '=', 'workers.user_id')
        ->where('users.role', 'doctor')
        ->pluck('workers.id');

    DB::table('patients')->orderBy('id')->pluck('id')->each(function (int $patientId) use ($doctorWorkerIds): void {
        $patientDates = DB::table('dates')->where('patient_id', $patientId)->get();

        expect($patientDates)->toHaveCount(3)
            ->and($patientDates->pluck('date_time')->unique())->toHaveCount(3)
            ->and($patientDates->pluck('worker_id')->diff($doctorWorkerIds))->toBeEmpty();
    });
});
