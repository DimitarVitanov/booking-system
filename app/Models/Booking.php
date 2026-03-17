<?php

namespace App\Models;

use App\Observers\BookingObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[ObservedBy(BookingObserver::class)]
class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'email_address',
        'seats_booked',
        'status',
    ];

    protected $attributes = [
        'status' => 'pending',
    ];

    protected $casts = [
        'seats_booked' => 'integer',
        'status' => 'string',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
