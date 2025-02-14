<?php

namespace App\Mail;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\Facade\Pdf;

class TicketMail extends Mailable
{
    use Queueable, SerializesModels;
    public $ticket;

    public function __construct(Ticket $ticket) {
        $this->ticket = $ticket;
    }

    public function build() {
        $pdf = Pdf::loadView('pdf.ticket', ['ticket' => $this->ticket]);
        return $this->subject('Your Event Ticket')
                    ->view('emails.ticket')
                    ->attachData($pdf->output(), "ticket.pdf");
    }
}

