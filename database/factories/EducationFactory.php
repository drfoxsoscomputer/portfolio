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

    /**
     * Configura el factory para crear medios de Spatie después de crear el modelo.
     */
    public function withMedia(): self
    {
        return $this->afterCreating(function (Education $education) {
            // Crea certificados para la formación usando Spatie Media Library
            $education->addMediaFromString('certificate-data')
                ->usingFileName('certificate-'.$education->id.'.pdf')
                ->toMediaCollection('certificates');
        });
    }
}
