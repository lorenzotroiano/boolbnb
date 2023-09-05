<?php

namespace Database\Seeders;

use App\Models\Apartment;
use App\Models\Image;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ImageTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
        $images = Image::factory()->count(500)->make();
        foreach ($images as $image) {

            $apartments = Apartment::inRandomOrder()->limit(rand(1, 5))->get();
            
            $apartmentId = $apartments->first()->id;
            $image->apartment_id = $apartmentId;

            $image->save();
        }
    }
}
