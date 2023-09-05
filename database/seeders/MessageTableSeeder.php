<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Message;
use App\Models\Apartment;

class MessageTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    // public function run()
    // {
    //     $messages = Message::factory()->count(500)->make();
    //     foreach ($messages as $message) {

    //         $apartment = Apartment::inRandomOrder()->limit(rand(0, 5))->get();
            

    //         $message->apartment_id = $apartment->id;

    //         $message->save();
    //     }
    // }

    public function run() {
        $messages = Message::factory()->count(500)->make();
        foreach ($messages as $message) {
    
            // Get a random number of apartments (between 1 to 5)
            $apartments = Apartment::inRandomOrder()->limit(rand(1, 5))->get();
    
            // Get the ID of the first apartment in the collection
            $apartmentId = $apartments->first()->id;
    
            $message->apartment_id = $apartmentId;
    
            $message->save();
        }
    }
}
