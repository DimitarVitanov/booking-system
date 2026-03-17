<?php

namespace Database\Factories;

use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventFactory extends Factory
{
    protected $model = Event::class;

    public function definition(): array
    {
        return [
            'name' => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'start_date' => fake()->dateTimeBetween('+1 week', '+1 month'),
            'end_date' => fake()->dateTimeBetween('+1 month', '+2 months'),
            'capacity' => fake()->numberBetween(10, 200),
        ];
    }
}