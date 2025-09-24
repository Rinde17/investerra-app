<?php

namespace Database\Factories;

use App\Models\Device;
use App\Models\User; // N'oubliez pas d'importer le modèle User
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Device>
 */
class DeviceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Device::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(), // Crée automatiquement un utilisateur si non spécifié
            'uuid' => fake()->uuid(),
            'user_agent' => fake()->userAgent(),
            'ip_address' => fake()->optional()->ipv4(), // Peut être IPv4 ou IPv6, ici on prend IPv4 en option
            'name' => fake()->optional()->word(), // Un simple mot comme nom d'appareil
            'last_used_at' => fake()->optional()->dateTimeBetween('-1 year', 'now'),
            'is_trusted' => fake()->boolean(),
        ];
    }

    /**
     * Indicate that the device is trusted.
     *
     * @return static
     */
    public function trusted(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_trusted' => true,
        ]);
    }

    /**
     * Indicate that the device is untrusted.
     *
     * @return static
     */
    public function untrusted(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_trusted' => false,
        ]);
    }

    /**
     * Indicate that the device has no UUID.
     *
     * @return static
     */
    public function noUuid(): static
    {
        return $this->state(fn (array $attributes) => [
            'uuid' => null,
        ]);
    }
}
