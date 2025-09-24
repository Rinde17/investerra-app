<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\User; // N'oubliez pas d'importer le modèle User
use App\Models\Team; // N'oubliez pas d'importer le modèle Team
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Project>
 */
class ProjectFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Project::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->sentence(3),
            'description' => fake()->optional()->paragraph(),
            'user_id' => User::factory(), // Crée automatiquement un utilisateur
            'team_id' => null, // Crée automatiquement une équipe si non spécifié, peut être nul
            'notes' => fake()->optional()->text(),
            'is_saved' => fake()->boolean(),
        ];
    }

    /**
     * Indicate that the project is saved.
     *
     * @return static
     */
    public function saved(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_saved' => true,
        ]);
    }

    /**
     * Indicate that the project is not saved.
     *
     * @return static
     */
    public function unsaved(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_saved' => false,
        ]);
    }

    /**
     * Indicate that the project belongs to a specific team.
     *
     * @param \App\Models\Team|null $team
     * @return static
     */
    public function withTeam(?Team $team = null): static
    {
        return $this->state(fn (array $attributes) => [
            'team_id' => $team ? $team->id : Team::factory(),
        ]);
    }

    /**
     * Indicate that the project does not belong to any team.
     *
     * @return static
     */
    public function withoutTeam(): static
    {
        return $this->state(fn (array $attributes) => [
            'team_id' => null,
        ]);
    }
}
