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
    // public function run()
    // {
    //     Service::create([
    //         'name' => 'Wifi',
    //         'icon' => 'nome_del_file_icona_wifi.png',
    //     ]);

    //     Service::create([
    //         'name' => 'Spotify',
    //         'icon' => 'nome_del_file_icona_spotify.png',
    //     ]);

    //     Service::create([
    //         'name' => 'Tv',
    //         'icon' => 'Tv.png',
    //     ]);

    //     Service::create([
    //         'name' => 'FrigoBar',
    //         'icon' => 'FrigoBar.png',
    //     ]);

    //     Service::create([
    //         'name' => 'Idromassaggio',
    //         'icon' => 'Idromassaggio.png',
    //     ]);

    //     Service::create([
    //         'name' => 'Garage',
    //         'icon' => 'Garage.png',
    //     ]);

    //     Service::create([
    //         'name' => 'Animali',
    //         'icon' => 'Animali.png',
    //     ]);

    //     Service::create([
    //         'name' => 'Bevande',
    //         'icon' => 'Bevande.png',
    //     ]);


    //     Service::create([
    //         'name' => 'Palestra',
    //         'icon' => 'Palestra.png',
    //     ]);

    //     Service::create([
    //         'name' => 'Bar',
    //         'icon' => 'Bar.png',
    //     ]);

    //     // Aggiungi altri servizi desiderati qui...

    //     $apartments = Apartment::inRandomOrder()->limit(rand(1, 3))->get();
    //     $services = Service::whereIn('name', ['Wifi', 'Spotify'])->get();

    //     foreach ($services as $service) {
    //         $service->apartments()->attach($apartments);
    //     }
    // }


    public function run()
    {
        // Definisci un array di dati dei servizi desiderati
        $serviceData = [
            ['name' => 'Wifi', 'icon' => 'nome_del_file_icona_wifi.png'],
            ['name' => 'Spotify', 'icon' => 'nome_del_file_icona_spotify.png'],
            ['name' => 'Tv', 'icon' => 'Tv.png'],
            ['name' => 'FrigoBar', 'icon' => 'FrigoBar.png'],
            ['name' => 'Idromassaggio', 'icon' => 'Idromassaggio.png'],
            ['name' => 'Garage', 'icon' => 'Garage.png'],
            ['name' => 'Animali', 'icon' => 'Animali.png'],
            ['name' => 'Bevande', 'icon' => 'Bevande.png'],
            ['name' => 'Palestra', 'icon' => 'Palestra.png'],
            ['name' => 'Bar', 'icon' => 'Bar.png'],
            // Puoi aggiungere altri servizi desiderati qui...
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
