<?php

namespace App\Mail;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TicketSurveyMail extends Mailable
{
    use Queueable, SerializesModels;

    public int $rating;
    public ?string $comments;

    public function __construct(public Ticket $ticket, int $rating, ?string $comments = null)
    {
        $this->rating = $rating;
        $this->comments = $comments;
    }

    public function envelope(): Envelope
    {
        $code = $this->ticket->formatted_code;
        return new Envelope(
            subject: "Encuesta de Satisfacción — Ticket #{$code} — {$this->rating}/5 ★"
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.tickets.survey',
        );
    }
}
