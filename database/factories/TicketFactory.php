<?php

namespace Database\Factories;

use App\Models\Ticket;
use App\Models\User;
use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;

class TicketFactory extends Factory
{
    protected $model = Ticket::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(), // assuming User factory exists
            'event_id' => Event::factory(), // assuming Event factory exists
            'quantity' => $this->faker->numberBetween(1, 5),
            'status' => $this->faker->randomElement(['paid', 'pending']),
            'pdf_url' => $this->faker->url, // URL for ticket PDF (you can adjust this to fit your structure)
        ];
    }
}

