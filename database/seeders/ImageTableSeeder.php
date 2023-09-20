<?php

namespace Database\Seeders;

use App\Models\Apartment;
use App\Models\Image;
use Illuminate\Support\Facades\Storage;
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
        // Recupera tutti gli appartamenti
        $apartments = Apartment::all();

        // Suppongo che tu abbia le immagini ordinate in qualche modo 
        // (ad es., "images/app1/1.jpg", "images/app1/2.jpg", ecc.).
        // Se non è così, dovresti adattare il codice di seguito.
        
        foreach ($apartments as $apartment) {
            for ($i = 1; $i <= 3; $i++) {
                $imagePath = "images/app" . $apartment->id . "/" . $i . ".jpg";
                if (Storage::disk('public')->exists($imagePath)) {
                    Image::create([
                        'apartment_id' => $apartment->id,
                        'path' => $imagePath,
                    ]);
                }
            }
        }
    }
}
