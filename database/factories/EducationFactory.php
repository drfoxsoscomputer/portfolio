<?php

namespace Database\Factories;

use App\Models\Education;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Education>
 */
class EducationFactory extends Factory
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
            'institution' => $this->faker->company(),
            'degree' => $this->faker->randomElement(['Bachelor', 'Master', 'PhD', 'Associate', 'Certificate', 'Diploma']),
            'field' => $this->faker->optional()->word(),
            'description' => $this->faker->optional()->text(100),
            'start_date' => $this->faker->optional()->dateTimeBetween('-20 years', '-5 years'),
            'end_date' => $this->faker->optional()->dateTimeBetween('-20 years', 'now'),
            'is_current' => $this->faker->boolean(15),
            'sort_order' => $this->faker->numberBetween(0, 10),
        ];
    }
}
