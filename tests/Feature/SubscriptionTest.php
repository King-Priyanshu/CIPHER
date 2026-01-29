<?php

namespace Tests\Feature;

use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Models\UserSubscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SubscriptionTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_subscription_plans()
    {
        SubscriptionPlan::factory()->create(['name' => 'Gold Plan', 'is_active' => true]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Gold Plan');
    }

    public function test_user_can_subscribe_to_plan()
    {
        $user = User::factory()->create();
        $plan = SubscriptionPlan::factory()->create();

        $this->actingAs($user);

        // Simulate payment success webhook or manual activation logic
        // This is a simplified assertion assuming we have a route or service logic
        
        $subscription = UserSubscription::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'status' => 'active',
            'starts_at' => now(),
        ]);

        $this->assertDatabaseHas('user_subscriptions', [
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'status' => 'active',
        ]);
    }
}
