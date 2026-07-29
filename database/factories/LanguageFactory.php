<?php

namespace Database\Factories;

use App\Models\Language;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Language>
 */
class LanguageFactory extends Factory
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
            'name' => $this->faker->randomElement(['Español', 'Inglés', 'Portugués', 'Francés', 'Italiano']),
            'level' => $this->faker->randomElement(['Beginner', 'Intermediate', 'Advanced', 'Fluent', 'Native']),
            'sort_order' => $this->faker->numberBetween(0, 10),
        ];
    }
}
