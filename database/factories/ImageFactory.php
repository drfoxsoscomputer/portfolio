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
        $imageTypes = ['avatar', 'screenshot', 'logo', 'certificate'];

        return [
            'url' => $this->faker->imageUrl(),
            'alt_text' => $this->faker->optional()->text(50),
            'type' => $this->faker->randomElement($imageTypes),
            'sort_order' => $this->faker->numberBetween(0, 10),
        ];
    }
}