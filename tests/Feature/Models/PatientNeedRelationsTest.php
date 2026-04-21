<?php

use App\Models\Need;
use App\Models\Patient;
use App\Models\PatientNeed;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('patient patient-need and need relationships work as expected', function () {
    $patient = Patient::create([
        'name' => 'Test Patient',
        'nts' => 'NTS-001',
        'address' => 'Test Address',
        'dni' => '11111111A',
        'phone' => 123456789,
    ]);

    $need = Need::create([
        'name' => 'Wheelchair access',
        'time' => 15,
    ]);

    $patientNeed = PatientNeed::create([
        'patient_id' => $patient->id,
        'need_id' => $need->id,
    ]);

    expect($patient->fresh()->patientNeeds)->toHaveCount(1)
        ->and($patient->fresh()->patientNeeds->first()->is($patientNeed))->toBeTrue()
        ->and($patientNeed->need->is($need))->toBeTrue()
        ->and($patientNeed->patient->is($patient))->toBeTrue()
        ->and($patient->needs)->toHaveCount(1)
        ->and($patient->needs->first()->is($need))->toBeTrue()
        ->and($need->patientNeeds)->toHaveCount(1)
        ->and($need->patientNeeds->first()->is($patientNeed))->toBeTrue();
});
