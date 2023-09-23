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

        // Lista delle possibili estensioni dell'immagine, come 'png', 'jpeg', 'jpg', etc.
        $possibleExtensions = ['png', 'jpg', 'jpeg', 'webp'];

        foreach ($apartments as $apartment) {
            // La lista delle possibili variazioni dell'immagine, come 'a', 'b', 'c', etc.
            $imageVariations = ['a', 'b', 'c'];
            
            foreach ($imageVariations as $variation) {
                foreach ($possibleExtensions as $extension) {
                    $imagePath = "images/foto" . $apartment->id . $variation . "." . $extension;
                    
                    if (Storage::disk('public')->exists($imagePath)) {
                        Image::create([
                            'apartment_id' => $apartment->id,
                            'image' => $imagePath,
                        ]);

                        // Esce dal ciclo delle estensioni quando trova un'immagine
                        break;
                    }
                }
            }
        }
    }
}

