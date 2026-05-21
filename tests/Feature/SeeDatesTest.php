<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

test('it returns all upcoming dates with relationships loaded', function () {
    $doctorUserId = DB::table('users')->insertGetId([
        'name' => 'Doctor See Dates',
        'email' => 'doctor.see.dates@example.com',
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
        'phone' => 600100011,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $secretaryUser = User::factory()->create([
        'role' => 'secretary',
    ]);

    $patientId = DB::table('patients')->insertGetId([
        'name' => 'Patient See Dates',
        'nts' => 'NTS900010',
        'address' => 'Test address',
        'dni' => '10000010Z',
        'phone' => 600100012,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $testId = DB::table('test_types')->insertGetId([
        'name' => 'Test Type See Dates',
        'time' => 30,
    ]);

    // Insert future dates
    DB::table('dates')->insert([
        [
            'patient_id' => $patientId,
            'worker_id' => $doctorWorkerId,
            'test_id' => $testId,
            'date_time' => now()->addDay(),
            'time' => 30,
            'estat' => 'programada',
            'urgencia' => 'no urgent',
            'description' => 'Future date 1',
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'patient_id' => $patientId,
            'worker_id' => $doctorWorkerId,
            'test_id' => $testId,
            'date_time' => now()->addDays(2),
            'time' => 30,
            'estat' => 'programada',
            'urgencia' => 'no urgent',
            'description' => 'Future date 2',
            'created_at' => now(),
            'updated_at' => now(),
        ],
    ]);

    // Insert past date (should not be returned)
    DB::table('dates')->insert([
        'patient_id' => $patientId,
        'worker_id' => $doctorWorkerId,
        'test_id' => $testId,
        'date_time' => now()->subDay(),
        'time' => 30,
        'estat' => 'programada',
        'urgencia' => 'no urgent',
        'description' => 'Past date',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $response = $this
        ->actingAs($secretaryUser, 'admin')
        ->withHeader('X-Inertia', 'true')
        ->withHeader('X-Inertia-Version', hash('xxh128', (string) config('app.asset_url')))
        ->get(route('dashboard'));

    $response->assertSuccessful();
    $response->assertJsonCount(2, 'props.dates');
    $response->assertJsonPath('props.dates.0.description', 'Future date 1');
    $response->assertJsonPath('props.dates.0.patient.name', 'Patient See Dates');
    $response->assertJsonPath('props.dates.0.worker.user.name', 'Doctor See Dates');
    $response->assertJsonPath('props.dates.0.test.name', 'Test Type See Dates');
    $response->assertJsonPath('props.dates.1.description', 'Future date 2');
});

test('it returns dates ordered by date_time ascending', function () {
    $doctorUserId = DB::table('users')->insertGetId([
        'name' => 'Doctor Ordered Dates',
        'email' => 'doctor.ordered@example.com',
        'role' => 'doctor',
        'password' => bcrypt('password'),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $doctorWorkerId = DB::table('workers')->insertGetId([
        'user_id' => $doctorUserId,
        'nss' => 'NSS900011',
        'address' => 'Test address',
        'dni' => '90000011A',
        'phone' => 600100013,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $secretaryUser = User::factory()->create([
        'role' => 'secretary',
    ]);

    $patientId = DB::table('patients')->insertGetId([
        'name' => 'Patient Ordered Dates',
        'nts' => 'NTS900011',
        'address' => 'Test address',
        'dni' => '10000011Z',
        'phone' => 600100014,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $testId = DB::table('test_types')->insertGetId([
        'name' => 'Test Type Ordered',
        'time' => 30,
    ]);

    // Insert dates in random order
    DB::table('dates')->insert([
        [
            'patient_id' => $patientId,
            'worker_id' => $doctorWorkerId,
            'test_id' => $testId,
            'date_time' => now()->addDays(3),
            'time' => 30,
            'estat' => 'programada',
            'urgencia' => 'no urgent',
            'description' => 'Third',
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'patient_id' => $patientId,
            'worker_id' => $doctorWorkerId,
            'test_id' => $testId,
            'date_time' => now()->addDay(),
            'time' => 30,
            'estat' => 'programada',
            'urgencia' => 'no urgent',
            'description' => 'First',
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'patient_id' => $patientId,
            'worker_id' => $doctorWorkerId,
            'test_id' => $testId,
            'date_time' => now()->addDays(2),
            'time' => 30,
            'estat' => 'programada',
            'urgencia' => 'no urgent',
            'description' => 'Second',
            'created_at' => now(),
            'updated_at' => now(),
        ],
    ]);

    $response = $this
        ->actingAs($secretaryUser, 'admin')
        ->withHeader('X-Inertia', 'true')
        ->withHeader('X-Inertia-Version', hash('xxh128', (string) config('app.asset_url')))
        ->get(route('dashboard'));

    $response->assertSuccessful();
    $response->assertJsonCount(3, 'props.dates');
    $response->assertJsonPath('props.dates.0.description', 'First');
    $response->assertJsonPath('props.dates.1.description', 'Second');
    $response->assertJsonPath('props.dates.2.description', 'Third');
});

test('it excludes past dates from see dates', function () {
    $doctorUserId = DB::table('users')->insertGetId([
        'name' => 'Doctor Past Dates',
        'email' => 'doctor.past@example.com',
        'role' => 'doctor',
        'password' => bcrypt('password'),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $doctorWorkerId = DB::table('workers')->insertGetId([
        'user_id' => $doctorUserId,
        'nss' => 'NSS900012',
        'address' => 'Test address',
        'dni' => '90000012A',
        'phone' => 600100015,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $secretaryUser = User::factory()->create([
        'role' => 'secretary',
    ]);

    $patientId = DB::table('patients')->insertGetId([
        'name' => 'Patient Past Dates',
        'nts' => 'NTS900012',
        'address' => 'Test address',
        'dni' => '10000012Z',
        'phone' => 600100016,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $testId = DB::table('test_types')->insertGetId([
        'name' => 'Test Type Past',
        'time' => 30,
    ]);

    DB::table('dates')->insert([
        [
            'patient_id' => $patientId,
            'worker_id' => $doctorWorkerId,
            'test_id' => $testId,
            'date_time' => now()->subDays(5),
            'time' => 30,
            'estat' => 'completada',
            'urgencia' => 'no urgent',
            'description' => 'Old past date',
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'patient_id' => $patientId,
            'worker_id' => $doctorWorkerId,
            'test_id' => $testId,
            'date_time' => now()->subMinutes(5),
            'time' => 30,
            'estat' => 'completada',
            'urgencia' => 'no urgent',
            'description' => 'Recent past date',
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'patient_id' => $patientId,
            'worker_id' => $doctorWorkerId,
            'test_id' => $testId,
            'date_time' => now()->addMinutes(5),
            'time' => 30,
            'estat' => 'programada',
            'urgencia' => 'no urgent',
            'description' => 'Future date',
            'created_at' => now(),
            'updated_at' => now(),
        ],
    ]);

    $response = $this
        ->actingAs($secretaryUser, 'admin')
        ->withHeader('X-Inertia', 'true')
        ->withHeader('X-Inertia-Version', hash('xxh128', (string) config('app.asset_url')))
        ->get(route('dashboard'));

    $response->assertSuccessful();
    $response->assertJsonCount(1, 'props.dates');
    $response->assertJsonPath('props.dates.0.description', 'Future date');
});

test('it returns empty array when no upcoming dates exist', function () {
    $secretaryUser = User::factory()->create([
        'role' => 'secretary',
    ]);

    $response = $this
        ->actingAs($secretaryUser, 'admin')
        ->withHeader('X-Inertia', 'true')
        ->withHeader('X-Inertia-Version', hash('xxh128', (string) config('app.asset_url')))
        ->get(route('dashboard'));

    $response->assertSuccessful();
    $response->assertJsonCount(0, 'props.dates');
});
