<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MemberDataNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $user;
    public $message;

    /**
     * Create a new notification instance.
     */
    public function __construct(User $user, string $message)
    {
        $this->user = $user;
        $this->message = $message;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Data Member Diperbarui',
            'message' => "{$this->user->name} - {$this->message}",
            'user_id' => $this->user->id,
            'user_name' => $this->user->name,
            'action' => $this->message,
            'url' => route('admin.member-data.show', $this->user->id),
        ];
    }
}