<?php

namespace Database\Seeders;

use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Models\User;
use Illuminate\Database\Seeder;

class TicketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tickets = Ticket::all();
        $users = User::all();

        $tickets->each(function (Ticket $ticket) use ($users) {
            TicketMessage::factory()->count(10)->state([
                'ticket_id' => $ticket->id,
                'user_id' => $users->random()->id
            ])->create();
        });
    }
}
