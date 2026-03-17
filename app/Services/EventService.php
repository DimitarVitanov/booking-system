<?php 

namespace App\Services;

use App\Models\Event;

class EventService
{
    public function getEvent(int $id): Event
    {
        return Event::findOrFail($id);
    }
    
    public function getAllEvents(int $limit = 10)
    {
        return Event::withSum(
            ['bookings as active_seats_sum' => fn($q) => $q->whereIn('status', ['confirmed', 'pending'])],
            'seats_booked'
        )->latest()->paginate($limit);
    }

    public function createEvent(array $data): Event
    {
        return Event::create($data);
    } 

    public function delete(Event $event): void
    {
        $event->delete();
    }
}
