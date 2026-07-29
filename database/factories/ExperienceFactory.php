<?php

namespace Database\Factories;

use App\Models\Experience;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Experience>
 */
class ExperienceFactory extends Factory
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
            'company' => $this->faker->company(),
            'role' => $this->faker->jobTitle(),
            'description' => $this->faker->optional()->text(200),
            'location' => $this->faker->optional()->city(),
            'start_date' => $this->faker->dateTimeBetween('-10 years', 'now'),
            'end_date' => $this->faker->optional()->dateTimeBetween('-10 years', 'now'),
            'is_current' => $this->faker->boolean(20),
            // Media will be handled by trait HasMedia, factory doesn't need to set it directly
            // Spatie media will be assigned separately when model is saved
        ];
    }
}