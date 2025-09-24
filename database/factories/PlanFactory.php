<?php

namespace Database\Factories;

use App\Models\Plan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Plan>
 */
class PlanFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Plan::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(2, true) . ' Plan', // Ex: "Basic Plan", "Premium Plan"
            'price_monthly' => fake()->randomFloat(2, 9.99, 499.99),
            'stripe_price_id' => fake()->optional()->uuid(), // Peut être nul
            'description' => fake()->optional()->paragraph(),
            'analyses_per_week' => fake()->numberBetween(5, 100),
            'pdf_pro' => fake()->boolean(),
            'comparator' => fake()->boolean(),
            'pdf_expert' => fake()->boolean(),
            'fiscal_analysis' => fake()->boolean(),
            'custom_alerts' => fake()->boolean(),
            'priority_support' => fake()->boolean(),
            'dedicated_account_manager' => fake()->boolean(),
        ];
    }

    /**
     * Indicate that the plan is a basic plan.
     *
     * @return static
     */
    public function basic(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Basic Plan',
            'price_monthly' => 9.99,
            'analyses_per_week' => 5,
            'pdf_pro' => false,
            'comparator' => false,
            'pdf_expert' => false,
            'fiscal_analysis' => false,
            'custom_alerts' => false,
            'priority_support' => false,
            'dedicated_account_manager' => false,
        ]);
    }

    /**
     * Indicate that the plan is a pro plan.
     *
     * @return static
     */
    public function pro(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Pro Plan',
            'price_monthly' => 49.99,
            'analyses_per_week' => 20,
            'pdf_pro' => true,
            'comparator' => true,
            'pdf_expert' => false,
            'fiscal_analysis' => false,
            'custom_alerts' => true,
            'priority_support' => false,
            'dedicated_account_manager' => false,
        ]);
    }

    /**
     * Indicate that the plan is an expert plan.
     *
     * @return static
     */
    public function expert(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Expert Plan',
            'price_monthly' => 199.99,
            'analyses_per_week' => 100,
            'pdf_pro' => true,
            'comparator' => true,
            'pdf_expert' => true,
            'fiscal_analysis' => true,
            'custom_alerts' => true,
            'priority_support' => true,
            'dedicated_account_manager' => true,
        ]);
    }
}

