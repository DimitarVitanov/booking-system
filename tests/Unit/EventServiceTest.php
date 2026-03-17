<?php

namespace Tests\Unit;

use App\Models\Event;
use App\Services\EventService;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class EventServiceTest extends TestCase
{
    use LazilyRefreshDatabase;

    private EventService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new EventService();
    }

    public function test_can_create_event(): void
    {
        $event = $this->service->createEvent([
            'name' => 'Test Event',
            'description' => 'Description',
            'start_date' => now()->addDays(5),
            'end_date' => now()->addDays(6),
            'capacity' => 100,
        ]);

        $this->assertInstanceOf(Event::class, $event);
        $this->assertEquals('Test Event', $event->name);
        $this->assertEquals(100, $event->capacity);
    }

    public function test_can_get_event_by_id(): void
    {
        $event = Event::factory()->create();

        $found = $this->service->getEvent($event->id);

        $this->assertEquals($event->id, $found->id);
    }

    public function test_get_event_throws_exception_for_invalid_id(): void
    {
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

        $this->service->getEvent(999);
    }

    public function test_can_delete_event(): void
    {
        $event = Event::factory()->create();

        $this->service->delete($event);

        $this->assertDatabaseMissing('events', ['id' => $event->id]);
    }
}