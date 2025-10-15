<?php

namespace App\Notifications;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketAssigned extends Notification
{
    use Queueable;

    protected $ticket;
    protected $createdBy;
    protected $isSpecific; // true jika untuk user spesifik, false jika umum

    /**
     * Create a new notification instance.
     */
    public function __construct(Ticket $ticket, User $createdBy, bool $isSpecific = false)
    {
        $this->ticket = $ticket;
        $this->createdBy = $createdBy;
        $this->isSpecific = $isSpecific;
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
        $subject = $this->isSpecific 
            ? 'Tiket Ditugaskan Khusus untuk Anda: ' . $this->ticket->title
            : 'Tiket Baru Tersedia: ' . $this->ticket->title;
        
        $greeting = 'Halo ' . $notifiable->name . '!';
        
        $line1 = $this->isSpecific
            ? 'Anda telah ditugaskan tiket khusus oleh ' . $this->createdBy->name . '.'
            : 'Ada tiket baru yang tersedia untuk role Anda.';

        // Determine action URL based on context
        $actionUrl = route('tickets.show', $this->ticket->id);
        
        if ($this->ticket->context === 'project' && $this->ticket->project_id) {
            $actionUrl = route('projects.show', $this->ticket->project_id);
        } elseif ($this->ticket->context === 'event' && $this->ticket->project_event_id) {
            $event = $this->ticket->projectEvent;
            if ($event && $event->project_id) {
                $actionUrl = route('projects.show', $event->project_id);
            }
        }

        return (new MailMessage)
            ->subject($subject)
            ->greeting($greeting)
            ->line($line1)
            ->line('**Judul:** ' . $this->ticket->title)
            ->when($this->ticket->description, function($mail) {
                return $mail->line('**Deskripsi:** ' . $this->ticket->description);
            })
            ->line('**Status:** ' . ucfirst($this->ticket->status))
            ->when($this->ticket->due_date, function($mail) {
                return $mail->line('**Due Date:** ' . $this->ticket->due_date->format('d M Y'));
            })
            ->action('Lihat Tiket', $actionUrl)
            ->line('Segera ambil dan kerjakan tiket ini!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $allRoles = \App\Models\Ticket::getAllRoles();
        
        $message = $this->isSpecific
            ? $this->createdBy->name . ' menugaskan tiket "' . $this->ticket->title . '" khusus untuk Anda'
            : $this->createdBy->name . ' membuat tiket "' . $this->ticket->title . '" yang tersedia untuk Anda';

        // Determine project name and action URL based on context
        $projectName = null;
        $actionUrl = route('tickets.show', $this->ticket->id);
        
        if ($this->ticket->context === 'project' && $this->ticket->project_id) {
            $projectName = $this->ticket->project?->name;
            $actionUrl = route('projects.show', $this->ticket->project_id);
        } elseif ($this->ticket->context === 'event' && $this->ticket->project_event_id) {
            $event = $this->ticket->projectEvent;
            if ($event && $event->project) {
                $projectName = $event->project->name;
                $actionUrl = route('projects.show', $event->project_id);
            }
        }

        return [
            'ticket_id' => $this->ticket->id,
            'ticket_title' => $this->ticket->title,
            'ticket_description' => $this->ticket->description,
            'ticket_status' => $this->ticket->status,
            'ticket_context' => $this->ticket->context,
            'project_id' => $this->ticket->project_id,
            'project_name' => $projectName,
            'project_event_id' => $this->ticket->project_event_id,
            'created_by_id' => $this->createdBy->id,
            'created_by_name' => $this->createdBy->name,
            'target_user_id' => $this->ticket->target_user_id,
            'target_role' => $this->ticket->target_role,
            'target_role_label' => $this->ticket->target_role ? ($allRoles[$this->ticket->target_role] ?? $this->ticket->target_role) : null,
            'due_date' => $this->ticket->due_date?->format('Y-m-d'),
            'is_specific' => $this->isSpecific,
            'message' => $message,
            'action_url' => $actionUrl,
        ];
    }
}
