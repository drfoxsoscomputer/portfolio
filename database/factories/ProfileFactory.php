<?php

namespace Database\Factories;

use App\Models\Profile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Profile>
 */
class ProfileFactory extends Factory
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
            'name' => $this->faker->name(),
            'title' => $this->faker->jobTitle(),
            'location' => $this->faker->city(),
            'phone' => $this->faker->phoneNumber(),
            'email' => $this->faker->email(),
            'summary' => $this->faker->text(200),
            'avatar' => null,
        ];
    }

    /**
     * Configure the model factory.
     */
    public function configure(): static
    {
        return $this->afterCreating(function (Profile $profile) {
            // Singleton enforcement is handled by ProfileObserver
            // Factory relies on the observer to prevent duplicates
        });
    }
}