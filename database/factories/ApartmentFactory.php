<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Apartment>
 */
class ApartmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [

            'name' => fake()->company(),
            'description' => fake()->paragraph(),
            'room' => fake()->numberBetween(4, 15),
            'bathroom' => fake()->numberBetween(1, 2),
            'mq' => fake()->numberBetween(40, 200),
            'address' => fake()->address(),
            'latitude' => fake()->latitude($min = -90, $max = 90),
            'longitude' => fake()->longitude($min = -180, $max = 180),
            'visible' => fake()->boolean(false),
            'cover' => fake()->imageUrl(800, 800, true, 'jpg')

        ];
    }
}
