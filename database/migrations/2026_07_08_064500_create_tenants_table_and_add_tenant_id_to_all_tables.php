<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Create tenants table
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Insert default tenant
        DB::table('tenants')->insertOrIgnore([
            'id' => 1,
            'name' => 'Default Bakery',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // List of all 19 tables that need tenant_id
        $tables = [
            'users',
            'settings',
            'units',
            'brands',
            'taxes',
            'categories',
            'suppliers',
            'customers',
            'products',
            'purchases',
            'purchase_items',
            'stock_ledgers',
            'sales',
            'sale_items',
            'recipes',
            'recipe_ingredients',
            'production_batches',
            'production_consumptions',
            'custom_orders',
        ];

        // 3. Add tenant_id to each table
        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName) && !Schema::hasColumn($tableName, 'tenant_id')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->foreignId('tenant_id')->default(1)->constrained('tenants')->onDelete('cascade');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = [
            'users',
            'settings',
            'units',
            'brands',
            'taxes',
            'categories',
            'suppliers',
            'customers',
            'products',
            'purchases',
            'purchase_items',
            'stock_ledgers',
            'sales',
            'sale_items',
            'recipes',
            'recipe_ingredients',
            'production_batches',
            'production_consumptions',
            'custom_orders',
        ];

        foreach (array_reverse($tables) as $tableName) {
            if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'tenant_id')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropColumn('tenant_id');
                });
            }
        }

        Schema::dropIfExists('tenants');
    }
};
