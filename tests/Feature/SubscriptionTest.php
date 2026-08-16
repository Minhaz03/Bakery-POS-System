<?php

use App\Models\User;
use App\Models\Tenant;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Product;
use App\Models\Category;
use App\Models\Unit;
use App\Models\Brand;
use App\Models\Tax;

beforeEach(function () {
    $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
    // Seed standard plans if they are not already there
    if (Plan::count() === 0) {
        Plan::create([
            'id' => 1,
            'name' => 'Basic Plan',
            'price' => 999.00,
            'limit_products' => 50,
            'limit_users' => 2,
        ]);
        Plan::create([
            'id' => 2,
            'name' => 'Standard Plan',
            'price' => 1999.00,
            'limit_products' => 200,
            'limit_users' => 5,
        ]);
        Plan::create([
            'id' => 3,
            'name' => 'Enterprise Plan',
            'price' => 4999.00,
            'limit_products' => 9999,
            'limit_users' => 9999,
        ]);
    }

    // Enforce subscription check middleware for the duration of these tests
    session(['enforce_subscription_check' => true]);
});

test('expired subscription redirects to billing page', function () {
    $tenant = Tenant::create(['name' => 'Bakery Gamma']);
    $user = User::factory()->create(['tenant_id' => $tenant->id]);

    // Create an expired subscription
    Subscription::create([
        'tenant_id' => $tenant->id,
        'plan_id' => 1, // Basic
        'starts_at' => now()->subMonth(),
        'ends_at' => now()->subDay(),
        'status' => 'active', // but ends_at has passed
    ]);

    // Attempt to access dashboard - should redirect to billing
    $response = $this->actingAs($user)->get(route('dashboard'));
    $response->assertRedirect(route('dashboard.billing'));
    $response->assertSessionHas('warning');

    // Attempt to access billing - should load fine (no infinite loop)
    $response = $this->actingAs($user)->get(route('dashboard.billing'));
    $response->assertOk();
});

test('subscribing to a plan grants access and enforces limits', function () {
    $tenant = Tenant::create(['name' => 'Bakery Delta']);
    $user = User::factory()->create(['tenant_id' => $tenant->id]);
    $user->assignRole('Super Admin');

    // 1. Initially no active subscription
    $response = $this->actingAs($user)->get(route('dashboard'));
    $response->assertRedirect(route('dashboard.billing'));

    // 2. Subscribe to Basic Plan (Limit products = 50, limit users = 2)
    Subscription::create([
        'tenant_id' => $tenant->id,
        'plan_id' => 1, // Basic Plan
        'starts_at' => now(),
        'ends_at' => now()->addMonth(),
        'status' => 'active',
    ]);

    // Now has access
    $response = $this->actingAs($user)->get(route('dashboard'));
    $response->assertOk();

    // 3. Test limits: Basic Plan allows 2 users. We already have 1.
    // Create 2nd user (success)
    $user2 = User::factory()->create(['tenant_id' => $tenant->id]);

    // Attempt to register a 3rd user through UserController (should fail)
    $response = $this->actingAs($user)->post(route('dashboard.users.store'), [
        'name' => 'User Three',
        'email' => 'three@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);
    $response->assertRedirect();
    $response->assertSessionHas('error');
    expect(User::where('tenant_id', $tenant->id)->count())->toBe(2);
});
