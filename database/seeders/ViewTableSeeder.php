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
        $views = View::factory()->count(5000)->make();
        foreach ($views as $view) {

            $apartments = Apartment::inRandomOrder()->limit(rand(1, 50))->get();
            
            $apartmentId = $apartments->first()->id;
    
            $view->apartment_id = $apartmentId;

            $view->save();
        }
    }
}
