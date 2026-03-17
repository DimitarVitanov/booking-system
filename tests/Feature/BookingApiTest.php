<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class BookingApiTest extends TestCase
{
    use LazilyRefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_can_create_booking_within_capacity(): void
    {
        $event = Event::factory()->create(['capacity' => 50]);

        $payload = [
            'email_address' => 'john@example.com',
            'seats_booked' => 5,
        ];

        $response = $this->actingAs($this->user, 'sanctum')->postJson("/api/events/{$event->id}/bookings", $payload);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'email_address' => 'john@example.com',
                'seats_booked' => 5,
                'status' => 'pending',
            ]);

        $this->assertDatabaseHas('bookings', [
            'event_id' => $event->id,
            'email_address' => 'john@example.com',
        ]);
    }

    public function test_booking_fails_when_exceeding_capacity(): void
    {
        $event = Event::factory()->create(['capacity' => 10]);

        Booking::factory()->confirmed()->create([
            'event_id' => $event->id,
            'seats_booked' => 8,
        ]);

        $payload = [
            'email_address' => 'overflow@example.com',
            'seats_booked' => 5,
        ];

        $response = $this->actingAs($this->user, 'sanctum')->postJson("/api/events/{$event->id}/bookings", $payload);

        $response->assertStatus(422);
    }

    public function test_cancelled_bookings_free_up_capacity(): void
    {
        $event = Event::factory()->create(['capacity' => 10]);

        Booking::factory()->cancelled()->create([
            'event_id' => $event->id,
            'seats_booked' => 10,
        ]);

        $payload = [
            'email_address' => 'free@example.com',
            'seats_booked' => 10,
        ];

        $response = $this->actingAs($this->user, 'sanctum')->postJson("/api/events/{$event->id}/bookings", $payload);

        $response->assertStatus(201);
    }

    public function test_can_update_booking_status_to_confirmed(): void
    {
        $booking = Booking::factory()->create(['status' => 'pending']);

        $response = $this->actingAs($this->user, 'sanctum')->patchJson("/api/bookings/{$booking->id}", [
            'status' => 'confirmed',
        ]);

        $response->assertStatus(200)
            ->assertJsonFragment(['status' => 'confirmed']);

        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'status' => 'confirmed',
        ]);
    }

    public function test_cannot_update_booking_with_invalid_status(): void
    {
        $booking = Booking::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')->patchJson("/api/bookings/{$booking->id}", [
            'status' => 'expired',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['status']);
    }

    public function test_can_list_bookings_for_event(): void
    {
        $event = Event::factory()->create();
        Booking::factory()->count(4)->create(['event_id' => $event->id]);

        $response = $this->actingAs($this->user, 'sanctum')->getJson("/api/events/{$event->id}/bookings");

        $response->assertStatus(200)
            ->assertJsonCount(4, 'data');
    }
}