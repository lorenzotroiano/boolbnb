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
        $apartmentSponsors = ApartmentSponsor::factory()->create();

        foreach ($apartmentSponsors as $apartmentSponsor) {

            $apartments = Apartment::inRandomOrder()->limit(rand(0, 3))->get();
            $sponsors = Sponsor::inRandomOrder()->limit(rand(0, 3))->get();
            

            $apartmentSponsor->apartments()->attach($apartments);
            $apartmentSponsor->sponsors()->attach($sponsors);
            
        }
    }
}
