<?php

use App\Models\User;
use App\Models\Tenant;
use App\Models\Product;
use App\Models\Category;
use App\Models\Unit;
use App\Models\Brand;
use App\Models\Tax;

test('data is automatically scoped by tenant and users cannot access other tenant data', function () {
    // 1. Create two tenants
    $tenant1 = Tenant::create(['name' => 'Bakery Alpha']);
    $tenant2 = Tenant::create(['name' => 'Bakery Beta']);

    // 2. Create users for both tenants
    $user1 = User::factory()->create(['tenant_id' => $tenant1->id]);
    $user2 = User::factory()->create(['tenant_id' => $tenant2->id]);

    // 3. Create sharing infrastructure dependencies
    session(['tenant_id' => $tenant1->id]);
    $tax1 = Tax::create(['name' => 'VAT 1', 'rate' => 5]);
    $unit1 = Unit::create(['name' => 'Kilo 1', 'short_name' => 'k1', 'operator' => '*', 'conversion_rate' => 1]);
    $category1 = Category::create(['name' => 'Bread 1']);
    $brand1 = Brand::create(['name' => 'Brand 1']);
    $product1 = Product::create([
        'name' => 'Alpha Special Bread',
        'sku' => 'ALPHA-BRD',
        'category_id' => $category1->id,
        'unit_id' => $unit1->id,
        'tax_id' => $tax1->id,
        'product_type' => 'finished_good',
        'cost_price' => 10.00,
        'sale_price' => 15.00,
    ]);

    session(['tenant_id' => $tenant2->id]);
    $tax2 = Tax::create(['name' => 'VAT 2', 'rate' => 5]);
    $unit2 = Unit::create(['name' => 'Kilo 2', 'short_name' => 'k2', 'operator' => '*', 'conversion_rate' => 1]);
    $category2 = Category::create(['name' => 'Bread 2']);
    $brand2 = Brand::create(['name' => 'Brand 2']);
    $product2 = Product::create([
        'name' => 'Beta Special Cake',
        'sku' => 'BETA-CAK',
        'category_id' => $category2->id,
        'unit_id' => $unit2->id,
        'tax_id' => $tax2->id,
        'product_type' => 'finished_good',
        'cost_price' => 20.00,
        'sale_price' => 30.00,
    ]);

    // Clear session to simulate normal HTTP request / authentications
    session()->forget('tenant_id');

    // 4. Assert that User 1 can see Product 1 but NOT Product 2
    $this->actingAs($user1);
    
    $user1Products = Product::all();
    expect($user1Products)->toHaveCount(1);
    expect($user1Products->first()->name)->toBe('Alpha Special Bread');

    // 5. Assert that User 2 can see Product 2 but NOT Product 1
    $this->actingAs($user2);
    
    $user2Products = Product::all();
    expect($user2Products)->toHaveCount(1);
    expect($user2Products->first()->name)->toBe('Beta Special Cake');

    // 6. Test automatic tenant_id insertion on resource creation
    $this->actingAs($user1);
    $response = $this->post(route('dashboard.categories.store'), [
        'name' => 'Alpha Extra Category',
        'is_active' => 1,
    ]);
    
    // Check that category belongs to Tenant 1 automatically
    $newCategory = Category::withoutGlobalScopes()->where('name', 'Alpha Extra Category')->first();
    expect($newCategory)->not->toBeNull();
    expect($newCategory->tenant_id)->toBe($tenant1->id);
});
