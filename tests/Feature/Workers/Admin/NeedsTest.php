<?php

use App\Models\Need;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// Creates a new need via the admin route.
test('admin can create a need', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $payload = [
        'name' => 'Need A',
        'time' => 10,
    ];

    $this->actingAs($admin, 'admin')
        ->post(route('needs.store'), $payload)
        ->assertRedirect();

    $this->assertDatabaseHas('needs', $payload);
});

// Updates an existing need via the admin route.
test('admin can update a need', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $need = Need::create([
        'name' => 'Need Old',
        'time' => 5,
    ]);
    $payload = [
        'name' => 'Need Updated',
        'time' => 25,
    ];

    $this->actingAs($admin, 'admin')
        ->put(route('needs.update', $need), $payload)
        ->assertRedirect();

    $this->assertDatabaseHas('needs', [
        'id' => $need->id,
        'name' => 'Need Updated',
        'time' => 25,
    ]);
});

// Deletes a need via the admin route.
test('admin can delete a need', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $need = Need::create([
        'name' => 'Need Delete',
        'time' => 15,
    ]);

    $this->actingAs($admin, 'admin')
        ->delete(route('needs.destroy', $need))
        ->assertRedirect();

    $this->assertDatabaseMissing('needs', ['id' => $need->id]);
});
