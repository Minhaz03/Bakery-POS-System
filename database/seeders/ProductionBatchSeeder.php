<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Recipe;
use App\Models\ProductionOrder;
use App\Models\ProductionBatch;

class ProductionBatchSeeder extends Seeder
{
    public function run(): void
    {
        $recipeBun = Recipe::where('name', 'Special Butter Bun Recipe')->first();
        $recipeCake = Recipe::where('name', 'Classic Pound Cake Recipe')->first();
        
        if ($recipeBun) {
            $orderBun = ProductionOrder::create([
                'tenant_id' => 1,
                'reference_no' => 'PO-001',
                'recipe_id' => $recipeBun->id,
                'planned_quantity' => 50,
                'actual_quantity' => 48,
                'planned_date' => now()->subDays(2),
                'produced_at' => now()->subDays(2),
                'status' => 'completed',
                'total_cost' => 150.00,
            ]);

            ProductionBatch::create([
                'tenant_id' => 1,
                'production_order_id' => $orderBun->id,
                'batch_number' => 'BAT-001',
                'qty' => 50,
                'scheduled_at' => now()->subDays(2),
                'manufacturing_date' => now()->subDays(2),
                'expiry_date' => now()->addDays(5),
            ]);
        }

        if ($recipeCake) {
            $orderCake1 = ProductionOrder::create([
                'tenant_id' => 1,
                'reference_no' => 'PO-002',
                'recipe_id' => $recipeCake->id,
                'planned_quantity' => 20,
                'actual_quantity' => 20,
                'planned_date' => now()->subDays(1),
                'produced_at' => now()->subDays(1),
                'status' => 'completed',
                'total_cost' => 120.00,
            ]);

            ProductionBatch::create([
                'tenant_id' => 1,
                'production_order_id' => $orderCake1->id,
                'batch_number' => 'BAT-002',
                'qty' => 20,
                'scheduled_at' => now()->subDays(1),
                'manufacturing_date' => now()->subDays(1),
                'expiry_date' => now()->addDays(7),
            ]);

            $orderCake2 = ProductionOrder::create([
                'tenant_id' => 1,
                'reference_no' => 'PO-003',
                'recipe_id' => $recipeCake->id,
                'planned_quantity' => 15,
                'actual_quantity' => 0,
                'planned_date' => now(),
                'status' => 'planned',
                'total_cost' => 90.00,
            ]);

            ProductionBatch::create([
                'tenant_id' => 1,
                'production_order_id' => $orderCake2->id,
                'batch_number' => 'BAT-003',
                'qty' => 15,
                'scheduled_at' => now(),
            ]);
        }
    }
}
