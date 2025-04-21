<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Event;
use App\Models\Payment;
use App\Models\Ticket;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Create 10 events using the EventFactory
        Event::factory(10)->create();

        // Create 10 payments using the PaymentFactory
        Payment::factory(10)->create();

        // Create 10 tickets using the TicketFactory
        Ticket::factory(10)->create();
    }
}

