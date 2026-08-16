<?php

use App\Models\User;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Unit;
use App\Models\Brand;
use App\Models\Tax;
use Database\Seeders\RolesAndPermissionsSeeder;
use Database\Seeders\SettingsSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
    $this->seed(SettingsSeeder::class);
    Storage::fake('public');
});

test('authenticated user can perform unit operations', function () {
    $user = User::factory()->create();
    $user->assignRole('Super Admin');

    // 1. Create Unit
    $response = $this->actingAs($user)->post(route('dashboard.units.store'), [
        'name' => 'Testing Unit',
        'short_name' => 'tst',
        'operator' => '*',
        'conversion_rate' => 1,
    ]);
    $response->assertSessionHasNoErrors();
    $this->assertDatabaseHas('units', ['name' => 'Testing Unit', 'short_name' => 'tst']);

    // 2. AJAX Create Unit
    $response = $this->actingAs($user)->postJson(route('dashboard.units.store'), [
        'name' => 'Testing Unit 2',
        'short_name' => 'tst2',
        'operator' => '*',
        'conversion_rate' => 1,
    ]);
    $response->assertJsonPath('success', true);
    $this->assertDatabaseHas('units', ['name' => 'Testing Unit 2']);

    // 3. Delete Unit
    $unit = Unit::where('name', 'Testing Unit')->first();
    $response = $this->actingAs($user)->delete(route('dashboard.units.destroy', $unit));
    $this->assertDatabaseMissing('units', ['id' => $unit->id]);
});

test('authenticated user can perform brand operations', function () {
    $user = User::factory()->create();
    $user->assignRole('Super Admin');
    $logoFile = UploadedFile::fake()->image('brand_logo.png');

    // 1. Create Brand with Logo
    $response = $this->actingAs($user)->post(route('dashboard.brands.store'), [
        'name' => 'Baking Master Brand',
        'description' => 'Test description',
        'logo' => $logoFile,
    ]);
    $response->assertSessionHasNoErrors();
    $this->assertDatabaseHas('brands', ['name' => 'Baking Master Brand']);

    // Check file was stored
    $brand = Brand::where('name', 'Baking Master Brand')->first();
    Storage::disk('public')->assertExists($brand->logo);

    // 2. Delete Brand
    $response = $this->actingAs($user)->delete(route('dashboard.brands.destroy', $brand));
    $this->assertDatabaseMissing('brands', ['id' => $brand->id]);
    Storage::disk('public')->assertMissing($brand->logo);
});

test('authenticated user can perform category CRUD', function () {
    $user = User::factory()->create();
    $user->assignRole('Super Admin');
    $imageFile = UploadedFile::fake()->image('cat_image.jpg');

    // 1. Create Category
    $response = $this->actingAs($user)->post(route('dashboard.categories.store'), [
        'name' => 'New Bread Category',
        'description' => 'Soft sourdough breads',
        'image' => $imageFile,
        'is_active' => '1',
    ]);
    $response->assertSessionHasNoErrors();
    $this->assertDatabaseHas('categories', ['name' => 'New Bread Category', 'is_active' => true]);

    $category = Category::where('name', 'New Bread Category')->first();
    Storage::disk('public')->assertExists($category->image);

    // 2. Edit Category Page
    $response = $this->actingAs($user)->get(route('dashboard.categories.edit', $category));
    $response->assertOk();

    // 3. Update Category
    $response = $this->actingAs($user)->put(route('dashboard.categories.update', $category), [
        'name' => 'Updated Bread Category',
        'description' => 'Sourdough & Baguettes',
        'is_active' => '1',
    ]);
    $response->assertSessionHasNoErrors();
    $this->assertDatabaseHas('categories', ['name' => 'Updated Bread Category', 'description' => 'Sourdough & Baguettes']);

    // 4. Delete Category (Soft Deletes)
    $response = $this->actingAs($user)->delete(route('dashboard.categories.destroy', $category));
    $this->assertSoftDeleted($category);
});

test('authenticated user can perform supplier CRUD', function () {
    $user = User::factory()->create();
    $user->assignRole('Super Admin');

    // 1. Create Supplier
    $response = $this->actingAs($user)->post(route('dashboard.suppliers.store'), [
        'name' => 'Flour Merchant Co.',
        'contact_person' => 'Merchant John',
        'phone' => '1122334455',
        'email' => 'merchant@example.com',
        'city' => 'Minneapolis',
        'opening_balance' => 1500.50,
        'is_active' => '1',
    ]);
    $response->assertSessionHasNoErrors();
    $this->assertDatabaseHas('suppliers', [
        'name' => 'Flour Merchant Co.',
        'opening_balance' => 1500.50,
        'current_balance' => 1500.50,
        'is_active' => true,
    ]);

    $supplier = Supplier::where('name', 'Flour Merchant Co.')->first();

    // 2. Edit Page
    $response = $this->actingAs($user)->get(route('dashboard.suppliers.edit', $supplier));
    $response->assertOk();

    // 3. Update Supplier
    $response = $this->actingAs($user)->put(route('dashboard.suppliers.update', $supplier), [
        'name' => 'Flour Merchant Co.',
        'contact_person' => 'Merchant Jane',
        'phone' => '9988776655',
        'email' => 'merchant@example.com',
        'city' => 'Minneapolis',
        'opening_balance' => 1000.00, // Reduced opening balance by 500.50
        'is_active' => '1',
    ]);
    $response->assertSessionHasNoErrors();
    $this->assertDatabaseHas('suppliers', [
        'contact_person' => 'Merchant Jane',
        'opening_balance' => 1000.00,
        'current_balance' => 1000.00,
    ]);

    // 4. Delete Supplier
    $response = $this->actingAs($user)->delete(route('dashboard.suppliers.destroy', $supplier));
    $this->assertSoftDeleted($supplier);
});

