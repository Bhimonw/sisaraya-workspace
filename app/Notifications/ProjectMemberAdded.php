<?php

namespace App\Notifications;

use App\Models\Project;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProjectMemberAdded extends Notification
{
    use Queueable;

    protected $project;
    protected $addedBy;
    protected $role;
    protected $eventRole;

    /**
     * Create a new notification instance.
     */
    public function __construct(Project $project, User $addedBy, string $role = 'member', ?string $eventRole = null)
    {
        $this->project = $project;
        $this->addedBy = $addedBy;
        $this->role = $role;
        $this->eventRole = $eventRole;
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
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Anda Ditambahkan ke Project: ' . $this->project->name)
            ->greeting('Halo ' . $notifiable->name . '!')
            ->line('Anda telah ditambahkan ke project **' . $this->project->name . '** oleh ' . $this->addedBy->name . '.')
            ->line('Role Anda: ' . ($this->role === 'admin' ? 'Admin Project' : 'Member'))
            ->when($this->eventRole, function($mail) {
                $allRoles = \App\Models\Ticket::getAllRoles();
                return $mail->line('Event Role: ' . ($allRoles[$this->eventRole] ?? $this->eventRole));
            })
            ->action('Lihat Project', url('/projects/' . $this->project->id))
            ->line('Terima kasih telah bergabung!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'project_id' => $this->project->id,
            'project_name' => $this->project->name,
            'project_status' => $this->project->status,
            'added_by_id' => $this->addedBy->id,
            'added_by_name' => $this->addedBy->name,
            'role' => $this->role,
            'event_role' => $this->eventRole,
            'message' => $this->addedBy->name . ' menambahkan Anda ke project "' . $this->project->name . '"',
            'action_url' => url('/projects/' . $this->project->id),
        ];
    }
}
