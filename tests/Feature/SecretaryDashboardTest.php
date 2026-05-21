<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Inertia\Testing\AssertableInertia as Assert;

uses(RefreshDatabase::class);

test('guests are redirected to the login page from secretary dashboard', function () {
    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('login'));
});

test('authenticated secretaries can visit the dashboard', function () {
    $user = User::factory()->create(['role' => 'secretary']);
    $this->actingAs($user);
    $version = hash('xxh128', (string) config('app.asset_url'));

    $response = $this
        ->withHeader('X-Inertia', 'true')
        ->withHeader('X-Inertia-Version', $version)
        ->get(route('dashboard'));
    $response->assertOk();
    $response->assertHeader('X-Inertia', 'true');
});

test('secretary dashboard frontend loads correctly', function () {
    $secretary = User::factory()->create(['role' => 'secretary']);

    $this->actingAs($secretary)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Workers/Dashboard')
            ->where('role', 'secretary')
            ->has('dates')
            ->has('doctors')
        );
});

test('dashboard loads with doctors list', function () {
    // Create multiple doctors
    $doctor1 = User::factory()->create(['role' => 'doctor', 'name' => 'Doctor One']);
    $doctor2 = User::factory()->create(['role' => 'doctor', 'name' => 'Doctor Two']);

    // Create workers for doctors
    DB::table('workers')->insert([
        ['user_id' => $doctor1->id, 'nss' => 'NSS001', 'address' => 'Address 1', 'dni' => '11111111A', 'phone' => 600000001, 'created_at' => now(), 'updated_at' => now()],
        ['user_id' => $doctor2->id, 'nss' => 'NSS002', 'address' => 'Address 2', 'dni' => '22222222B', 'phone' => 600000002, 'created_at' => now(), 'updated_at' => now()],
    ]);

    $secretary = User::factory()->create(['role' => 'secretary']);
    $this->actingAs($secretary);
    $version = hash('xxh128', (string) config('app.asset_url'));

    $response = $this
        ->withHeader('X-Inertia', 'true')
        ->withHeader('X-Inertia-Version', $version)
        ->get(route('dashboard'));

    $response->assertOk();
    $response->assertJsonPath('component', 'Workers/Dashboard');
    $response->assertJsonPath('props.doctors.0.name', 'Doctor One');
    $response->assertJsonPath('props.doctors.1.name', 'Doctor Two');
});

test('dashboard loads with dates list', function () {
    $doctor = User::factory()->create(['role' => 'doctor', 'name' => 'Doctor Test']);
    $doctorWorkerId = DB::table('workers')->insertGetId([
        'user_id' => $doctor->id,
        'nss' => 'NSS003',
        'address' => 'Test address',
        'dni' => '33333333C',
        'phone' => 600000003,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $patient = DB::table('patients')->insertGetId([
        'name' => 'Patient Test',
        'nts' => 'NTS001',
        'address' => 'Test address',
        'dni' => '44444444D',
        'phone' => 600000004,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $testId = DB::table('test_types')->insertGetId([
        'name' => 'Test Type',
        'time' => 30,
    ]);

    DB::table('dates')->insert([
        'patient_id' => $patient,
        'worker_id' => $doctorWorkerId,
        'test_id' => $testId,
        'date_time' => now()->addDay(),
        'time' => 30,
        'estat' => 'programada',
        'urgencia' => 'no urgent',
        'description' => 'Test appointment',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $secretary = User::factory()->create(['role' => 'secretary']);
    $this->actingAs($secretary);
    $version = hash('xxh128', (string) config('app.asset_url'));

    $response = $this
        ->withHeader('X-Inertia', 'true')
        ->withHeader('X-Inertia-Version', $version)
        ->get(route('dashboard'));

    $response->assertOk();
    $response->assertJsonPath('component', 'Workers/Dashboard');
    $response->assertJsonPath('props.dates.0.patient.name', 'Patient Test');
    $response->assertJsonPath('props.dates.0.test.name', 'Test Type');
});

test('dashboard does not load past dates', function () {
    $doctor = User::factory()->create(['role' => 'doctor']);
    $doctorWorkerId = DB::table('workers')->insertGetId([
        'user_id' => $doctor->id,
        'nss' => 'NSS004',
        'address' => 'Test address',
        'dni' => '55555555E',
        'phone' => 600000005,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $patient = DB::table('patients')->insertGetId([
        'name' => 'Patient Past',
        'nts' => 'NTS002',
        'address' => 'Test address',
        'dni' => '66666666F',
        'phone' => 600000006,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $testId = DB::table('test_types')->insertGetId([
        'name' => 'Test Type',
        'time' => 30,
    ]);

    // Insert past date
    DB::table('dates')->insert([
        'patient_id' => $patient,
        'worker_id' => $doctorWorkerId,
        'test_id' => $testId,
        'date_time' => now()->subDay(),
        'time' => 30,
        'estat' => 'completada',
        'urgencia' => 'no urgent',
        'description' => 'Past appointment',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    // Insert future date
    DB::table('dates')->insert([
        'patient_id' => $patient,
        'worker_id' => $doctorWorkerId,
        'test_id' => $testId,
        'date_time' => now()->addDay(),
        'time' => 30,
        'estat' => 'programada',
        'urgencia' => 'no urgent',
        'description' => 'Future appointment',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $secretary = User::factory()->create(['role' => 'secretary']);
    $this->actingAs($secretary);
    $version = hash('xxh128', (string) config('app.asset_url'));

    $response = $this
        ->withHeader('X-Inertia', 'true')
        ->withHeader('X-Inertia-Version', $version)
        ->get(route('dashboard'));

    $response->assertOk();
    $response->assertJsonCount(1, 'props.dates');
    $response->assertJsonPath('props.dates.0.description', 'Future appointment');
});
