<?php

use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Database\Seeders\SettingsSeeder;

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
    $this->seed(SettingsSeeder::class);
});

test('guest users are redirected from subpages', function () {
    $subpages = [
        'dashboard.products',
        'dashboard.categories',
        'dashboard.stock-ledger',
        'dashboard.suppliers',
        'dashboard.purchases',
        'dashboard.pos-terminal',
        'dashboard.sales',
        'dashboard.customers',
        'dashboard.recipes',
        'dashboard.production',
        'dashboard.custom-orders',
        'dashboard.analytics',
    ];

    foreach ($subpages as $route) {
        $response = $this->get(route($route));
        $response->assertRedirect(route('login'));
    }
});

test('authenticated admin user can access all 12 dashboard subpages', function () {
    $user = User::factory()->create();
    $user->assignRole('Super Admin');

    $subpages = [
        'dashboard.products',
        'dashboard.categories',
        'dashboard.stock-ledger',
        'dashboard.suppliers',
        'dashboard.purchases',
        'dashboard.pos-terminal',
        'dashboard.sales',
        'dashboard.customers',
        'dashboard.recipes',
        'dashboard.production',
        'dashboard.custom-orders',
        'dashboard.analytics',
    ];

    foreach ($subpages as $route) {
        $response = $this->actingAs($user)->get(route($route));
        $response->assertOk();
    }
});

test('regular logged-in users without permissions are denied access to restricted subpages', function () {
    $user = User::factory()->create(); // No roles or permissions assigned

    $subpages = [
        'dashboard.products',
        'dashboard.categories',
        'dashboard.stock-ledger',
        'dashboard.suppliers',
        'dashboard.purchases',
        'dashboard.pos-terminal',
        'dashboard.sales',
        'dashboard.customers',
        'dashboard.recipes',
        'dashboard.production',
        'dashboard.custom-orders',
        'dashboard.analytics',
    ];

    foreach ($subpages as $route) {
        $response = $this->actingAs($user)->get(route($route));
        $response->assertStatus(403);
    }
});
