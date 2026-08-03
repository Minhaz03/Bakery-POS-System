<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Set session tenant context so all seeded records fall under Tenant 1
        session(['tenant_id' => 1]);

        $this->call([
            RolesAndPermissionsSeeder::class,
            AdminUserSeeder::class,
            SettingsSeeder::class,
            UnitSeeder::class,
            // BrandSeeder::class,
            // TaxSeeder::class,
            // CategorySeeder::class,
            // SupplierSeeder::class,
            // CustomerSeeder::class,
            // ProductSeeder::class,
            // StockLedgerSeeder::class,
            // PurchaseSeeder::class,
            // RecipeSeeder::class,
            // ProductionBatchSeeder::class,
            // SaleSeeder::class,
            // CustomOrderSeeder::class,
        ]);
    }
}
