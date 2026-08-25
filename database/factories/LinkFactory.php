<?php

namespace Database\Factories;

use App\Models\Link;
use App\Models\User;
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
            'user_id' => User::factory(),
            'label' => $this->faker->text(30),
            'url' => $this->faker->url(),
            'icon' => fake()->randomElement([
                'github',
                'linkedin',
                'x',
                'instagram',
                'youtube',
            ]),
            'sort_order' => $this->faker->numberBetween(0, 10),
        ];
    }
}
