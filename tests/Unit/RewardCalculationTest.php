<?php

namespace Tests\Unit;

use App\Models\Project;
use App\Models\RewardPool;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Models\UserSubscription;
use App\Services\Rewards\RewardCalculationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RewardCalculationTest extends TestCase
{
    use RefreshDatabase;

    public function test_rewards_are_distributed_evenly_among_active_subscribers()
    {
        // 1. Setup Data
        $project = Project::factory()->create();
        $pool = RewardPool::create([
            'project_id' => $project->id,
            'total_amount' => 1000.00,
            'status' => 'pending',
        ]);

        // Create 2 active users
        $users = User::factory()->count(2)->create();
        $plan = SubscriptionPlan::factory()->create();

        foreach($users as $user) {
            UserSubscription::create([
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'status' => 'active',
            ]);
        }

        // 2. Execute Service
        $service = new RewardCalculationService();
        $service->calculateForPool($pool);

        // 3. Assertions
        // Each user should get 500
        $this->assertDatabaseCount('rewards', 2);
        
        $this->assertDatabaseHas('rewards', [
            'user_id' => $users[0]->id,
            'amount' => 500.00,
            'status' => 'pending',
        ]);

        $this->assertDatabaseHas('rewards', [
            'user_id' => $users[1]->id,
            'amount' => 500.00,
            'status' => 'pending',
        ]);
    }
}
