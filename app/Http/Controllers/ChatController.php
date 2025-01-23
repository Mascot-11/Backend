<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\Message;
use Illuminate\Http\Request;
use App\Events\MessageSent;

class ChatController extends Controller
{
    // Start a chat or fetch the existing one for a user
    public function startChat()
    {
        $chat = Chat::firstOrCreate([
            'user_id' => auth()->id(),
            'admin_id' => null, // No admin assigned initially
        ]);

        return response()->json($chat, 201);
    }

    // List all chats (for admin view)
    public function listChats()
{
    // Fetch chats with the user and the latest message for each chat
    $chats = Chat::with(['user', 'messages' => function ($query) {
        $query->latest()->take(1); // Include the latest message for each chat
    }])->get();

    // Modify the chat data to include the latest message content and user name
    $chatsData = $chats->map(function ($chat) {
        return [
            'id' => $chat->id,
            'participant' => $chat->user ? $chat->user->name : 'Unknown User', // Include user name or default to "Unknown User"
            'latest_message' => $chat->messages->isNotEmpty() ? $chat->messages->first()->content : 'No messages yet', // Include the latest message content
        ];
    });

    return response()->json($chatsData);
}


    // Fetch all messages in a chat using id
    public function fetchMessages($chatId)
    {
        $chat = Chat::findOrFail($chatId);

        if (auth()->id() !== $chat->user_id && auth()->user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json($chat->messages()->with('sender')->get());
    }

    // Send a message in a specific chat by id
    public function sendMessage(Request $request, $chatId)
    {
        // Validate the request data
        $request->validate([
            'content' => 'required|string|max:500', // Assuming the message content is a string
        ]);

        // Fetch the chat by ID
        $chat = Chat::findOrFail($chatId);

        // Create a new message
        $message = $chat->messages()->create([
            'sender_id' => auth()->user()->id,
            'content' => $request->content,
        ]);

        // Broadcast the message to the front-end (real-time communication using Laravel Echo)
        broadcast(new MessageSent($chat, $message));

        // Return the sent message as response
        return response()->json(['message' => $message], 201);
    }
}
