<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Apartment;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
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
        // Crei 30 appartamenti con Apartment::factory() e li salvi nel database
        $apartments = Apartment::factory()->count(20)->create();

        foreach ($apartments as $apartment) {
            // Ottieni il primo utente in ordine casuale
            $user = User::inRandomOrder()->first();
            $apartment->user_id = $user->id;

            // Costruisci il nome del file immagine basato sull'id dell'appartamento
            $imageName = "images/foto" . $apartment->id;

            // Controlla se l'immagine esiste
            if (Storage::disk('public')->exists($imageName)) {
                // Imposta il percorso dell'immagine come URL dell'appartamento
                $apartment->cover = $imageName;
            }

            // Salva le modifiche fatte all'appartamento
            $apartment->save();
        }
    }

}
