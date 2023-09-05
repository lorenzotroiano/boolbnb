<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Apartment;
use App\Models\Image;
use App\Models\Message;
use App\Models\View;

class ApartmentTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $apartments = Apartment::factory()->count(50)->make();
        foreach ($apartments as $apartment) {

            $image = Image::inRandomOrder()->first();
            $message = Message::inRandomOrder()->first();
            $view = View::inRandomOrder()->first();

            $apartment->image_id = $image->id;
            $apartment->message_id = $message->id;
            $apartment->view_id = $view->id;

            $apartment->save();
    }
}

}