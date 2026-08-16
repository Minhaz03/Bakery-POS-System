<?php

use App\Models\User;

it('shows the welcome page for guest users', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
    $response->assertSee('BakeryPOS');
});

it('redirects authenticated users to the dashboard', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/');

    $response->assertRedirect(route('dashboard'));
});