test('authenticated user can perform customer CRUD and Walk-in deletion protection', function () {
    $user = User::factory()->create();
    $user->assignRole('Super Admin');

    // Seed Walk-in Customer
    $walkin = Customer::create([
        'name' => 'Walk-in Customer',
        'phone' => '0000000000',
        'email' => 'walkin@example.com',
        'loyalty_points' => 0,
        'total_spent' => 0.00,
        'is_active' => true,
    ]);

    // 1. Create Customer
    $response = $this->actingAs($user)->post(route('dashboard.customers.store'), [
        'name' => 'Regular Customer A',
        'phone' => '1234567890',
        'email' => 'customer.a@example.com',
        'loyalty_points' => 15,
        'total_spent' => 200.00,
        'is_active' => '1',
    ]);
    $response->assertSessionHasNoErrors();
    $this->assertDatabaseHas('customers', ['name' => 'Regular Customer A', 'phone' => '1234567890']);

    $customer = Customer::where('phone', '1234567890')->first();

    // 2. Update Customer
    $response = $this->actingAs($user)->put(route('dashboard.customers.update', $customer), [
        'name' => 'Regular Customer A Updated',
        'phone' => '1234567890',
        'email' => 'customer.a@example.com',
        'loyalty_points' => 25,
        'total_spent' => 350.00,
        'is_active' => '1',
    ]);
    $response->assertSessionHasNoErrors();
    $this->assertDatabaseHas('customers', ['name' => 'Regular Customer A Updated', 'loyalty_points' => 25]);

    // 3. Delete Customer (Soft Deletes)
    $response = $this->actingAs($user)->delete(route('dashboard.customers.destroy', $customer));
    $this->assertSoftDeleted($customer);

    // 4. Try to delete Walk-in customer (should fail)
    $response = $this->actingAs($user)->delete(route('dashboard.customers.destroy', $walkin));
    $response->assertSessionHas('error');
    $this->assertDatabaseHas('customers', ['phone' => '0000000000', 'deleted_at' => null]);
});

test('authenticated user can perform product CRUD', function () {
    $user = User::factory()->create();
    $user->assignRole('Super Admin');

    // Seed dependencies
    $category = Category::create(['name' => 'Cakes']);
    $brand = Brand::create(['name' => 'Puratos']);
    $unit = Unit::create(['name' => 'Piece', 'short_name' => 'pcs']);
    $tax = Tax::create(['name' => 'VAT 5%', 'rate' => 5.00]);

    $imageFile = UploadedFile::fake()->image('cake.png');

    // 1. Create Product
    $response = $this->actingAs($user)->post(route('dashboard.products.store'), [
        'name' => 'Strawberry Shortcake',
        'sku' => '', // blank to auto-generate
        'barcode' => '', // blank to auto-generate
        'category_id' => $category->id,
        'brand_id' => $brand->id,
        'unit_id' => $unit->id,
        'tax_id' => $tax->id,
        'cost_price' => 15.00,
        'sale_price' => 45.00,
        'mrp_price' => 50.00,
        'stock_qty' => 10,
        'alert_qty' => 2,
        'reorder_qty' => 5,
        'is_active' => '1',
        'is_pos_enabled' => '1',
        'is_bakery_item' => '1',
        'image' => $imageFile,
        'product_type' => 'finished_product',
    ]);
    $response->assertSessionHasNoErrors();
    
    $product = Product::where('name', 'Strawberry Shortcake')->first();
    expect($product->sku)->not->toBeEmpty();
    expect($product->barcode)->not->toBeEmpty();
    Storage::disk('public')->assertExists($product->image);

    // 2. Edit Page
    $response = $this->actingAs($user)->get(route('dashboard.products.edit', $product));
    $response->assertOk();

    // 3. Update Product
    $response = $this->actingAs($user)->put(route('dashboard.products.update', $product), [
        'name' => 'Strawberry Shortcake Special',
        'cost_price' => 18.00,
        'sale_price' => 48.00,
        'mrp_price' => 55.00,
        'stock_qty' => 15,
        'alert_qty' => 2,
        'reorder_qty' => 5,
        'is_active' => '1',
        'is_pos_enabled' => '1',
        'product_type' => 'finished_product',
    ]);
    $response->assertSessionHasNoErrors();
    $this->assertDatabaseHas('products', [
        'name' => 'Strawberry Shortcake Special',
        'cost_price' => 18.00,
        'stock_qty' => 15,
    ]);

    // 4. Delete Product (Soft Deletes)
    $response = $this->actingAs($user)->delete(route('dashboard.products.destroy', $product));
    $this->assertSoftDeleted($product);
});
