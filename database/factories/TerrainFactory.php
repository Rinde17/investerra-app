<?php

namespace Database\Factories;

use App\Models\Terrain;
use App\Models\User; // N'oubliez pas d'importer le modèle User
use App\Models\TerrainAnalysis; // N'oubliez pas d'importer le modèle TerrainAnalysis
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Terrain>
 */
class TerrainFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Terrain::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(3),
            'description' => fake()->optional()->paragraph(),
            'surface_m2' => fake()->randomFloat(2, 50, 5000), // Surface entre 50 et 5000 m²
            'price' => fake()->randomFloat(2, 10000, 1000000), // Prix entre 10 000 et 1 000 000
            'city' => fake()->city(),
            'zip_code' => fake()->postcode(),
            'latitude' => fake()->latitude(40, 50), // Latitude approximative pour la France
            'longitude' => fake()->longitude(0, 10), // Longitude approximative pour la France
            'viabilised' => fake()->boolean(),
            'source_url' => fake()->optional()->url(),
            'source_platform' => fake()->optional()->word(),
            'is_scraped' => fake()->boolean(),
            'user_id' => User::factory(), // Crée automatiquement un utilisateur
        ];
    }

    /**
     * Indicate that the terrain is viabilised.
     *
     * @return static
     */
    public function viabilised(): static
    {
        return $this->state(fn (array $attributes) => [
            'viabilised' => true,
        ]);
    }

    /**
     * Indicate that the terrain is not viabilised.
     *
     * @return static
     */
    public function notViabilised(): static
    {
        return $this->state(fn (array $attributes) => [
            'viabilised' => false,
        ]);
    }

    /**
     * Indicate that the terrain is scraped.
     *
     * @return static
     */
    public function scraped(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_scraped' => true,
        ]);
    }

    /**
     * Indicate that the terrain is not scraped.
     *
     * @return static
     */
    public function notScraped(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_scraped' => false,
        ]);
    }

    /**
     * Configure the model to have an associated analysis.
     *
     * @return static
     */
    public function withAnalysis(): static
    {
        return $this->afterCreating(function (Terrain $terrain) {
            TerrainAnalysis::factory()->for($terrain)->create();
        });
    }
}
