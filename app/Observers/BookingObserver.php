<?php

namespace App\Observers;

use App\Models\ActivityLog;
use App\Models\Booking;

class BookingObserver
{
    public function created(Booking $booking): void
    {
        ActivityLog::create([
            'log_name' => 'booking',
            'description' => 'created',
            'subject_type' => Booking::class,
            'subject_id' => $booking->id,
            'properties' => ['attributes' => $booking->toArray()],
        ]);
    }

    public function updated(Booking $booking): void
    {
        ActivityLog::create([
            'log_name' => 'booking',
            'description' => 'updated',
            'subject_type' => Booking::class,
            'subject_id' => $booking->id,
            'properties' => [
                'old' => $booking->getOriginal(),
                'new' => $booking->getChanges(),
            ],
        ]);
    }
}
