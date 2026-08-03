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
        Schema::table('stock_ledgers', function (Blueprint $table) {
            $table->renameColumn('qty', 'quantity');
            $table->unsignedBigInteger('variant_id')->nullable()->after('product_id');
            $table->unsignedBigInteger('warehouse_id')->nullable()->after('variant_id');
            $table->unsignedBigInteger('branch_id')->nullable()->after('warehouse_id');
            $table->decimal('unit_cost', 12, 3)->nullable()->after('type');
            $table->nullableMorphs('reference');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_ledgers', function (Blueprint $table) {
            $table->renameColumn('quantity', 'qty');
            $table->dropColumn(['variant_id', 'warehouse_id', 'branch_id', 'unit_cost']);
            $table->dropMorphs('reference');
        });
    }
};
