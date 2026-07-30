<?php

namespace Database\Factories;

use App\Models\Image;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Image>
 */
class ImageFactory extends Factory
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
        $imageTypes = ['avatar', 'screenshot', 'logo', 'certificate'];

        return [
            'url' => $this->faker->imageUrl(),
            'alt_text' => $this->faker->optional()->text(50),
            'type' => $this->faker->randomElement($imageTypes),
            'sort_order' => $this->faker->numberBetween(0, 10),
        ];
    }
}