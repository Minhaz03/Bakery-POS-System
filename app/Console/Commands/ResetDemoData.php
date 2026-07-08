<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ResetDemoData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:reset-demo {--force : Force the operation to run without confirmation} {--no-seed : Do not seed database after truncation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Truncate all database tables except users, roles, and permissions, then optionally seed demo data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (app()->environment('production') && !$this->option('force')) {
            if (!$this->confirm('This application is in PRODUCTION. Do you really want to reset the database?')) {
                $this->error('Operation cancelled.');
                return 1;
            }
        } elseif (!$this->option('force')) {
            $action = $this->option('no-seed') ? 'truncate all data (except users, roles, and permissions)' : 'truncate all data and seed fresh demo data';
            if (!$this->confirm("This will {$action}. Do you want to proceed?")) {
                $this->error('Operation cancelled.');
                return 1;
            }
        }

        $this->info('Starting database reset...');

        // Get all tables in the current database
        $currentDatabase = DB::getDatabaseName();
        $tables = [];
        foreach (Schema::getTables() as $table) {
            $tableName = is_array($table) ? ($table['name'] ?? null) : ($table->name ?? null);
            $tableSchema = is_array($table) ? ($table['schema'] ?? null) : ($table->schema ?? null);

            if ($tableName && (!$tableSchema || $tableSchema === $currentDatabase)) {
                $tables[] = $tableName;
            }
        }

        // Tables that we must NOT truncate
        $excludedTables = [
            'users',
            'roles',
            'permissions',
            'model_has_roles',
            'model_has_permissions',
            'role_has_permissions',
            'migrations',
            'sessions',
            'password_reset_tokens',
        ];

        // Filter tables to truncate
        $tablesToTruncate = array_filter($tables, function ($table) use ($excludedTables) {
            return !in_array($table, $excludedTables);
        });

        $this->warn('Truncating tables: ' . implode(', ', $tablesToTruncate));

        Schema::disableForeignKeyConstraints();

        foreach ($tablesToTruncate as $table) {
            try {
                DB::table($table)->truncate();
                $this->line("Truncated table: {$table}");
            } catch (\Exception $e) {
                $this->error("Failed to truncate table {$table}: " . $e->getMessage());
            }
        }

        Schema::enableForeignKeyConstraints();

        if ($this->option('no-seed')) {
            $this->info('Database reset completed successfully! Seeding was skipped as requested.');
            return 0;
        }

        $this->info('Tables truncated successfully. Starting database seeding...');

        $seeders = [
            \Database\Seeders\SettingsSeeder::class,
            \Database\Seeders\UnitSeeder::class,
            \Database\Seeders\BrandSeeder::class,
            \Database\Seeders\TaxSeeder::class,
            \Database\Seeders\CategorySeeder::class,
            \Database\Seeders\SupplierSeeder::class,
            \Database\Seeders\CustomerSeeder::class,
            \Database\Seeders\ProductSeeder::class,
            \Database\Seeders\StockLedgerSeeder::class,
            \Database\Seeders\PurchaseSeeder::class,
            \Database\Seeders\RecipeSeeder::class,
            \Database\Seeders\ProductionBatchSeeder::class,
            \Database\Seeders\SaleSeeder::class,
            \Database\Seeders\CustomOrderSeeder::class,
        ];

        foreach ($seeders as $seeder) {
            $this->info("Running seeder: {$seeder}");
            try {
                $this->call('db:seed', ['--class' => $seeder]);
            } catch (\Exception $e) {
                $this->error("Failed to run seeder {$seeder}: " . $e->getMessage());
            }
        }

        $this->info('Database reset and demo data seeding completed successfully!');
        return 0;
    }
}
