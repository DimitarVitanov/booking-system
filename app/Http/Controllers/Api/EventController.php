<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Services\EventService;
use App\Http\Requests\StoreEventRequest;
use App\Http\Resources\EventResource;
use Illuminate\Http\Request;


class EventController extends Controller
{
    protected EventService $events;

    public function __construct(EventService $events)
    {
        $this->events = $events;
    }

    // Get all events 
    public function index(Request $request)
    {
        $all_events = $this->events->getAllEvents($request->query('per_page', 10));

        return EventResource::collection($all_events);
    }

    // Create new event
    public function store(StoreEventRequest $request)
    {
        $event = $this->events->createEvent($request->validated());
        return new EventResource($event);
    }

    // Get single event
    public function show(int $id)
    {
        $event = $this->events->getEvent($id);
        return new EventResource($event);
    }

    // Delete event
    public function destroy(int $id)
    {
       $event = $this->events->getEvent($id);
       $this->events->delete($event);

       return response()->json(['message' => 'Event deleted successfully'],200);
    }
}
