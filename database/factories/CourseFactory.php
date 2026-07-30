<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory para el modelo Course.
 */
class CourseFactory extends Factory
{
    protected $model = Course::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => fake()->sentence(3),
            'institution' => fake()->company(),
            'date' => fake()->date(),
            'description' => fake()->paragraph(),
            'sort_order' => fake()->numberBetween(0, 10),
        ];
    }
}