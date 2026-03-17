<?php

namespace App\Console\Commands;

use App\Models\ActivityLog;
use App\Models\Booking;
use App\Models\Event;
use Illuminate\Console\Command;

class ClearDemoData extends Command
{
    protected $signature = 'app:clear';

    protected $description = 'Remove all events and bookings from the database';

    public function handle(): int
    {
        if (!$this->confirm('This will delete ALL events and bookings. Continue?')) {
            $this->info('Cancelled.');
            return self::SUCCESS;
        }

        $activities = ActivityLog::query()->delete();
        $bookings = Booking::query()->delete();
        $events = Event::query()->delete();

        $this->info("Deleted {$events} events, {$bookings} bookings, and {$activities} activity logs.");

        return self::SUCCESS;
    }
}
