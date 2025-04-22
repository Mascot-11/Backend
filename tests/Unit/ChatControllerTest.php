<?php
namespace Tests\Unit;

use App\Models\User;
use App\Models\Chat;
use App\Models\Message;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChatControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_start_chat()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user, 'sanctum')->postJson('/api/chat/start');
        $response->assertStatus(201);
        $response->assertJsonStructure([
            'id', 'user_id', 'admin_id'
        ]);
        $this->assertDatabaseHas('chats', [
            'user_id' => $user->id,
            'admin_id' => null,
        ]);
    }



    public function test_list_chats_for_non_admin()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user, 'sanctum')->getJson('/api/chats');
        $response->assertStatus(403);
    }



    public function test_unauthorized_fetch_messages()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $chat = Chat::factory()->create(['user_id' => $user1->id]);
        $response = $this->actingAs($user2, 'sanctum')->getJson("/api/chat/{$chat->id}/messages");
        $response->assertStatus(403);
    }

    public function test_send_message()
    {
        $user = User::factory()->create();
        $chat = Chat::factory()->create(['user_id' => $user->id]);
        $response = $this->actingAs($user, 'sanctum')->postJson("/api/chat/{$chat->id}/message", [
            'content' => 'New message'
        ]);
        $response->assertStatus(201);
        $response->assertJsonFragment([
            'content' => 'New message',
        ]);
        $this->assertDatabaseHas('messages', [
            'chat_id' => $chat->id,
            'content' => 'New message',
        ]);
    }


}
