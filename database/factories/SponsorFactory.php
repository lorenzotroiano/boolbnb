<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Sponsor>
 */
class SponsorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        
        $names = ['Economy', 'Standard', 'Premium'];

        $sponsors = [];

        for ($i=0; $i < count($names) ; $i++) { 
            
            $sponsors = [
                'name' => $names[$i],
                'price' => fake()->randomFloat(2, 1, 10),
                'duration' => fake()->numberBetween(1, 7)
            ];
        }
        
        return $sponsors;

        // return [
        //     'name' => fake()->randomElement(['base', 'medio', 'premium']),
        //     'price' => fake()->randomFloat(2, 1, 10),
        //     'duration' => fake()->numberBetween(1, 7)
        // ];
    }
}
