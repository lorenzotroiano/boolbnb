<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ApartmentSponsor>
 */
class ApartmentSponsorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'start_date' => fake()->dateTimeBetween('-4 year', 'now'),
            'end_date' => fake()->dateTimeBetween('now', '+10 year'),
        ];
    }
}
