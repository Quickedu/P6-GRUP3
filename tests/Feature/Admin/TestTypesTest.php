<?php

use App\Models\Test as TestModel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// Creates a new test type via the admin route.
test('admin can create a test type', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $payload = [
        'name' => 'Test A',
        'time' => 20,
    ];

    $this->actingAs($admin, 'admin')
        ->post(route('tests.store'), $payload)
        ->assertRedirect();

    $this->assertDatabaseHas('test_types', $payload);
});

// Updates an existing test type via the admin route.
test('admin can update a test type', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $testType = TestModel::create([
        'name' => 'Test Old',
        'time' => 30,
    ]);
    $payload = [
        'name' => 'Test Updated',
        'time' => 45,
    ];

    $this->actingAs($admin, 'admin')
        ->put(route('tests.update', $testType), $payload)
        ->assertRedirect();

    $this->assertDatabaseHas('test_types', [
        'id' => $testType->id,
        'name' => 'Test Updated',
        'time' => 45,
    ]);
});

// Deletes a test type via the admin route.
test('admin can delete a test type', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $testType = TestModel::create([
        'name' => 'Test Delete',
        'time' => 35,
    ]);

    $this->actingAs($admin, 'admin')
        ->delete(route('tests.destroy', $testType))
        ->assertRedirect();

    $this->assertDatabaseMissing('test_types', ['id' => $testType->id]);
});
