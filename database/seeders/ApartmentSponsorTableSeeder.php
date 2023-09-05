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
        $apartmentSponsors = ApartmentSponsor::factory()->count(5)->make();

        foreach ($apartmentSponsors as $apartmentSponsor) {

            $apartments = Apartment::inRandomOrder()->first();
            $sponsors = Sponsor::inRandomOrder()->first();
            

            $apartmentSponsor->apartment_id = $apartments->id;
            $apartmentSponsor->sponsor_id = $sponsors->id;

            $apartmentSponsor->save();
            
        }
    }
}
