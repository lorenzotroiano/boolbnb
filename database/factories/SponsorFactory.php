<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class SponsorFactory extends Factory
{

    public function definition()
    {
        $names = ['Economy', 'Standard', 'Premium'];

        // Seleziona un nome casuale dall'array $names
        $name = $this->faker->randomElement($names);

        // Configurazione dei dettagli basata sul nome
        switch ($name) {
            case 'Economy':
                $price = $this->faker->randomFloat(2, 1, 3);
                $duration = $this->faker->numberBetween(1, 3);
                break;
            case 'Standard':
                $price = $this->faker->randomFloat(2, 3, 7);
                $duration = $this->faker->numberBetween(3, 5);
                break;
            case 'Premium':
                $price = $this->faker->randomFloat(2, 7, 10);
                $duration = $this->faker->numberBetween(5, 7);
                break;
        }

        return [
            'name' => $name,
            'price' => $price,
            'duration' => $duration
        ];
    }
}
