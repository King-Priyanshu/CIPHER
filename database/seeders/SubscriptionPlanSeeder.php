<?php

namespace Database\Seeders;

use App\Models\SubscriptionPlan;
use Illuminate\Database\Seeder;

class SubscriptionPlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Seed Plan',
                'slug' => 'seed',
                'description' => 'Perfect for starting your journey in community projects.',
                'price' => 29.00,
                'interval' => 'monthly',
                'currency' => 'USD',
                'is_active' => true,
            ],
            [
                'name' => 'Growth Plan',
                'slug' => 'growth',
                'description' => 'Accelerate your contribution and potential rewards.',
                'price' => 79.00,
                'interval' => 'monthly',
                'currency' => 'USD',
                'is_active' => true,
            ],
            [
                'name' => 'Visionary Plan',
                'slug' => 'visionary',
                'description' => 'Maximum impact for serious community builders.',
                'price' => 199.00,
                'interval' => 'monthly',
                'currency' => 'USD',
                'is_active' => true,
            ],
        ];

        foreach ($plans as $plan) {
            SubscriptionPlan::updateOrCreate(['slug' => $plan['slug']], $plan);
        }
    }
}
