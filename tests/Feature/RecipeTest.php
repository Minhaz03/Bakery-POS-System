<?php

use App\Models\User;
use App\Models\Product;
use App\Models\Recipe;
use App\Models\RecipeIngredient;
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

test('authenticated user can view recipes index', function () {
    $user = User::factory()->create();
    $user->assignRole('Super Admin');

    Recipe::create([
        'name' => 'Fudge Brownie',
        'prep_time' => '15 mins',
        'bake_time' => '25 mins',
        'yield_qty' => 12,
        'yield_unit' => 'slices',
        'is_active' => true,
    ]);

    $response = $this->actingAs($user)->get(route('dashboard.recipes'));

    $response->assertOk();
    $response->assertSee('Recipes Book');
    $response->assertSee('Fudge Brownie');
});

test('authenticated user can view recipe create page', function () {
    $user = User::factory()->create();
    $user->assignRole('Super Admin');

    $response = $this->actingAs($user)->get(route('dashboard.recipes.create'));

    $response->assertOk();
    $response->assertSee('Add New Recipe');
});

test('user can store a recipe with ingredients', function () {
    $user = User::factory()->create();
    $user->assignRole('Super Admin');
    $product = Product::first();

    $response = $this->actingAs($user)->post(route('dashboard.recipes.store'), [
        'name' => 'Classic Croissant',
        'category' => 'Pastry',
        'prep_time' => '3 hours',
        'bake_time' => '20 mins',
        'yield_qty' => 10,
        'yield_unit' => 'pcs',
        'instructions' => 'Step 1: Roll dough...',
        'ingredients' => [
            'ingredient_name' => ['Butter', 'Flour'],
            'product_id' => [null, $product->id],
            'quantity' => [250, 500],
            'unit' => ['g', 'g'],
            'unit_cost' => [0.8, 0.12],
            'notes' => ['Unsalted', 'High gluten flour']
        ]
    ]);

    $response->assertRedirect(route('dashboard.recipes'));
    $response->assertSessionHas('success');

    $recipe = Recipe::where('name', 'Classic Croissant')->first();
    expect($recipe)->not->toBeNull();
    expect($recipe->category)->toEqual('Pastry');
    expect($recipe->prep_time)->toEqual('3 hours');
    expect($recipe->bake_time)->toEqual('20 mins');
    expect((int)$recipe->yield_qty)->toEqual(10);
    expect($recipe->yield_unit)->toEqual('pcs');

    // Cost: 250*0.8 + 500*0.12 = 200 + 60 = 260
    expect((float)$recipe->estimated_cost)->toEqual(260.00);

    // Verify ingredients
    $ingredients = $recipe->ingredients;
    expect($ingredients->count())->toEqual(2);
    expect($ingredients[0]->ingredient_name)->toEqual('Butter');
    expect($ingredients[0]->product_id)->toBeNull();
    expect($ingredients[1]->ingredient_name)->toEqual('Flour');
    expect($ingredients[1]->product_id)->toEqual($product->id);
});

test('user can view specific recipe details', function () {
    $user = User::factory()->create();
    $user->assignRole('Super Admin');

    $recipe = Recipe::create([
        'name' => 'Strawberry Jam Cake',
        'prep_time' => '20 mins',
        'bake_time' => '40 mins',
        'yield_qty' => 1,
        'yield_unit' => 'cake',
    ]);

    $response = $this->actingAs($user)->get(route('dashboard.recipes.show', $recipe));

    $response->assertOk();
    $response->assertSee('Strawberry Jam Cake');
    $response->assertSee('Recipe Details');
});

test('user can update a recipe and ingredients', function () {
    $user = User::factory()->create();
    $user->assignRole('Super Admin');

    $recipe = Recipe::create([
        'name' => 'Gluten Free Sourdough',
        'prep_time' => '10h',
        'bake_time' => '45m',
        'yield_qty' => 1,
        'yield_unit' => 'loaf',
    ]);

    RecipeIngredient::create([
        'recipe_id' => $recipe->id,
        'ingredient_name' => 'Rice Flour',
        'quantity' => 400,
        'unit' => 'g',
        'unit_cost' => 0.15,
        'subtotal' => 60
    ]);

    $response = $this->actingAs($user)->put(route('dashboard.recipes.update', $recipe), [
        'name' => 'Gluten Free Sourdough v2',
        'prep_time' => '12h',
        'bake_time' => '50m',
        'yield_qty' => 1,
        'yield_unit' => 'loaf',
        'ingredients' => [
            'ingredient_name' => ['Rice Flour', 'Tapioca Starch'],
            'product_id' => [null, null],
            'quantity' => [350, 100],
            'unit' => ['g', 'g'],
            'unit_cost' => [0.15, 0.25],
        ]
    ]);

    $response->assertRedirect(route('dashboard.recipes'));
    
    $recipe->refresh();
    expect($recipe->name)->toEqual('Gluten Free Sourdough v2');
    expect($recipe->prep_time)->toEqual('12h');
    expect($recipe->bake_time)->toEqual('50m');
    
    // Cost: 350 * 0.15 + 100 * 0.25 = 52.5 + 25 = 77.5
    expect((float)$recipe->estimated_cost)->toEqual(77.50);

    $ingredients = $recipe->ingredients;
    expect($ingredients->count())->toEqual(2);
    expect($ingredients[0]->ingredient_name)->toEqual('Rice Flour');
    expect($ingredients[1]->ingredient_name)->toEqual('Tapioca Starch');
});

test('user can delete a recipe', function () {
    $user = User::factory()->create();
    $user->assignRole('Super Admin');

    $recipe = Recipe::create([
        'name' => 'Garlic Bread',
        'yield_qty' => 5,
    ]);

    $response = $this->actingAs($user)->delete(route('dashboard.recipes.destroy', $recipe));

    $response->assertRedirect(route('dashboard.recipes'));
    $response->assertSessionHas('success');

    $recipeDeleted = Recipe::find($recipe->id);
    expect($recipeDeleted)->toBeNull();
});
