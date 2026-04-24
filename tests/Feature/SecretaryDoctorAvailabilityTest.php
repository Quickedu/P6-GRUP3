<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

test('it returns free doctor slots that fit requested time plus ten minutes', function () {
    $doctorUserId = DB::table('users')->insertGetId([
        'name' => 'Doctor Test',
        'email' => 'doctor.test@example.com',
        'role' => 'doctor',
        'password' => bcrypt('password'),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $doctorWorkerId = DB::table('workers')->insertGetId([
        'user_id' => $doctorUserId,
        'nss' => 'NSS900001',
        'address' => 'Test address',
        'dni' => '90000001A',
        'phone' => 600100001,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $secretaryUser = User::factory()->create([
        'role' => 'secretary',
    ]);

    DB::table('patients')->insert([
        'id' => 1,
        'name' => 'Patient Test',
        'nts' => 'NTS900001',
        'address' => 'Test address',
        'dni' => '10000001Z',
        'phone' => 600100002,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    DB::table('test_types')->insert([
        'id' => 1,
        'name' => 'Test Type',
        'time' => 30,
    ]);

    DB::table('dates')->insert([
        [
            'patient_id' => 1,
            'worker_id' => $doctorWorkerId,
            'test_id' => 1,
            'date_time' => '2026-04-22 08:00:00',
            'time' => 30,
            'estat' => 'programada',
            'urgencia' => 'no urgent',
            'description' => 'Control de prova',
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'patient_id' => 1,
            'worker_id' => $doctorWorkerId,
            'test_id' => 1,
            'date_time' => '2026-04-22 09:30:00',
            'time' => 20,
            'estat' => 'programada',
            'urgencia' => 'no urgent',
            'description' => 'Control de prova',
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'patient_id' => 1,
            'worker_id' => $doctorWorkerId,
            'test_id' => 1,
            'date_time' => '2026-04-22 12:00:00',
            'time' => 60,
            'estat' => 'programada',
            'urgencia' => 'no urgent',
            'description' => 'Control de prova',
            'created_at' => now(),
            'updated_at' => now(),
        ],
    ]);

    $response = $this
        ->actingAs($secretaryUser, 'admin')
        ->getJson(route('ajax-doctor', ['id' => $doctorUserId, 'date' => '2026-04-22', 'time' => 30]));

    $response->assertSuccessful();
    $response->assertJsonPath('status', 'success');
    $response->assertJsonPath('data.required_minutes', 40);
    $response->assertJsonPath('data.slots', [
        '8:30 - 9:30',
        '9:50 - 12:00',
        '13:00 - 15:00',
    ]);
});

test('it returns test time for an existing test type', function () {
    $secretaryUser = User::factory()->create([
        'role' => 'secretary',
    ]);

    $testId = DB::table('test_types')->insertGetId([
        'name' => 'Radiografia',
        'time' => 25,
    ]);

    $response = $this
        ->actingAs($secretaryUser, 'admin')
        ->getJson(route('ajax-test', ['id' => $testId]));

    $response->assertSuccessful();
    $response->assertJsonPath('status', 'success');
    $response->assertJsonPath('data.number', 25);
});

test('it returns error for a missing test type', function () {
    $secretaryUser = User::factory()->create([
        'role' => 'secretary',
    ]);

    $response = $this
        ->actingAs($secretaryUser, 'admin')
        ->getJson(route('ajax-test', ['id' => 999999]));

    $response->assertSuccessful();
    $response->assertJsonPath('status', 'error');
});

test('it returns summed patient needs time', function () {
    $secretaryUser = User::factory()->create([
        'role' => 'secretary',
    ]);

    $patientId = DB::table('patients')->insertGetId([
        'name' => 'Pacient Test',
        'nts' => 'NTS555001',
        'address' => 'Test address',
        'dni' => '55500001Z',
        'phone' => 600555001,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $firstNeedId = DB::table('needs')->insertGetId(['name' => 'Cadira rodes', 'time' => 15]);
    $secondNeedId = DB::table('needs')->insertGetId(['name' => 'Acompanyament', 'time' => 20]);

    DB::table('patient_needs')->insert([
        ['patient_id' => $patientId, 'need_id' => $firstNeedId],
        ['patient_id' => $patientId, 'need_id' => $secondNeedId],
    ]);

    $response = $this
        ->actingAs($secretaryUser, 'admin')
        ->getJson(route('ajax-patient', ['nts' => 'NTS555001']));

    $response->assertSuccessful();
    $response->assertJsonPath('status', 'success');
    $response->assertJsonPath('data.number', 35);
});
