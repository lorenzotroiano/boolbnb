<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ApartmentSponsor;
use App\Models\Apartment;
use App\Models\Sponsor;

class ApartmentSponsorTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Genero gli id relativi alla tabella ponte
        $apartmentSponsors = ApartmentSponsor::factory()->count(5)->make();

        foreach ($apartmentSponsors as $apartmentSponsor) {
            $randomApartment = Apartment::inRandomOrder()->first();
            $randomSponsor = Sponsor::inRandomOrder()->first();

            $apartmentSponsor->apartment_id = $randomApartment->id;
            $apartmentSponsor->sponsor_id = $randomSponsor->id;
            $apartmentSponsor->save();

            // Modifica il valore visible subito dopo aver salvato l'ApartmentSponsor
            $randomApartment->sponsor = true;
            $randomApartment->save();
        }
    }
}
