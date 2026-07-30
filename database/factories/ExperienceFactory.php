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
        return [
            'user_id' => User::factory(),
            'company' => $this->faker->company(),
            'role' => $this->faker->jobTitle(),
            'description' => $this->faker->optional()->text(200),
            'location' => $this->faker->optional()->city(),
            'start_date' => $this->faker->dateTimeBetween('-10 years', 'now'),
            'end_date' => $this->faker->optional()->dateTimeBetween('-10 years', 'now'),
            'is_current' => $this->faker->boolean(20),
        ];
    }

    /**
     * Configura el factory para crear medios de Spatie después de crear el modelo.
     */
    public function withMedia(): self
    {
        return $this->afterCreating(function (Experience $experience) {
            // Crea un logo para la experiencia usando Spatie Media Library
            $experience->addMediaFromString('logo-data')
                ->usingFileName('logo-'.$experience->id.'.webp')
                ->toMediaCollection('logos');
        });
    }
}
