<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookingFactory extends Factory
{
    protected $model = Booking::class;

    public function definition(): array
    {
        return [
            'event_id' => Event::factory(),
            'email_address' => fake()->safeEmail(),
            'seats_booked' => fake()->numberBetween(1, 5),
            'status' => 'pending',
        ];
    }

    public function confirmed(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'confirmed',
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'cancelled',
        ]);
    }
}
