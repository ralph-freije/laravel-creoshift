<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class FlightFactory extends Factory
{
    public function definition(): array
    {
        $departureTime = fake()->dateTimeBetween('now', '+6 months');
        $arrivalTime = (clone $departureTime)->modify('+' . fake()->numberBetween(1, 12) . ' hours');

        return [
            'number' => fake()->unique()->bothify('FL-####'),
            'departure_city' => fake()->city(),
            'arrival_city' => fake()->city(),
            'departure_time' => $departureTime,
            'arrival_time' => $arrivalTime,
        ];
    }
}