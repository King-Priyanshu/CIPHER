<?php

namespace Database\Factories;

use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Models\UserSubscription;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserSubscription>
 */
class UserSubscriptionFactory extends Factory
{
    protected $model = UserSubscription::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'plan_id' => \App\Models\SubscriptionPlan::inRandomOrder()->first()?->id,
            'amount' => $this->faker->randomFloat(2, 100, 1000),
            'allocated_amount' => 0,
            'status' => 'active',
            'starts_at' => now(),
            'ends_at' => now()->addMonth(),
        ];
    }

    /**
     * Configure the model factory.
     */
    public function configure(): static
    {
        return $this->afterMaking(function (UserSubscription $subscription) {
             if (!$subscription->plan_id) {
                 $plan = SubscriptionPlan::inRandomOrder()->first();
                 if ($plan) {
                     $subscription->plan_id = $plan->id;
                     $subscription->amount = $plan->price;
                 }
             }
        });
    }
}
