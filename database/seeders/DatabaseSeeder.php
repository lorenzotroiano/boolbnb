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
            UserTableSeeder::class,
            ApartmentTableSeeder::class,
            MessageTableSeeder::class,
            ViewTableSeeder::class,
            ImageTableSeeder::class,
            SponsorTableSeeder::class,
            ServiceTableSeeder::class,
            ApartmentSponsorTableSeeder::class
        ]);
    }
}
