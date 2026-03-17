<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Event;

class BookingService
{
    public function getBookingsForEvent(Event $event, int $limit = 10, ?string $search = null)
    {
        $query = $event->bookings()->latest();

        if($search){
            $query->where('email_address', 'like', "%{$search}%");      
        }
        return $query->paginate($limit);
    }

    public function createBooking(Event $event, array $data): Booking
    {
        if($data['seats_booked'] > $event->availableSeats()) {
            throw new \Exception('Not enough seats available');
        }

        return $event->bookings()->create($data);
    }

    public function updateStatus(Booking $booking, string $status): Booking
    {
        $booking->update(['status' => $status]);
        return $booking->fresh();
    }
}
