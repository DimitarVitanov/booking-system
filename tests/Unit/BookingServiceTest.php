<?php

namespace Tests\Unit;

use App\Models\Booking;
use App\Models\Event;
use App\Services\BookingService;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class BookingServiceTest extends TestCase
{
    use LazilyRefreshDatabase;

    private BookingService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new BookingService();
    }

    public function test_can_create_booking_within_capacity(): void
    {
        $event = Event::factory()->create(['capacity' => 50]);

        $booking = $this->service->createBooking($event, [
            'email_address' => 'test@example.com',
            'seats_booked' => 5,
        ]);

        $this->assertInstanceOf(Booking::class, $booking);
        $this->assertEquals('test@example.com', $booking->email_address);
        $this->assertEquals(5, $booking->seats_booked);
    }

    public function test_cannot_create_booking_exceeding_capacity(): void
    {
        $event = Event::factory()->create(['capacity' => 10]);

        Booking::factory()->confirmed()->create([
            'event_id' => $event->id,
            'seats_booked' => 8,
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Not enough seats available');

        $this->service->createBooking($event, [
            'email_address' => 'overflow@example.com',
            'seats_booked' => 5,
        ]);
    }

    public function test_can_update_booking_status(): void
    {
        $booking = Booking::factory()->create(['status' => 'pending']);

        $updated = $this->service->updateStatus($booking, 'confirmed');

        $this->assertEquals('confirmed', $updated->status);
    }
}