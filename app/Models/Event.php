<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Observers\EventObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;

#[ObservedBy(EventObserver::class)]
class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'start_date',
        'end_date',
        'capacity',
    ];

    protected $casts = [
        'capacity' => 'integer',
        'start_date' => 'datetime:Y-m-d H:i:s',
        'end_date' => 'datetime:Y-m-d H:i:s',
    ];

    protected $appends = [
        'available_seats',
        'booking_progress',
        'booked_seats',
    ];

    public function getAvailableSeatsAttribute(): int
    {
        return $this->availableSeats();
    }

    public function getBookingProgressAttribute(): float
    {
        return $this->bookingCapacity();
    }

    public function getBookedSeatsAttribute(): int
    {
        return $this->totalBookedSeats();
    }
    
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function availableSeats()
    {
        return max($this->capacity - $this->totalBookedSeats(), 0);
    }

    /* Total number of confirmed and pending seats already booked */
    public function totalBookedSeats()
    {
        if (isset($this->attributes['active_seats_sum'])) {
            return (int) $this->attributes['active_seats_sum'];
        }

        return (int) $this->bookings()->whereIn('status', ['confirmed', 'pending'])->sum('seats_booked');
    }

    /* Percentage of booking capacity */
    public function bookingCapacity()
    {
        return round(($this->totalBookedSeats() / $this->capacity) * 100, 1);
    }

}
