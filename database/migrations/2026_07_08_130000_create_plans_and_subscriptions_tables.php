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
        // 1. Create plans table
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('price', 10, 2);
            $table->string('billing_cycle')->default('monthly'); // monthly, yearly
            $table->integer('limit_products')->default(100);
            $table->integer('limit_users')->default(5);
            $table->timestamps();
        });

        // 2. Create subscriptions table
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->foreignId('plan_id')->constrained('plans')->onDelete('cascade');
            $table->dateTime('starts_at');
            $table->dateTime('ends_at');
            $table->string('status')->default('active'); // active, expired, cancelled
            $table->timestamps();
        });

        // 3. Seed default plans
        DB::table('plans')->insert([
            [
                'id' => 1,
                'name' => 'Basic Plan',
                'price' => 999.00,
                'billing_cycle' => 'monthly',
                'limit_products' => 50,
                'limit_users' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'name' => 'Standard Plan',
                'price' => 1999.00,
                'billing_cycle' => 'monthly',
                'limit_products' => 200,
                'limit_users' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'name' => 'Enterprise Plan',
                'price' => 4999.00,
                'billing_cycle' => 'monthly',
                'limit_products' => 9999, // unlimited representation
                'limit_users' => 9999, // unlimited representation
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // 4. Create active subscription for default tenant (ID = 1) valid for 1 year
        DB::table('subscriptions')->insert([
            'tenant_id' => 1,
            'plan_id' => 3, // Enterprise
            'starts_at' => now(),
            'ends_at' => now()->addYear(),
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
        Schema::dropIfExists('plans');
    }
};
