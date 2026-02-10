<?php

namespace Database\Seeders;

use App\Models\Payment;
use App\Models\Project;
use App\Models\ProjectInvestment;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Models\UserSubscription;
use Illuminate\Database\Seeder;

class DummyDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure plans exist
        $this->call(SubscriptionPlanSeeder::class);

        $plans = SubscriptionPlan::all();
        if ($plans->isEmpty()) {
            return;
        }

        // Create Users
        $users = User::factory(10)->create();

        // Create Projects
        $projects = Project::factory(5)->create();

        foreach ($users as $user) {
            // Assign a random plan to the user
            $plan = $plans->random();
            
            $subscription = UserSubscription::factory()->create([
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'amount' => $plan->price,
            ]);

            // Create a payment for the subscription
            Payment::factory()->create([
                'user_id' => $user->id,
                'subscription_id' => $subscription->id,
                'amount' => $subscription->amount,
            ]);

            // Create Investments for random projects
            $randomProjects = $projects->random(rand(1, 3));
            foreach ($randomProjects as $project) {
                ProjectInvestment::factory()->create([
                    'user_id' => $user->id,
                    'project_id' => $project->id,
                    'subscription_id' => $subscription->id,
                    'amount' => rand(100, 1000),
                ]);
            }
        }
    }
}
