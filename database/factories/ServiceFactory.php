<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Service>
 */
class ServiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => fake()->randomElement(['wifi', 'spotify', 'garage', 'youtube', 'condizionatore', 'palestra', 'bar', 'scuola', 'tv', 'idromassaggio', 'colazione', 'animali consentiti']),
            'icon' => fake()->image(null, 80, 80),
        ];
    }
}
