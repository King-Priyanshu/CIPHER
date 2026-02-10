<?php

namespace Database\Factories;

use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Project>
 */
class ProjectFactory extends Factory
{
    protected $model = Project::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $fundGoal = $this->faker->numberBetween(100000, 1000000);
        $currentFund = $this->faker->numberBetween(0, $fundGoal);

        return [
            'title' => $this->faker->sentence(3),
            'slug' => $this->faker->slug(),
            'description' => $this->faker->paragraph(),
            'business_type' => $this->faker->randomElement(['Tech', 'Real Estate', 'Agriculture']),
            'royalty_model' => $this->faker->randomElement(['Fixed', 'Percentage']),
            'visibility_status' => 'visible',
            'allocation_eligibility' => $this->faker->randomElement(['manual_only', 'auto_only', 'both']),
            'status' => 'active',
            'fund_goal' => $fundGoal,
            'current_fund' => $currentFund,
            'starts_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'ends_at' => $this->faker->dateTimeBetween('now', '+6 months'),
        ];
    }
}
