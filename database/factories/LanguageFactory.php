<?php

namespace Database\Factories;

use App\Models\Language;
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
            'profile_id' => 1, // Will be overridden in tests
            'name' => $this->faker->randomElement(['Español', 'Inglés', 'Portugués', 'Francés', 'Italiano']),
            'level' => $this->faker->randomElement(['Beginner', 'Intermediate', 'Advanced', 'Fluent', 'Native']),
            'sort_order' => $this->faker->numberBetween(0, 10),
        ];
    }
}
