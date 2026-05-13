<?php

use App\Actions\Workers\Secretary\GetPatientAction;
use App\Models\Patient;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('it returns an error when the patient is missing', function () {
    $action = new GetPatientAction;

    $response = $action->handle('NTS-NOT-FOUND');

    expect($response['status'])->toBe('error')
        ->and($response['available'])->toBeFalse()
        ->and($response['data'])->toBe([]);
});

test('it returns patient data when found', function () {
    $patient = Patient::create([
        'name' => 'Patient Test',
        'nts' => 'NTS-123',
        'address' => 'Test Address',
        'dni' => '12345678A',
        'phone' => 600000000,
        'email' => 'patient.test@example.com',
        'birth_date' => '1990-01-01',
    ]);

    $action = new GetPatientAction;

    $response = $action->handle($patient->nts);

    expect($response['status'])->toBe('success')
        ->and($response['available'])->toBeTrue()
        ->and($response['data'])->toBeInstanceOf(Patient::class)
        ->and($response['data']->is($patient))->toBeTrue();
});
