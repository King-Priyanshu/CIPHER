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
                'name' => 'Daily Saver',
                'slug' => 'daily-saver',
                'description' => 'Start small with just ₹1 per day. Build the habit of saving.',
                'price' => 30.00,
                'interval' => 'monthly',
                'currency' => 'INR',
                'is_active' => true,
            ],
            [
                'name' => 'Standard Member',
                'slug' => 'standard',
                'description' => 'The complete community participation plan.',
                'price' => 365.00,
                'interval' => 'monthly',
                'currency' => 'INR',
                'is_active' => true,
            ],
            [
                'name' => 'Premium Member',
                'slug' => 'premium',
                'description' => 'Maximize your contribution and community impact.',
                'price' => 730.00,
                'interval' => 'monthly',
                'currency' => 'INR',
                'is_active' => true,
            ],
        ];

        // Deactivate old plans (optional cleanup)
        SubscriptionPlan::whereNotIn('slug', array_column($plans, 'slug'))->update(['is_active' => false]);

        foreach ($plans as $plan) {
            SubscriptionPlan::updateOrCreate(['slug' => $plan['slug']], $plan);
        }
    }
}
