<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class PassengerFactory extends Factory
{
    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'password' => Hash::make('password'),
            'dob' => fake()->dateTimeBetween('-70 years', '-18 years')->format('Y-m-d'),
            'passport_expiry_date' => fake()->dateTimeBetween('now', '+10 years')->format('Y-m-d'),
        ];
    }
}