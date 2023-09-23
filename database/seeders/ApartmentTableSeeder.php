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
        $apartments = Apartment::factory()->count(20)->make();

        $possibleExtensions = ['png', 'jpg', 'jpeg', 'webp'];

        foreach ($apartments as $apartment) {
            $user = User::inRandomOrder()->first();
            $apartment->user_id = $user->id;
            $apartment->save(); // Salva l'appartamento per ottenere un ID

            $imageName = null;
            
            // Cicla su ogni possibile estensione
            foreach($possibleExtensions as $extension) {
                $potentialImageName = "images/foto" . $apartment->id . "." . $extension;
                
                if (Storage::disk('public')->exists($potentialImageName)) {
                    $imageName = $potentialImageName;
                    break; // Esce dal ciclo quando trova un'immagine
                }
            }
            
            if ($imageName) {
                $apartment->cover = $imageName;
            } else {
                $apartment->cover = 'percorso/a/un/immagine/default.png';
            }
            
            // Salva di nuovo l'appartamento con l'immagine associata
            $apartment->save();
        }

    }

}
