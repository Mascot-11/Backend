<?php

namespace Database\Factories;

use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MessageFactory extends Factory
{
    protected $model = Message::class;

    public function definition()
    {
        return [
            'content' => $this->faker->sentence(),
            'user_id' => User::factory(),
            'chat_id' => \App\Models\Chat::factory(),  // Assuming a message belongs to a chat
        ];
    }
}

