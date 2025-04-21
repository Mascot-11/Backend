<?php

namespace Database\Factories;

use App\Models\Payment;
use App\Models\User;
use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(), // assuming User factory exists
            'event_id' => Event::factory(), // assuming Event factory exists
            'price' => $this->faker->randomFloat(2, 10, 100),
            'quantity' => $this->faker->numberBetween(1, 5),
            'total_amount' => $this->faker->randomFloat(2, 100, 500),
            'transaction_id' => $this->faker->uuid,
            'payment_method' => $this->faker->randomElement(['Khalti', 'Cash', 'Card']),
            'status' => $this->faker->randomElement(['Completed', 'Pending', 'Failed']),
        ];
    }
}
