<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Apartment;
use App\Models\User;
use App\Models\ApartmentSponsor;
use Illuminate\Support\Facades\File;

class ApartmentTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $apartments = Apartment::factory()->count(30)->make();

        // Ottieni la lista di tutti i file nella cartella "apartments"
        $imageFiles = File::allFiles(storage_path('app/public/apartments'));

        foreach ($apartments as $apartment) {
            $users = User::inRandomOrder()->first();
            $apartment->user_id = $users->id;

            // Seleziona una immagine casuale dalla lista dei file
            $randomImage = $imageFiles[array_rand($imageFiles)];

            // Imposta il percorso dell'immagine come URL dell'appartamento
            $apartment->cover = 'storage/app/public/apartments/' . $randomImage->getFilename();


            $apartment->save();
        }
    }
}
