<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
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
            'name' => $this->faker->company(),
            'description' => $this->faker->text(200),
            'tech_stack' => $this->faker->randomElements(['PHP', 'Laravel', 'Vue', 'React', 'MySQL', 'Git', 'Docker', 'AWS'], $this->faker->numberBetween(2, 5)),
            'role' => $this->faker->jobTitle(),
            'team_size' => $this->faker->numberBetween(1, 20),
            'url' => $this->faker->optional()->url(),
            'repo_url' => $this->faker->optional()->url(),
            'start_date' => $this->faker->dateTimeBetween('-5 years', '-1 year'),
            'end_date' => $this->faker->optional()->dateTimeBetween('-1 year', 'now'),
            'is_current' => $this->faker->boolean(20),
            'is_featured' => $this->faker->boolean(30),
        ];
    }
}
