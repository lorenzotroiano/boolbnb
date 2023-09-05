<?php

namespace Database\Seeders;

use App\Models\Apartment;
use App\Models\Service;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ServiceTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Service::factory()->count(10)->create();

        $services = Service::factory()->count(10)->create();

        foreach ($services as $service) {

            $apartments = Apartment::inRandomOrder()->limit(rand(1, 3))->get();

            $service->apartments()->attach($apartments);
        }
    }
}
