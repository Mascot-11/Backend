<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Event;
use App\Models\Payment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Ticket;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Http;

class TicketControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;


    // Test for Khalti callback (Success case)
    public function test_handle_khalti_callback_success()
    {
        $user = User::factory()->create();
        $event = Event::factory()->create(['price' => 100]);

        // Simulate successful payment data
        $paymentData = [
            'status' => 'Completed',
            'purchase_order_id' => $event->id,
            'user_id' => $user->id,
            'total_amount' => 200, // For 2 tickets
            'transaction_id' => '123456789',
        ];

        // Check the route is correct and exists
        $response = $this->postJson('/api/khalti/callback', $paymentData);

        $response->assertStatus(200);
        $response->assertJsonStructure(['message', 'user', 'event', 'paymentDetails']);
    }

    // Test for ticket details
    public function test_get_ticket_details()
    {
        $user = User::factory()->create();
        $event = Event::factory()->create();
        $ticket = Ticket::create([
            'user_id' => $user->id,
            'event_id' => $event->id,
            'quantity' => 1,
            'status' => 'paid'
        ]);

        // Correct URL path for ticket details
        $response = $this->actingAs($user, 'sanctum')
            ->getJson("/api/ticket-details/{$ticket->id}");  // Fixed URL for getting ticket details

        $response->assertStatus(200);
        $response->assertJsonStructure(['ticket']);
    }

    // Test for unsuccessful Khalti callback
    public function test_handle_khalti_callback_failure()
    {
        // Simulating failed payment data
        $paymentData = [
            'status' => 'Failed',
            'purchase_order_id' => 1,
            'user_id' => 1,
            'total_amount' => 100,
            'transaction_id' => '123456789',
        ];

        // Simulate the callback with failed status
        $response = $this->postJson('/api/khalti/callback', $paymentData);

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Transaction failed, Payment for event not confirmed',
        ]);
    }
}
