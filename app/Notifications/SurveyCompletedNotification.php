<?php

namespace App\Notifications;

use App\Models\Survey;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SurveyCompletedNotification extends Notification
{
    use Queueable;

    public function __construct(protected Survey $survey) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $ticket = $this->survey->ticket;

        return [
            'type'       => 'survey_completed',
            'survey_id'  => $this->survey->id,
            'ticket_id'  => $ticket->id,
            'codigo'     => $ticket->formatted_code,
            'titulo'     => $ticket->title,
            'rating'     => $this->survey->rating,
            'solicitante' => optional($ticket->user)->name,
            'message'    => optional($ticket->user)->name . ' calificó la solicitud "' . $ticket->title . '" con ' . $this->survey->rating . ' estrellas',
        ];
    }
}
