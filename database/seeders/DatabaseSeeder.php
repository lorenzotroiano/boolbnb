<?php

namespace Database\Seeders;


use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            MessageTableSeeder::class,
            ViewTableSeeder::class,
            ImageTableSeeder::class,
            SponsorTableSeeder::class,
            ServiceTableSeeder::class,           
            ApartmentTableSeeder::class,
            UserTableSeeder::class,
            ApartmentSponsorTableSeeder::class
        ]);
    }
}
