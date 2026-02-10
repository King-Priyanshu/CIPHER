<?php

namespace Database\Factories;

use App\Models\Payment;
use App\Models\User;
use App\Models\UserSubscription;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payment>
 */
class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'subscription_id' => UserSubscription::factory(),
            'gateway' => 'razorpay',
            'gateway_transaction_id' => 'pay_' . $this->faker->regexify('[a-zA-Z0-9]{14}'),
            'amount' => $this->faker->randomFloat(2, 100, 1000),
            'currency' => 'INR',
            'status' => 'succeeded',
            'paid_at' => now(),
        ];
    }
}
