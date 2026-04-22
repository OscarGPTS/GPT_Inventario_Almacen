<?php

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TicketPendingApprovalNotification extends Notification
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
            'type'        => 'ticket_pending_approval',
            'ticket_id'   => $this->ticket->id,
            'codigo'      => $this->ticket->formatted_code,
            'titulo'      => $this->ticket->title,
            'solicitante' => optional($this->ticket->user)->name,
            'message'     => 'Nueva solicitud pendiente de ' . optional($this->ticket->user)->name . ': "' . $this->ticket->title . '"',
        ];
    }
}
