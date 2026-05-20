<?php

use App\Models\Patient;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('secretary can update patient with valid phone', function () {
    $secretary = User::factory()->create([
        'role' => 'secretary',
    ]);

    $patient = Patient::factory()->create([
        'phone' => '123456789',
        'email' => 'old@mail.com',
        'address' => 'Old address',
    ]);

    $payload = [
        'phone' => '987654321',
        'email' => 'new@mail.com',
        'address' => 'New address street 123',
    ];

    $this->actingAs($secretary, 'admin')
        ->post("/patients/{$patient->id}", $payload)
        ->assertRedirect();

    $this->assertDatabaseHas('patients', [
        'id' => $patient->id,
        'phone' => '987654321',
        'email' => 'new@mail.com',
        'address' => 'New address street 123',
    ]);
});