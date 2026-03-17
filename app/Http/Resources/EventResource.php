<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'capacity' => $this->capacity,
            'available_seats' => $this->availableSeats(),
            'booking_progress' => $this->bookingCapacity(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
