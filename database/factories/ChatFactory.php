<?php

namespace Database\Factories;

use App\Models\Chat;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ChatFactory extends Factory
{
    protected $model = Chat::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(), // assuming the user is a foreign key
            'admin_id' => null, // or set a default admin ID if needed
        ];
    }
}

