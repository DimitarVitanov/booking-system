<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class EventApiTest extends TestCase
{
    use LazilyRefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_can_create_event_with_valid_data(): void
    {
        $payload = [
            'name' => 'Laravel Meetup',
            'description' => 'Monthly gathering for Laravel developers.',
            'start_date' => now()->addDays(5)->format('Y-m-d H:i:s'),
            'end_date' => now()->addDays(5)->addHours(3)->format('Y-m-d H:i:s'),
            'capacity' => 100,
        ];

        $response = $this->actingAs($this->user, 'sanctum')->postJson('/api/events', $payload);

        $response->assertStatus(201)
            ->assertJsonFragment(['name' => 'Laravel Meetup']);

        $this->assertDatabaseHas('events', ['name' => 'Laravel Meetup']);
    }

    public function test_event_creation_fails_when_end_date_is_before_start_date(): void
    {
        $payload = [
            'name' => 'Bad Event',
            'description' => 'This should fail.',
            'start_date' => now()->addDays(5)->format('Y-m-d H:i:s'),
            'end_date' => now()->addDays(4)->format('Y-m-d H:i:s'),
            'capacity' => 50,
        ];

        $response = $this->actingAs($this->user, 'sanctum')->postJson('/api/events', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['end_date']);
    }

    public function test_event_creation_fails_without_required_fields(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')->postJson('/api/events', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'start_date', 'end_date', 'capacity']);
    }

    public function test_can_list_events(): void
    {
        Event::factory()->count(3)->create();

        $response = $this->actingAs($this->user, 'sanctum')->getJson('/api/events');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    public function test_can_view_single_event(): void
    {
        $event = Event::factory()->create(['name' => 'Vue Conf']);

        $response = $this->actingAs($this->user, 'sanctum')->getJson("/api/events/{$event->id}");

        $response->assertStatus(200)
            ->assertJsonFragment(['name' => 'Vue Conf']);
    }

    public function test_can_delete_event(): void
    {
        $event = Event::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')->deleteJson("/api/events/{$event->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('events', ['id' => $event->id]);
    }
}