<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\StoreBookingRequest;
use App\Http\Requests\UpdateBookingStatusRequest;
use App\Models\ActivityLog;
use App\Models\Booking;
use App\Models\Event;
use App\Services\BookingService;
use App\Services\EventService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EventController extends Controller
{
    public function __construct(
        private EventService $eventService,
        private BookingService $bookingService,
    ) {}

    public function index(Request $request)
    {
        $events = $this->eventService->getAllEvents($request->query('per_page', 10));

        return Inertia::render('Events/Index', [
            'events' => $events,
        ]);
    }

    public function create()
    {
        return Inertia::render('Events/Create');
    }

    public function store(StoreEventRequest $request)
    {
        $this->eventService->createEvent($request->validated());

        return redirect()->route('events.index');
    }

    public function show(Event $event, Request $request)
    {
        $bookings = $this->bookingService->getBookingsForEvent(
            $event,
            $request->query('per_page', 10),
            $request->query('search')
        );

        $activities = ActivityLog::where(function ($q) use ($event) {
            $q->where('subject_type', Event::class)->where('subject_id', $event->id);
        })->orWhere(function ($q) use ($event) {
            $q->where('subject_type', Booking::class)
              ->whereIn('subject_id', $event->bookings()->pluck('id'));
        })->latest()->get();

        return Inertia::render('Events/Show', [
            'event' => $event,
            'bookings' => $bookings,
            'availableSeats' => $event->availableSeats(),
            'bookingProgress' => $event->bookingCapacity(),
            'filters' => $request->only('search'),
            'activities' => $activities,
        ]);
    }

    public function destroy(Event $event)
    {
        $this->eventService->delete($event);

        return redirect()->route('events.index');
    }

    public function storeBooking(Event $event, StoreBookingRequest $request)
    {
        try {
            $this->bookingService->createBooking($event, $request->validated());
            return redirect()->route('events.show', $event)->with('success', 'Booking created successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['seats_booked' => $e->getMessage()]);
        }
    }

    public function updateBookingStatus(Booking $booking, UpdateBookingStatusRequest $request)
    {
        $this->bookingService->updateStatus($booking, $request->validated()['status']);

        return redirect()->route('events.show', $booking->event_id)->with('success', 'Booking status updated.');
    }
}
