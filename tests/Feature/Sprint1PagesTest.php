<?php

use App\Models\User;
use App\Models\Setting;
use Database\Seeders\RolesAndPermissionsSeeder;
use Database\Seeders\SettingsSeeder;
use Illuminate\Support\Facades\DB;

beforeEach(function () {
    // Seed roles/permissions and default settings
    $this->seed(RolesAndPermissionsSeeder::class);
    $this->seed(SettingsSeeder::class);
});

test('guest users are redirected to login from dashboard', function () {
    $response = $this->get('/dashboard');
    $response->assertRedirect(route('login'));
});

test('guest users are redirected to login from admin routes', function () {
    $response = $this->get(route('dashboard.settings.index'));
    $response->assertRedirect(route('login'));

    $response = $this->get(route('dashboard.modules.index'));
    $response->assertRedirect(route('login'));
});

test('regular logged-in users without permissions cannot access modules but can access settings', function () {
    $user = User::factory()->create(); // No roles assigned

    $response = $this->actingAs($user)->get(route('dashboard.settings.index'));
    $response->assertStatus(200);

    $response = $this->actingAs($user)->get(route('dashboard.modules.index'));
    $response->assertStatus(403);
});

test('dashboard page loads for authenticated users', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/dashboard');
    $response->assertOk();
});

test('admin with appropriate roles can access settings and update them', function () {
    $admin = User::factory()->create();
    $admin->assignRole('Super Admin');

    // Test GET settings page
    $response = $this->actingAs($admin)->get(route('dashboard.settings.index'));
    $response->assertOk();
    $response->assertSee('Settings');

    // Test POST settings update
    $response = $this->actingAs($admin)->post(route('dashboard.settings.update'), [
        'business_name'    => 'Test POS System',
        'business_address' => '123 Test Street',
        'business_phone'   => '123456789',
        'business_email'   => 'test@example.com',
        'currency_symbol'  => '$',
        'currency_code'    => 'USD',
    ]);

    $response->assertSessionHasNoErrors();
    $response->assertRedirect();
    
    expect(Setting::get('business_name'))->toBe('Test POS System');
    expect(Setting::get('business_address'))->toBe('123 Test Street');
});

test('admin with appropriate roles can access modules and toggle infrastructure modules', function () {
    $admin = User::factory()->create();
    $admin->assignRole('Super Admin');

    // Test GET modules page
    $response = $this->actingAs($admin)->get(route('dashboard.modules.index'));
    $response->assertOk();
    $response->assertSee('Module');

    // Test POST toggle infrastructure module on
    $response = $this->actingAs($admin)->post(route('dashboard.modules.toggle-infrastructure', ['module' => 'warehouse']), [
        'enabled' => 1,
    ]);
    $response->assertSessionHasNoErrors();
    $response->assertRedirect();

    expect(Setting::get('module_warehouse_enabled'))->toBe(true);

    // Test POST toggle infrastructure module off
    $response = $this->actingAs($admin)->post(route('dashboard.modules.toggle-infrastructure', ['module' => 'warehouse']), [
        'enabled' => 0,
    ]);
    $response->assertSessionHasNoErrors();
    $response->assertRedirect();

    expect(Setting::get('module_warehouse_enabled'))->toBe(false);
});

test('admin with appropriate roles can change active business type module', function () {
    $admin = User::factory()->create();
    $admin->assignRole('Super Admin');

    // Default business type is bakery
    expect(Setting::get('active_business_type'))->toBe('bakery');

    // Test POST set business type to bakery (available in config)
    $response = $this->actingAs($admin)->post(route('dashboard.modules.set-business-type'), [
        'business_type' => 'bakery',
    ]);
    $response->assertSessionHasNoErrors();
    $response->assertRedirect();

    expect(Setting::get('active_business_type'))->toBe('bakery');
});
