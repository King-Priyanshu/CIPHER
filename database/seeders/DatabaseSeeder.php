<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            AdminUserSeeder::class,
            LedgerAccountSeeder::class,
            SubscriptionPlanSeeder::class,
            ProjectSeeder::class,
            UserSeeder::class,
            ContentPageSeeder::class,
            // Add other seeders as needed for full test coverage
            // DummyDataSeeder::class can be used for bulk test data
        ]);
    }
}
