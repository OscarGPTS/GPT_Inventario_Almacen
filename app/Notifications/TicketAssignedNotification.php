<?php

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TicketAssignedNotification extends Notification
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
            'type'       => 'ticket_assigned',
            'ticket_id'  => $this->ticket->id,
            'codigo'     => $this->ticket->formatted_code,
            'titulo'     => $this->ticket->title,
            'asignado_a' => optional($this->ticket->assignedTo)->name,
            'message'    => 'Tu solicitud "' . $this->ticket->title . '" ha sido asignada a ' . optional($this->ticket->assignedTo)->name,
        ];
    }
}
