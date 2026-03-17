<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Event;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $events = Event::factory(20)->create();

        $events->each(function (Event $event) {
            $bookingCount = rand(2, 8);
            $statuses = ['pending', 'confirmed', 'cancelled'];

            for ($i = 0; $i < $bookingCount; $i++) {
                $seats = rand(1, min(5, $event->availableSeats()));
                if ($seats <= 0) break;

                Booking::factory()->create([
                    'event_id' => $event->id,
                    'seats_booked' => $seats,
                    'status' => $statuses[array_rand($statuses)],
                ]);
            }
        });
    }
}
