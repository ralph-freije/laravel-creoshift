<?php

namespace Database\Seeders;

use App\Models\Passenger;
use Illuminate\Database\Seeder;

class PassengerSeeder extends Seeder
{
    public function run(): void
    {
        Passenger::factory()->count(1000)->create();
    }
}