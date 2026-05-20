<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('guests are redirected to the login page', function () {
    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('login'));
});

test('authenticated users can visit the dashboard', function () {
    $user = User::factory()->create();
    $this->actingAs($user);
    $version = hash('xxh128', (string) config('app.asset_url'));

    $response = $this
        ->withHeader('X-Inertia', 'true')
        ->withHeader('X-Inertia-Version', $version)
        ->get(route('dashboard'));
    $response->assertOk();
});
