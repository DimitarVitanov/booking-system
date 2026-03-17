<?php

namespace App\Observers;

use App\Models\ActivityLog;
use App\Models\Event;

class EventObserver
{
    public function created(Event $event): void
    {
        ActivityLog::create([
            'log_name' => 'event',
            'description' => 'created',
            'subject_type' => Event::class,
            'subject_id' => $event->id,
            'properties' => ['attributes' => $event->toArray()],
        ]);
    }

    public function updated(Event $event): void
    {
        ActivityLog::create([
            'log_name' => 'event',
            'description' => 'updated',
            'subject_type' => Event::class,
            'subject_id' => $event->id,
            'properties' => [
                'old' => $event->getOriginal(),
                'new' => $event->getChanges(),
            ],
        ]);
    }

    public function deleting(Event $event): void
    {
        $event->bookings()
            ->where('status', 'pending')
            ->update(['status' => 'cancelled']);

        ActivityLog::create([
            'log_name' => 'event',
            'description' => 'deleted',
            'subject_type' => Event::class,
            'subject_id' => $event->id,
            'properties' => ['attributes' => $event->toArray()],
        ]);
    }
}
