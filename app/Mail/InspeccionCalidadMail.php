<?php

namespace App\Mail;

use App\Models\InspeccionIngreso;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InspeccionCalidadMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public InspeccionIngreso $inspeccion) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Nueva Inspección Requiere Control de Calidad — ' . $this->inspeccion->folio
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.inspeccion-calidad',
        );
    }
}
