<?php

namespace Database\Factories;

use App\Models\Team;
use App\Models\User; // N'oubliez pas d'importer le modèle User
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Team>
 */
class TeamFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Team::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->company(),
            'owner_id' => User::factory(), // Crée automatiquement un utilisateur qui sera le propriétaire
            'description' => fake()->optional()->sentence(),
        ];
    }

    /**
     * Indicate that the team has a specific owner.
     *
     * @param \App\Models\User|null $user
     * @return static
     */
    public function withOwner(?User $user = null): static
    {
        return $this->state(fn (array $attributes) => [
            'owner_id' => $user ? $user->id : User::factory(),
        ]);
    }

    /**
     * Configure the model to have a specific number of members.
     * Note: This will create additional users and attach them to the team.
     *
     * @param int $count
     * @param string $role
     * @return static
     */
    public function withMembers(int $count = 1, string $role = 'member'): static
    {
        return $this->afterCreating(function (Team $team) use ($count, $role) {
            $users = User::factory($count)->create();
            $team->users()->attach($users->pluck('id'), ['role' => $role]);
        });
    }
}
