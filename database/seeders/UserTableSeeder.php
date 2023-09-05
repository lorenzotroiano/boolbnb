<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\User;
use App\Models\Apartment;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = User::factory()->count(50)->create();

        foreach ($users as $user) {

            $apartments = Apartment::inRandomOrder()->limit(rand(0, 3))->get();
            
            $user->apartment_id = $apartments->id;

            $user->save();
            
        }
    }
}
