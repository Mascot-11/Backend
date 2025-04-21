<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\Facade\Pdf;

class TicketMail extends Mailable
{
    use SerializesModels;
    public $payment;
    public $pdfPath;
    public $userData;

    public function __construct($payment, $pdfPath, $userData)
    {
        $this->payment = $payment;
        $this->pdfPath = $pdfPath;
        $this->userData = $userData;
    }


    public function build()
    {
        return $this->to($this->userData->email)
            ->subject('Your Event Ticket')
            ->view('emails.ticket')
            ->attach($this->pdfPath, [
                 'as' => 'Your_Ticket.pdf',
                 'mime' => 'application/pdf',
             ]);
    }
}
