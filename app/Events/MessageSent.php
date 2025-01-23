<?php

namespace App\Events;

use App\Models\Chat;
use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $chat;
    public $message;

    public function __construct(Chat $chat, Message $message)
    {
        $this->chat = $chat;
        $this->message = $message;
    }

    public function broadcastOn()
    {
        return new Channel('chat.' . $this->message->chat_id);
    }

    public function broadcastWith()
{
    return [
        'id' => $this->message->id,
        'sender' => [
            'id' => $this->message->sender->id,
            'name' => $this->message->sender->name,
        ],
        'content' => $this->message->content,
    ];
}
 public function broadcastAs()
    {
        return 'MessageSent';
    }
}


