<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Models\Event;
use Illuminate\Console\Command;

class SeedDemoData extends Command
{
    protected $signature = 'app:demo';

    protected $description = 'Populate the database with 20 demo events and bookings';

    public function handle(): int
    {
        $this->info('Creating 20 demo events with bookings...');

        $bar = $this->output->createProgressBar(20);

        Event::factory(20)->create()->each(function (Event $event) use ($bar) {
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

            $bar->advance();
        });

        $bar->finish();
        $this->newLine(2);
        $this->info('Done! Created 20 events with random bookings.');

        return self::SUCCESS;
    }
}
