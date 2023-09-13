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
        // Definisci un array di dati dei servizi desiderati
        $serviceData = [
            ['name' => 'Wifi', 'icon' => 'fas fa-wifi'],
            ['name' => 'Spotify', 'icon' => 'fas fa-music'],
            ['name' => 'Tv', 'icon' => 'fas fa-tv'],
            ['name' => 'FrigoBar', 'icon' => 'fas fa-cocktail'],
            ['name' => 'Idromassaggio', 'icon' => 'fas fa-bath'],
            ['name' => 'Garage', 'icon' => 'fas fa-parking'],
            ['name' => 'Animali', 'icon' => 'fas fa-dog'],
            ['name' => 'Bevande', 'icon' => 'fas fa-coffee'],
            ['name' => 'Palestra', 'icon' => 'fas fa-dumbbell'],
            ['name' => 'Bar', 'icon' => 'fas fa-beer'],

        ];

        // Loop attraverso l'array dei dati dei servizi e crea i servizi nel database
        foreach ($serviceData as $data) {
            Service::create($data);
        }

        // Ottieni appartamenti casuali (da 1 a 3)
        $apartments = Apartment::inRandomOrder()->limit(rand(1, 3))->get();

        // Estrai i nomi dei servizi dall'array $serviceData
        $serviceNames = array_column($serviceData, 'name');

        // Recupera i servizi corrispondenti dal database in base ai nomi estratti
        $services = Service::whereIn('name', $serviceNames)->get();

        // Associa i servizi estratti agli appartamenti
        foreach ($services as $service) {
            $service->apartments()->attach($apartments);
        }
    }
}
