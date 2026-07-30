<?php

namespace Database\Factories;

use App\Models\Language;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Language>
 */
class LanguageFactory extends Factory
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
            'name' => $this->faker->randomElement(['Español', 'Inglés', 'Portugués', 'Francés', 'Italiano']),
            'level' => $this->faker->randomElement(['Beginner', 'Intermediate', 'Advanced', 'Fluent', 'Native']),
            'sort_order' => $this->faker->numberBetween(0, 10),
        ];
    }

    /**
     * Configura el factory para crear medios de Spatie después de crear el modelo.
     */
    public function withMedia(): self
    {
        return $this->afterCreating(function (Language $language) {
            // Crea un icono de bandera para el idioma usando Spatie Media Library
            $language->addMediaFromString('flag-data')
                ->usingFileName('flag-'.$language->id.'.webp')
                ->toMediaCollection('flags');
        });
    }
}
