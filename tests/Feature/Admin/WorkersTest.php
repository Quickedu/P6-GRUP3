<?php

use App\Models\User;
use App\Models\Worker;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// Creates a worker and its user via the admin route.
test('admin can create a worker', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $payload = [
        'name' => 'Worker Example',
        'email' => 'worker@example.com',
        'role' => 'doctor',
        'password' => 'password123',
        'nss' => 'NSS-12345',
        'address' => 'Main Street 1',
        'dni' => 'DNI-12345',
        'license_number' => 'LIC-12345',
        'phone' => '123456789',
    ];

    $this->actingAs($admin, 'admin')
        ->post(route('workers.store'), $payload)
        ->assertRedirect();

    $userId = User::query()->where('email', $payload['email'])->value('id');

    $this->assertDatabaseHas('users', [
        'id' => $userId,
        'email' => 'worker@example.com',
        'role' => 'doctor',
    ]);
    $this->assertDatabaseHas('workers', [
        'user_id' => $userId,
        'nss' => 'NSS-12345',
        'dni' => 'DNI-12345',
        'license_number' => 'LIC-12345',
        'address' => 'Main Street 1',
        'phone' => 123456789,
    ]);
});

// Updates worker and user details via the admin route.
test('admin can update a worker', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $workerUser = User::factory()->create([
        'role' => 'doctor',
        'email' => 'old-worker@example.com',
    ]);
    $worker = Worker::create([
        'user_id' => $workerUser->id,
        'nss' => 'NSS-54321',
        'address' => 'Old Street 9',
        'dni' => 'DNI-54321',
        'license_number' => 'LIC-54321',
        'phone' => 111222333,
    ]);
    $payload = [
        'email' => 'new-worker@example.com',
        'role' => 'technician',
        'address' => 'New Street 10',
        'phone' => '987654321',
    ];

    $this->actingAs($admin, 'admin')
        ->put(route('workers.update', $worker), $payload)
        ->assertRedirect();

    $worker->refresh();
    $workerUser->refresh();

    expect($worker->address)->toBe('New Street 10');
    expect($worker->phone)->toBe(987654321);
    expect($workerUser->email)->toBe('new-worker@example.com');
    expect($workerUser->role)->toBe('technician');
});

// Deletes a worker and the linked user via the admin route.
test('admin can delete a worker', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $workerUser = User::factory()->create([
        'role' => 'secretary',
        'email' => 'delete-worker@example.com',
    ]);
    $worker = Worker::create([
        'user_id' => $workerUser->id,
        'nss' => 'NSS-99999',
        'address' => 'Delete Street 3',
        'dni' => 'DNI-99999',
        'license_number' => 'LIC-99999',
        'phone' => 999888777,
    ]);

    $this->actingAs($admin, 'admin')
        ->delete(route('workers.destroy', $worker))
        ->assertRedirect();

    $this->assertDatabaseMissing('workers', ['id' => $worker->id]);
    $this->assertDatabaseMissing('users', ['id' => $workerUser->id]);
});
