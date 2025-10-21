<?php

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class TicketAssignedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Ticket $ticket)
    {
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['database', WebPushChannel::class];
    }

    /**
     * Get the web push representation of the notification.
     */
    public function toWebPush(object $notifiable, Notification $notification): WebPushMessage
    {
        $url = route('tickets.show', $this->ticket->id);
        
        return (new WebPushMessage)
            ->title('🎯 Tiket Baru Ditugaskan!')
            ->icon('/images/notification-icon.png')
            ->body("Kamu ditugaskan: {$this->ticket->title}")
            ->action('Lihat Detail', 'view_ticket')
            ->data([
                'ticket_id' => $this->ticket->id,
                'url' => $url,
                'project_name' => $this->ticket->project->name ?? 'General',
            ])
            ->badge('/images/badge-icon.png')
            ->options(['TTL' => 2419200]); // 4 weeks
    }

    /**
     * Get the array representation (for database storage).
     */
    public function toArray(object $notifiable): array
    {
        return [
            'ticket_id' => $this->ticket->id,
            'title' => $this->ticket->title,
            'message' => "Kamu ditugaskan tiket: {$this->ticket->title}",
            'url' => route('tickets.show', $this->ticket->id),
            'project_name' => $this->ticket->project->name ?? 'General',
        ];
    }
}
