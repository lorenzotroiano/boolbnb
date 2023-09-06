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

            'name' => fake()->name(),
            'description' => fake()->paragraph(),
            'room' => fake()->randomDigit(3, 15),
            'bathroom' => fake()->randomDigit(1, 3),
            'mq' => fake()->randomNumber(3, false),
            'address' => fake()->address(),
            'latitude' => fake()->latitude($min = -90, $max = 90),
            'longitude' => fake()->longitude($min = -180, $max = 180),
            'visible' => fake()->boolean(),
            'cover' => fake()->imageUrl(800, 800, true, 'jpg')

        ];
    }
}
