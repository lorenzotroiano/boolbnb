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
        
        $images = Image::factory()->count(100)->make();
        foreach ($images as $image) {

            $apartment = Apartment::inRandomOrder()->first();
            

            $image->apartment_id = $apartment->id;

            $image->save();
        }
    }
}
