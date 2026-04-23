<?php

namespace App\Notifications;

use App\Models\Solicitud;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NuevaSolicitudNotification extends Notification
{
    use Queueable;

    public function __construct(protected Solicitud $solicitud) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'       => 'nueva_solicitud',
            'folio'      => $this->solicitud->folio,
            'solicitante'=> $this->solicitud->solicitante,
            'producto'   => optional($this->solicitud->producto)->descripcion,
            'message'    => 'Nueva requisición ' . $this->solicitud->folio . ' de ' . $this->solicitud->solicitante,
        ];
    }
}
