<?php

use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Database\Seeders\SettingsSeeder;

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
    $this->seed(SettingsSeeder::class);
});

test('unauthorized mutation request redirects back with custom permission error message', function () {
    $user = User::factory()->create(); // User with no roles or permissions

    $response = $this->actingAs($user)->post(route('dashboard.products.store'), [
        'name' => 'Unauthorized Product',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('error', 'You do not have sufficient permissions to perform this action.');
});

test('unauthorized GET request renders custom 403 page with permission error message', function () {
    $user = User::factory()->create(); // User with no roles or permissions

    $response = $this->actingAs($user)->get(route('dashboard.products'));

    $response->assertStatus(403);
    $response->assertSee('You do not have sufficient permissions');
});
