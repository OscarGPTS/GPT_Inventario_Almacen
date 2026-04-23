<?php

namespace App\Mail;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TicketCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Ticket $ticket) {}

    public function envelope(): Envelope
    {
        $code = $this->ticket->formatted_code;
        return new Envelope(
            subject: "Nueva Solicitud de Movimiento — #{$code} — {$this->ticket->user->name}"
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.tickets.created',
        );
    }
}
