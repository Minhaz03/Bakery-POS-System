<?php

use App\Models\User;
use App\Models\Product;
use App\Models\StockLedger;
use Database\Seeders\RolesAndPermissionsSeeder;
use Database\Seeders\SettingsSeeder;
use Database\Seeders\UnitSeeder;
use Database\Seeders\BrandSeeder;
use Database\Seeders\TaxSeeder;
use Database\Seeders\CategorySeeder;
use Database\Seeders\ProductSeeder;

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
    $this->seed(SettingsSeeder::class);
    $this->seed(UnitSeeder::class);
    $this->seed(BrandSeeder::class);
    $this->seed(TaxSeeder::class);
    $this->seed(CategorySeeder::class);
    $this->seed(ProductSeeder::class);
});

test('user can view stock ledger page and seeded entries', function () {
    $user = User::factory()->create();
    $user->assignRole('Super Admin');
    
    // Seed some movements
    $product = Product::first();
    StockLedger::create([
        'product_id' => $product->id,
        'type' => 'Production (+)',
        'quantity' => 30,
        'user_id' => $user->id,
        'notes' => 'Test production notes'
    ]);

    $response = $this->actingAs($user)->get(route('dashboard.stock-ledger'));

    $response->assertOk();
    $response->assertSee('Stock Ledger');
    $response->assertSee($product->name);
    $response->assertSee('Production (+)');
    $response->assertSee('Test production notes');
});

test('user can perform a stock adjustment', function () {
    $user = User::factory()->create();
    $user->assignRole('Super Admin');
    $product = Product::first();
    $initialStock = (float) $product->stock_qty;

    $response = $this->actingAs($user)->post(route('dashboard.stock-ledger.adjust'), [
        'product_id' => $product->id,
        'type' => 'Adjustment (+)',
        'qty' => 15,
        'notes' => 'Bulk adjustment test'
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    // Assert product stock was updated
    $product->refresh();
    expect((float) $product->stock_qty)->toEqual($initialStock + 15);

    // Assert ledger entry was created
    $ledger = StockLedger::latest()->first();
    expect($ledger->product_id)->toEqual($product->id);
    expect($ledger->type)->toEqual('Adjustment (+)');
    expect((float) $ledger->quantity)->toEqual(15.0);
    expect($ledger->notes)->toEqual('Bulk adjustment test');
});

test('user can export stock ledger to excel csv', function () {
    $user = User::factory()->create();
    $user->assignRole('Super Admin');
    
    $response = $this->actingAs($user)->get(route('dashboard.stock-ledger.export'));

    $response->assertOk();
    $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
});
