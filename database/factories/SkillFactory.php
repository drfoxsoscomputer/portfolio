<?php

namespace Database\Factories;

use App\Models\Skill;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Skill>
 */
class SkillFactory extends Factory
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
        $skills = [
            ['name' => 'PHP', 'category' => 'Lenguajes'],
            ['name' => 'Laravel', 'category' => 'Frameworks'],
            ['name' => 'Vue.js', 'category' => 'Frameworks'],
            ['name' => 'React', 'category' => 'Frameworks'],
            ['name' => 'MySQL', 'category' => 'DB'],
            ['name' => 'PostgreSQL', 'category' => 'DB'],
            ['name' => 'Git', 'category' => 'Tools'],
            ['name' => 'Docker', 'category' => 'Tools'],
            ['name' => 'AWS', 'category' => 'Tools'],
            ['name' => 'Agile', 'category' => 'Metodologías'],
            ['name' => 'Scrum', 'category' => 'Metodologías'],
            ['name' => 'Kanban', 'category' => 'Metodologías'],
        ];

        $skill = $skills[$this->faker->numberBetween(0, count($skills) - 1)];

        return [
            'user_id' => User::factory(),
            'name' => $skill['name'],
            'category' => $skill['category'],
            'sort_order' => $this->faker->numberBetween(0, 10),
        ];
    }
}
