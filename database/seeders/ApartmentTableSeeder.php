<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Apartment;
use App\Models\User;

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

        foreach ($apartments as $apartment) {

            $users = User::inRandomOrder()->first();

            $apartment->user_id = $users->id;

            $apartment->save();
        }
    }
}
