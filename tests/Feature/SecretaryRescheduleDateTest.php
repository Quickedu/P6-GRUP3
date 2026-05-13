<?php

use App\Models\Date;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

test('secretary can reschedule a date', function () {
    $doctorUserId = DB::table('users')->insertGetId([
        'name' => 'Doctor Reschedule',
        'email' => 'doctor.reschedule@example.com',
        'role' => 'doctor',
        'password' => bcrypt('password'),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $doctorWorkerId = DB::table('workers')->insertGetId([
        'user_id' => $doctorUserId,
        'nss' => 'NSS900010',
        'address' => 'Test address',
        'dni' => '90000010A',
        'phone' => 600100010,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $secretaryUser = User::factory()->create([
        'role' => 'secretary',
    ]);

    $patientId = DB::table('patients')->insertGetId([
        'name' => 'Patient Reschedule',
        'nts' => 'NTS900010',
        'address' => 'Test address',
        'dni' => '10000010Z',
        'phone' => 600100011,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $testId = DB::table('test_types')->insertGetId([
        'name' => 'Test Type',
        'time' => 20,
    ]);

    $dateId = DB::table('dates')->insertGetId([
        'patient_id' => $patientId,
        'worker_id' => $doctorWorkerId,
        'test_id' => $testId,
        'date_time' => '2026-04-22 09:00:00',
        'time' => 20,
        'estat' => 'programada',
        'urgencia' => 'no urgent',
        'description' => 'Control de prova',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $response = $this
        ->actingAs($secretaryUser, 'admin')
        ->putJson(route('dateSchedule.update', ['date' => $dateId]), [
            'date_time' => '2026-04-22 10:30:00',
        ]);

    $response->assertSuccessful();
    $response->assertJsonPath('status', 'success');

    expect(Date::findOrFail($dateId)->date_time)->toBe('2026-04-22 10:30:00');
});
