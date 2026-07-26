<?php

namespace Database\Factories;

use App\Models\Link;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Link>
 */
class LinkFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'profile_id' => 1, // Will be replaced with actual profile ID
            'label' => $this->faker->text(30),
            'url' => $this->faker->url(),
            'icon' => $this->faker->optional()->imageUrl(),
            'sort_order' => $this->faker->numberBetween(0, 10),
        ];
    }
}