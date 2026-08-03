<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('production_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->index();
            $table->string('reference_no')->nullable();
            $table->unsignedBigInteger('recipe_id');
            $table->decimal('planned_quantity', 10, 3);
            $table->decimal('actual_quantity', 10, 3)->nullable();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->unsignedBigInteger('warehouse_id')->nullable();
            $table->date('planned_date')->nullable();
            $table->datetime('produced_at')->nullable();
            $table->string('status')->default('planned');
            $table->decimal('cost_per_unit', 12, 2)->nullable();
            $table->decimal('total_cost', 12, 2)->nullable();
            $table->unsignedBigInteger('produced_by')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('production_order_ingredients', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->index();
            $table->unsignedBigInteger('production_order_id');
            $table->unsignedBigInteger('ingredient_id');
            $table->decimal('required_qty', 10, 3);
            $table->decimal('consumed_qty', 10, 3)->nullable();
            $table->decimal('waste_qty', 10, 3)->nullable();
            $table->timestamps();

            $table->foreign('production_order_id')->references('id')->on('production_orders')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('production_order_ingredients');
        Schema::dropIfExists('production_orders');
    }
};
