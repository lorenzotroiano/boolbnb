<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\View;
use App\Models\Apartment;


class ViewTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $views = View::factory()->count(100)->make();
        foreach ($views as $view) {

            $apartment = Apartment::inRandomOrder()->first();
            

            $view->apartment_id = $apartment->id;

            $view->save();
        }
    }
}
