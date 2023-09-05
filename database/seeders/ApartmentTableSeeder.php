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

        $apartments = Apartment::factory()->count(100)->make();
        foreach ($apartments as $apartment) {

            $image = Image::inRandomOrder()->limit(rand(1, 20))->get();
            $message = Message::inRandomOrder()->limit(rand(1, 20))->get();
            $view = View::inRandomOrder()->limit(rand(1, 500))->get();

            $apartment->image_id = $image->id;
            $apartment->message_id = $message->id;
            $apartment->view_id = $view->id;

            $apartment->save();
    }
}

}