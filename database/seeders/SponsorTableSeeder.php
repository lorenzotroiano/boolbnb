<?php

namespace Database\Seeders;

use App\Models\Sponsor;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SponsorTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function run()
    {
        Sponsor::create([
            'name' => 'Economy',
            'price' => 2.99,
            'duration' => 1,
        ]);

        Sponsor::create([
            'name' => 'Standard',
            'price' => 4.99,
            'duration' => 4,
        ]);

        Sponsor::create([
            'name' => 'Premium',
            'price' => 8.99,
            'duration' => 7,
        ]);
    }
}
