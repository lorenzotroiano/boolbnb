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

        foreach ($apartments as $apartment) {
            // La lista delle possibili estensioni dell'immagine, come 'a', 'b', 'c', etc.
            $imageExtensions = ['a', 'b', 'c'];
            
            foreach ($imageExtensions as $extension) {
                $imagePath = "images/foto" . $apartment->id . $extension;
                
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
