<?php

namespace Database\Seeders;

use App\Models\Flight;
use App\Models\Passenger;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Passenger::factory()->count(1000)->create();
        Flight::factory()->count(50)->create();

        $passengers = Passenger::all();

        Flight::all()->each(function ($flight) use ($passengers) {
            $flight->passengers()->attach(
                $passengers->random(rand(10, 40))->pluck('id')->toArray()
            );
        });
    }
}