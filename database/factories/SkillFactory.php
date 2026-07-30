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
     * Contraseña actual utilizada por el factory.
     */
    protected static ?string $password;

    /**
     * Define el estado por defecto del modelo.
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

    /**
     * Configura el factory para crear medios de Spatie después de crear el modelo.
     */
    public function withMedia(): self
    {
        return $this->afterCreating(function (Skill $skill) {
            // Crea un icono para la habilidad usando Spatie Media Library
            $skill->addMediaFromString('icon-data')
                ->usingFileName('icon-'.$skill->id.'.webp')
                ->toMediaCollection('icons');
        });
    }
}
