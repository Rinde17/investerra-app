<?php

namespace Database\Factories;

use App\Models\TerrainAnalysis;
use App\Models\Terrain; // N'oubliez pas d'importer le modèle Terrain
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TerrainAnalysis>
 */
class TerrainAnalysisFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TerrainAnalysis::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $resaleEstimateMin = fake()->randomFloat(2, 100000, 500000);
        $resaleEstimateMax = fake()->randomFloat(2, $resaleEstimateMin, $resaleEstimateMin + 200000); // Ensure max is >= min

        return [
            'terrain_id' => Terrain::factory(), // Crée automatiquement un terrain
            'price_m2' => fake()->randomFloat(2, 100, 1000),
            'market_price_m2' => fake()->randomFloat(2, 100, 1000),
            'price_difference_percentage' => fake()->randomFloat(2, -50, 50),
            'viability_cost' => fake()->randomFloat(2, 5000, 50000),
            'lots_possible' => fake()->numberBetween(1, 10),
            'resale_estimate_min' => $resaleEstimateMin,
            'resale_estimate_max' => $resaleEstimateMax,
            'net_margin_estimate' => fake()->randomFloat(2, 10000, 100000),
            'profit_margin_percentage' => fake()->randomFloat(2, 5, 50),
            'ai_score' => fake()->randomFloat(2, 0, 100),
            'profitability_label' => fake()->randomElement(['Low', 'Medium', 'High', 'Very High']),
            'overall_risk' => fake()->randomElement(['Low', 'Medium', 'High']),
            'overall_recommendation' => fake()->randomElement(['Buy', 'Hold', 'Sell', 'Review']),
            'analysis_details' => [
                'environmental' => fake()->sentence(),
                'regulatory' => fake()->sentence(),
                'market' => fake()->sentence(),
            ], // Exemple de données JSON
            'analyzed_at' => fake()->dateTimeBetween('-1 year', 'now'),
        ];
    }

    /**
     * Indicate that the analysis has a high AI score.
     *
     * @return static
     */
    public function highAiScore(): static
    {
        return $this->state(fn (array $attributes) => [
            'ai_score' => fake()->randomFloat(2, 80, 100),
            'profitability_label' => 'Very High',
            'overall_recommendation' => 'Buy',
        ]);
    }

    /**
     * Indicate that the analysis has a low AI score.
     *
     * @return static
     */
    public function lowAiScore(): static
    {
        return $this->state(fn (array $attributes) => [
            'ai_score' => fake()->randomFloat(2, 0, 20),
            'profitability_label' => 'Low',
            'overall_recommendation' => 'Sell',
        ]);
    }

    /**
     * Indicate that the analysis has a high risk.
     *
     * @return static
     */
    public function highRisk(): static
    {
        return $this->state(fn (array $attributes) => [
            'overall_risk' => 'High',
            'overall_recommendation' => 'Review',
        ]);
    }
}
