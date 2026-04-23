<?php

namespace App\Mail;

use App\Models\Solicitud;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NuevaSolicitudMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Solicitud $solicitud) {}

    public function envelope(): Envelope
    {
        $folio = $this->solicitud->folio ?? '(sin folio)';
        $solicitante = $this->solicitud->solicitante;

        return new Envelope(
            subject: "Nueva Solicitud de Material — {$folio} — {$solicitante}"
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.nueva-solicitud',
        );
    }
}
