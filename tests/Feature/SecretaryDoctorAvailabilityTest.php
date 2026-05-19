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
        '8:40 - 9:30',
        '10:00 - 12:00',
        '13:10 - 15:00',
    ]);
    $response->assertJsonPath('data.start_times', [
        '8:40',
        '8:45',
        '10:00',
        '10:15',
        '10:30',
        '10:45',
        '11:00',
        '11:15',
        '13:10',
        '13:15',
        '13:30',
        '13:45',
        '14:00',
        '14:15',
    ]);
});

test('it blocks start times inside an existing appointment on the selected day', function () {
    $doctorUserId = DB::table('users')->insertGetId([
        'name' => 'Doctor Busy',
        'email' => 'doctor.busy@example.com',
        'role' => 'doctor',
        'password' => bcrypt('password'),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $doctorWorkerId = DB::table('workers')->insertGetId([
        'user_id' => $doctorUserId,
        'nss' => 'NSS900002',
        'address' => 'Test address',
        'dni' => '90000002A',
        'phone' => 600100003,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $secretaryUser = User::factory()->create([
        'role' => 'secretary',
    ]);

    DB::table('patients')->insert([
        'id' => 1,
        'name' => 'Patient Busy',
        'nts' => 'NTS900002',
        'address' => 'Test address',
        'dni' => '10000002Z',
        'phone' => 600100004,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    DB::table('test_types')->insert([
        'id' => 1,
        'name' => 'Test Type',
        'time' => 5,
    ]);

    DB::table('dates')->insert([
        'patient_id' => 1,
        'worker_id' => $doctorWorkerId,
        'test_id' => 1,
        'date_time' => '2026-04-22 09:30:00',
        'time' => 50,
        'estat' => 'programada',
        'urgencia' => 'no urgent',
        'description' => 'Control de prova',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $response = $this
        ->actingAs($secretaryUser, 'admin')
        ->getJson(route('ajax-doctor', ['id' => $doctorUserId, 'date' => '2026-04-22', 'time' => 5]));

    $response->assertSuccessful();
    $response->assertJsonPath('status', 'success');
    $response->assertJsonPath('data.required_minutes', 15);
    $response->assertJsonPath('data.slots', [
        '8:00 - 9:30',
        '10:30 - 15:00',
    ]);
    $response->assertJsonPath('data.start_times', [
        '8:00',
        '8:15',
        '8:30',
        '8:45',
        '9:00',
        '9:15',
        '10:30',
        '10:45',
        '11:00',
        '11:15',
        '11:30',
        '11:45',
        '12:00',
        '12:15',
        '12:30',
        '12:45',
        '13:00',
        '13:15',
        '13:30',
        '13:45',
        '14:00',
        '14:15',
        '14:30',
        '14:45',
    ]);
});

test('it provides worker mapping for doctors on new appointment page', function () {
    $doctorUserId = DB::table('users')->insertGetId([
        'name' => 'Doctor Mapping',
        'email' => 'doctor.mapping@example.com',
        'role' => 'doctor',
        'password' => bcrypt('password'),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $doctorWorkerId = DB::table('workers')->insertGetId([
        'user_id' => $doctorUserId,
        'nss' => 'NSS900003',
        'address' => 'Test address',
        'dni' => '90000003A',
        'phone' => 600100005,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $secretaryUser = User::factory()->create([
        'role' => 'secretary',
    ]);

    $response = $this
        ->actingAs($secretaryUser, 'admin')
        ->withHeader('X-Inertia', 'true')
        ->withHeader('X-Inertia-Version', hash('xxh128', (string) config('app.asset_url')))
        ->get(route('nova-cita'));

    $response->assertSuccessful();
    $response->assertHeader('X-Inertia', 'true');
    $response->assertJsonPath('component', 'Workers/NewDate');
    $response->assertJsonPath('props.doctors.0.id', $doctorUserId);
    $response->assertJsonPath('props.doctors.0.worker_id', $doctorWorkerId);
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

test('it returns patient dates for an authenticated secretary', function () {
    $doctorUserId = DB::table('users')->insertGetId([
        'name' => 'Doctor Patient Dates',
        'email' => 'doctor.patient.dates@example.com',
        'role' => 'doctor',
        'password' => bcrypt('password'),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $doctorWorkerId = DB::table('workers')->insertGetId([
        'user_id' => $doctorUserId,
        'nss' => 'NSS900004',
        'address' => 'Test address',
        'dni' => '90000004A',
        'phone' => 600100006,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $secretaryUser = User::factory()->create([
        'role' => 'secretary',
    ]);

    $patientId = DB::table('patients')->insertGetId([
        'name' => 'Patient Dates',
        'nts' => 'ABCD1234567890',
        'address' => 'Test address',
        'dni' => '10000004Z',
        'phone' => 600100007,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $testId = DB::table('test_types')->insertGetId([
        'name' => 'Analitica',
        'time' => 20,
    ]);

    DB::table('dates')->insert([
        'patient_id' => $patientId,
        'worker_id' => $doctorWorkerId,
        'test_id' => $testId,
        'date_time' => now()->addWeek(),
        'time' => 20,
        'estat' => 'programada',
        'urgencia' => 'no urgent',
        'description' => 'Control de prova',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $response = $this
        ->actingAs($secretaryUser, 'admin')
        ->getJson(route('filter-patient-dates', ['nts' => 'ABCD1234567890']));

    $response->assertSuccessful();
    $response->assertJsonPath('status', 'success');
    $response->assertJsonCount(1, 'data');
    $response->assertJsonPath('data.0.patient.name', 'Patient Dates');
});

test('it filters dates by doctor id and date with today as the default date', function () {
    $firstDoctorUserId = DB::table('users')->insertGetId([
        'name' => 'Doctor Filter One',
        'email' => 'doctor.filter.one@example.com',
        'role' => 'doctor',
        'password' => bcrypt('password'),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $firstDoctorWorkerId = DB::table('workers')->insertGetId([
        'user_id' => $firstDoctorUserId,
        'nss' => 'NSS900005',
        'address' => 'Test address',
        'dni' => '90000005A',
        'phone' => 600100008,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $secondDoctorUserId = DB::table('users')->insertGetId([
        'name' => 'Doctor Filter Two',
        'email' => 'doctor.filter.two@example.com',
        'role' => 'doctor',
        'password' => bcrypt('password'),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $secondDoctorWorkerId = DB::table('workers')->insertGetId([
        'user_id' => $secondDoctorUserId,
        'nss' => 'NSS900006',
        'address' => 'Test address',
        'dni' => '90000006A',
        'phone' => 600100009,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $secretaryUser = User::factory()->create([
        'role' => 'secretary',
    ]);

    $patientId = DB::table('patients')->insertGetId([
        'name' => 'Filter Patient',
        'nts' => 'WXYZ1234567890',
        'address' => 'Test address',
        'dni' => '10000005Z',
        'phone' => 600100010,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $testId = DB::table('test_types')->insertGetId([
        'name' => 'Ecografia',
        'time' => 20,
    ]);

    DB::table('dates')->insert([
        [
            'patient_id' => $patientId,
            'worker_id' => $firstDoctorWorkerId,
            'test_id' => $testId,
            'date_time' => today()->setTime(9, 0),
            'time' => 20,
            'estat' => 'programada',
            'urgencia' => 'no urgent',
            'description' => 'Today first doctor',
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'patient_id' => $patientId,
            'worker_id' => $secondDoctorWorkerId,
            'test_id' => $testId,
            'date_time' => today()->setTime(10, 0),
            'time' => 20,
            'estat' => 'programada',
            'urgencia' => 'no urgent',
            'description' => 'Today second doctor',
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'patient_id' => $patientId,
            'worker_id' => $firstDoctorWorkerId,
            'test_id' => $testId,
            'date_time' => today()->addDay()->setTime(11, 0),
            'time' => 20,
            'estat' => 'programada',
            'urgencia' => 'no urgent',
            'description' => 'Tomorrow first doctor',
            'created_at' => now(),
            'updated_at' => now(),
        ],
    ]);

    $defaultDateResponse = $this
        ->actingAs($secretaryUser, 'admin')
        ->getJson(route('filter-dates', ['doctor_id' => $firstDoctorWorkerId]));

    $defaultDateResponse->assertSuccessful();
    $defaultDateResponse->assertJsonPath('status', 'success');
    $defaultDateResponse->assertJsonCount(2, 'data');
    $defaultDateResponse->assertJsonPath('data.0.worker_id', $firstDoctorWorkerId);
    $defaultDateResponse->assertJsonPath('data.0.description', 'Today first doctor');

    $dateOnlyResponse = $this
        ->actingAs($secretaryUser, 'admin')
        ->getJson(route('filter-dates', ['date' => today()->addDay()->toDateString()]));

    $dateOnlyResponse->assertSuccessful();
    $dateOnlyResponse->assertJsonPath('status', 'success');
    $dateOnlyResponse->assertJsonCount(1, 'data');
    $dateOnlyResponse->assertJsonPath('data.0.description', 'Tomorrow first doctor');
});
