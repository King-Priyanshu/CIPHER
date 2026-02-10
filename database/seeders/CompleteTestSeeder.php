<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;
use App\Models\SubscriptionPlan;
use App\Models\Project;
use App\Models\UserSubscription;
use App\Models\ProjectInvestment;
use App\Models\ProfitDistribution;
use App\Models\UserProfitLog;
use Carbon\Carbon;
use Illuminate\Support\Str;

class CompleteTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Roles
        $adminRole = Role::firstOrCreate(['slug' => 'admin'], ['name' => 'Admin']);
        $subscriberRole = Role::firstOrCreate(['slug' => 'subscriber'], ['name' => 'Subscriber']);

        // 2. Admin User
        $admin = User::firstOrCreate(
            ['email' => 'admin@cipher.com'],
            [
                'name' => 'Cipher Admin',
                'password' => Hash::make('password'),
                'role_id' => $adminRole->id,
                'email_verified_at' => now(),
            ]
        );

        // 3. Subscription Plans (Investment Tiers)
        $plans = [
             [
                'name' => 'Starter Pack',
                'slug' => 'starter-pack',
                'price' => 5000.00,
                'description' => "Basic Access, Community Support",
                'interval' => 'monthly', // Legacy field, treated as tier
                'is_active' => true,
            ],
            [
                'name' => 'Growth Pack',
                'slug' => 'growth-pack',
                'price' => 25000.00,
                'description' => "Priority Access, Higher Allocation",
                'interval' => 'monthly',
                'is_active' => true,
            ],
            [
                'name' => 'Elite Pack',
                'slug' => 'elite-pack',
                'price' => 100000.00,
                'description' => "VIP Access, Dedicated Manager",
                'interval' => 'monthly',
                'is_active' => true,
            ],
        ];

        foreach ($plans as $planData) {
            SubscriptionPlan::firstOrCreate(
                ['slug' => $planData['slug']],
                $planData
            );
        }
        
        $starterPlan = SubscriptionPlan::where('slug', 'starter-pack')->first();
        $growthPlan = SubscriptionPlan::where('slug', 'growth-pack')->first();

        // 4. Subscriber Users (Investors)
        $users = [];
        for ($i = 1; $i <= 5; $i++) {
            $user = User::firstOrCreate(
                ['email' => "user{$i}@example.com"],
                [
                    'name' => "Investor {$i}",
                    'password' => Hash::make('password'),
                    'role_id' => $subscriberRole->id,
                    'phone' => "987654320{$i}",
                    'email_verified_at' => now(),
                    'participation_mode' => 'manual', // Auto-allocation disabled
                ]
            );
            $users[] = $user;
        }

        // 5. Projects
        $projectStatuses = [
            ['title' => 'Eco Resort Bali', 'status' => 'active', 'fund_goal' => 5000000, 'current_fund' => 150000],
            ['title' => 'Urban Tech Park', 'status' => 'active', 'fund_goal' => 10000000, 'current_fund' => 4000000],
            ['title' => 'Solar Farm Alpha', 'status' => 'active', 'fund_goal' => 2000000, 'current_fund' => 2000000], // Fully funded, but status is active
            ['title' => 'Crypto Mining Hub', 'status' => 'completed', 'fund_goal' => 500000, 'current_fund' => 500000], // Generating profit
        ];

        foreach ($projectStatuses as $pData) {
            $proj = Project::firstOrCreate(
                ['title' => $pData['title']],
                [
                    'slug' => Str::slug($pData['title']),
                    'description' => 'A high-yield investment opportunity in ' . $pData['title'],
                    'status' => $pData['status'],
                    'fund_goal' => $pData['fund_goal'],
                    'current_fund' => $pData['current_fund'],
                    'business_type' => 'Real Estate', // Defaulting for seeded data
                    'royalty_model' => 'Profit Share',
                    'visibility_status' => 'visible',
                    'allocation_eligibility' => 'both',
                    'starts_at' => now(),
                    'ends_at' => now()->addYear(),
                ]
            );
            $projects[$pData['title']] = $proj;
        }

        // 6. Investments (Pay-Per-Use Orders) & Project Allocations
        
        // Investor 1: Bought Starter Pack, Invested in Eco Resort
        $u1 = $users[0];
        $sub1 = UserSubscription::create([
            'user_id' => $u1->id,
            'plan_id' => $starterPlan->id,
            'amount' => $starterPlan->price,
            'status' => 'active',
            'starts_at' => now()->subDays(10),
            'ends_at' => now()->addDays(20),
            'razorpay_payment_id' => 'pay_test_' . Str::random(10),
        ]);
        
        // Allocate to Project
        ProjectInvestment::create([
            'user_id' => $u1->id,
            'project_id' => $projects['Eco Resort Bali']->id,
            'amount' => 5000,
            'status' => 'active',
            'allocated_at' => now(),
        ]);

        // Investor 2: Bought Growth Pack, Split Inv
        $u2 = $users[1];
        UserSubscription::create([
            'user_id' => $u2->id,
            'plan_id' => $growthPlan->id,
            'amount' => $growthPlan->price,
            'status' => 'active',
            'starts_at' => now()->subDays(5),
            'ends_at' => now()->addDays(25),
            'razorpay_payment_id' => 'pay_test_' . Str::random(10),
        ]);

        ProjectInvestment::create([
            'user_id' => $u2->id,
            'project_id' => $projects['Urban Tech Park']->id,
            'amount' => 15000,
            'status' => 'active',
            'allocated_at' => now(),
        ]);
        
        ProjectInvestment::create([
            'user_id' => $u2->id,
            'project_id' => $projects['Crypto Mining Hub']->id, // Completed project
            'amount' => 10000,
            'status' => 'active',
            'allocated_at' => now()->subMonths(2), // Invested earlier
        ]);


        // 7. Profit Distributions (For Admin Profits Page)
        $completedProject = $projects['Crypto Mining Hub'];
        
        // Distribution 1: 1 Month ago
        $dist1 = ProfitDistribution::create([
            'project_id' => $completedProject->id,
            'total_profit' => 50000,
            'distributed_amount' => 50000,
            'distributed_at' => now()->subMonth(),
            'declared_at' => now()->subMonth(),
            'declared_by' => $admin->id,
            'status' => 'completed',
        ]);
        
        // Log for User 2
        UserProfitLog::create([
            'user_id' => $u2->id,
            'profit_distribution_id' => $dist1->id,
            // 'project_id' => $completedProject->id, // Not in schema, linked via distribution or investment
            'project_investment_id' => null, // Or find user's investment
            'amount' => 1200, // profit_amount -> amount
            'status' => 'credited',
            'credited_at' => now()->subMonth(),
        ]);

        // Distribution 2: Pending
        ProfitDistribution::create([
            'project_id' => $completedProject->id,
            'total_profit' => 60000,
            'distributed_amount' => 0,
            'declared_at' => now(),
            'declared_by' => $admin->id,
            'status' => 'pending',
        ]);

        $this->command->info('Complete Test Data Seeded Successfully!');
    }
}
