<?php

namespace Database\Factories;

use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventFactory extends Factory
{
    protected $model = Event::class;

    public function definition()
    {
        return [
            'name' => $this->faker->word,
            'description' => $this->faker->text,
            'date' => $this->faker->date,
            'time' => $this->faker->time,
            'price' => $this->faker->randomFloat(2, 10, 100),
            'available_tickets' => $this->faker->numberBetween(10, 100),
            'location' => $this->faker->address,
            'image_url' => $this->faker->imageUrl(),
        ];
    }
}
