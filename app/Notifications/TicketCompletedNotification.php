<?php

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TicketCompletedNotification extends Notification
{
    use Queueable;

    public function __construct(protected Ticket $ticket) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'       => 'ticket_completed',
            'ticket_id'  => $this->ticket->id,
            'codigo'     => $this->ticket->formatted_code,
            'titulo'     => $this->ticket->title,
            'completado_por' => optional($this->ticket->assignedTo)->name,
            'message'    => 'Tu solicitud "' . $this->ticket->title . '" ha sido completada. Por favor califica el servicio.',
        ];
    }
}
