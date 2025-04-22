<?php

namespace Tests\Unit;

use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class EventControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that the index route returns all events.
     *
     * @return void
     */
    public function test_index()
    {
        // Create some events
        Event::factory()->count(3)->create();

        // Test the route
        $response = $this->getJson('/api/events');

        $response->assertStatus(200)
                 ->assertJsonCount(3);
    }

    /**
     * Test that the store route creates an event.
     *
     * @return void
     */

    /**
     * Test that the show route returns a single event.
     *
     * @return void
     */
    public function test_show()
{
    // Create a user and authenticate
    $user = User::factory()->create();
    $this->actingAs($user, 'sanctum'); // or 'api' if you use token-based auth

    // Create an event
    $event = Event::factory()->create();

    // Test the route
    $response = $this->getJson("/api/events/{$event->id}");

    $response->assertStatus(200)
             ->assertJsonFragment(['name' => $event->name]);
}

    /**
     * Test that the update route updates an event.
     *
     * @return void
     */

    /**
     * Test that the destroy route deletes an event.
     *
     * @return void
     */
    public function test_destroy()
    {
        // Create an event
        $event = Event::factory()->create();

        // Create a user and authenticate
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        // Test the delete route
        $response = $this->deleteJson("/api/events/{$event->id}");

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Event deleted']);

        // Assert the event was deleted
        $this->assertDatabaseMissing('events', ['id' => $event->id]);
    }

    /**
     * Test validation errors when invalid data is passed to store.
     *
     * @return void
     */
    public function test_store_validation_fail()
    {
        // Create a user and authenticate
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $data = [
            'name' => '', // Invalid name
            'description' => 'Test event description.',
            'date' => '2025-04-22',
            'time' => '18:00',
            'price' => 20,
            'available_tickets' => 100,
            'location' => 'Test Location',
            'image' => null, // Missing image
        ];

        $response = $this->postJson('/api/events', $data);

        // Assert validation errors
        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['name', 'image']);
    }
}
