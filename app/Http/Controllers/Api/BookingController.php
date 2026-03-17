<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Event;
use App\Services\BookingService;
use App\Http\Requests\StoreBookingRequest;
use App\Http\Requests\UpdateBookingStatusRequest;
use App\Http\Resources\BookingResource;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    protected BookingService $bookings;

    public function __construct(BookingService $bookings)
    {
        $this->bookings = $bookings;
    }

    // Get all bookings 
    public function index(Event $event, Request $request)
    {
        $all_bookings = $this->bookings->getBookingsForEvent($event, $request->query('per_page', 10), $request->query('search'));
        return BookingResource::collection($all_bookings);
    }

    // Create new booking 
    public function store(Event $event, StoreBookingRequest $request)
    {
        try {
            $bookings = $this->bookings->createBooking($event, $request->validated());
            return new BookingResource($bookings);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    // Update booking status
    public function updateStatus(Booking $booking, UpdateBookingStatusRequest $request)
    {
        $originalStatus = $booking->status;

        try {
            $booking = $this->bookings->updateStatus($booking, $request->validated()['status']);
            return new BookingResource($booking);
        } catch (\Exception $e) {
            $booking->update(['status' => $originalStatus]);
            return response()->json(['message' => 'Failed to update booking status.'], 500);
        }
    }
}
