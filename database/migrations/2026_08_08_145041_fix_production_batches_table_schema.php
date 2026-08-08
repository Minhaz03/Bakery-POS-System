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
        Schema::table('production_batches', function (Blueprint $table) {
            $table->dropForeign(['recipe_id']);
            $table->dropColumn(['recipe_id', 'batch_code']);

            $table->unsignedBigInteger('production_order_id')->nullable()->after('id');
            $table->string('batch_number')->nullable()->after('production_order_id');
            
            $table->foreign('production_order_id')->references('id')->on('production_orders')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('production_batches', function (Blueprint $table) {
            $table->dropForeign(['production_order_id']);
            $table->dropColumn(['production_order_id', 'batch_number']);
            
            $table->string('batch_code')->nullable();
            $table->unsignedBigInteger('recipe_id')->nullable();
        });
    }
};
