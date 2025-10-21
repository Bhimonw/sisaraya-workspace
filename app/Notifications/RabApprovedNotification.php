<?php

namespace App\Notifications;

use App\Models\Rab;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;
use NotificationChannels\WebPush\WebPushMessage;
use NotificationChannels\WebPush\WebPushChannel;

class RabApprovedNotification extends Notification
{
    use Queueable;

    protected $rab;

    /**
     * Create a new notification instance.
     */
    public function __construct(Rab $rab)
    {
        $this->rab = $rab;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        return ['database', WebPushChannel::class];
    }

    /**
     * Get the database representation of the notification.
     */
    public function toDatabase($notifiable): array
    {
        return [
            'title' => 'RAB Disetujui',
            'message' => "RAB {$this->rab->title} telah disetujui oleh Bendahara.",
            'action_url' => route('rabs.show', $this->rab->id),
            'type' => 'rab_approved'
        ];
    }

    /**
     * Get the web push representation of the notification.
     */
    public function toWebPush($notifiable): WebPushMessage
    {
        return (new WebPushMessage)
            ->title('RAB Disetujui')
            ->body("RAB {$this->rab->title} telah disetujui oleh Bendahara.")
            ->icon('/images/notification-icon.png')
            ->badge('/images/badge-icon.png')
            ->action('Lihat Detail', 'view_rab')
            ->data([
                'url' => route('rabs.show', $this->rab->id),
                'rab_id' => $this->rab->id
            ]);
    }
}
