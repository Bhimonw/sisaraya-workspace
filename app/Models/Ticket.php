<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'status',
        'priority',
        'weight',
        'due_date',
        'creator_id',
        'rab_id',
        'type',
        'context',
        'project_id',
        'project_event_id',
        'target_role',
        'target_user_id',
        'claimed_by',
        'claimed_at',
        'started_at',
        'completed_at'
    ];

    protected $casts = [
        'due_date' => 'date',
        'claimed_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    // Context labels
    public static function getContextLabel(string $context): string
    {
        return match($context) {
            'umum' => 'Umum',
            'event' => 'Event',
            'proyek' => 'Proyek',
            default => $context,
        };
    }

    // Context colors
    public static function getContextColor(string $context): string
    {
        return match($context) {
            'umum' => 'gray',
            'event' => 'indigo',
            'proyek' => 'blue',
            default => 'gray',
        };
    }

    // Priority labels
    public static function getPriorityLabel(string $priority): string
    {
        return match($priority) {
            'low' => 'Rendah',
            'medium' => 'Sedang',
            'high' => 'Tinggi',
            'urgent' => 'Mendesak',
            default => $priority,
        };
    }

    // Priority colors
    public static function getPriorityColor(string $priority): string
    {
        return match($priority) {
            'low' => 'gray',
            'medium' => 'blue',
            'high' => 'orange',
            'urgent' => 'red',
            default => 'gray',
        };
    }

    // Status labels
    public static function getStatusLabel(string $status): string
    {
        return match($status) {
            'todo' => 'To Do',
            'doing' => 'Doing',
            'done' => 'Done',
            'blackout' => 'Blackout',
            default => ucfirst($status),
        };
    }

    // Status colors
    public static function getStatusColor(string $status): string
    {
        return match($status) {
            'todo' => 'gray',
            'doing' => 'blue',
            'done' => 'green',
            'blackout' => 'black',
            default => 'gray',
        };
    }

    // Weight label (1-10)
    public static function getWeightLabel(int $weight): string
    {
        return match(true) {
            $weight <= 3 => 'Ringan',
            $weight <= 5 => 'Sedang',
            $weight <= 7 => 'Berat',
            $weight <= 10 => 'Sangat Berat',
            default => 'Sedang',
        };
    }

    // List of available roles that can be assigned to tickets
    public static function getAvailableRoles(): array
    {
        return [
            // Role Tetap (Permanent Roles)
            'pm' => 'Project Manager',
            'hr' => 'Human Resources',
            'sekretaris' => 'Sekretaris',
            'bendahara' => 'Bendahara',
            'media' => 'Media',
            'pr' => 'Public Relations',
            'researcher' => 'Researcher',
            'talent_manager' => 'Talent Manager',
            'talent' => 'Talent',
            'kewirausahaan' => 'Kewirausahaan',
        ];
    }

    // List of event-specific roles (active only during project)
    public static function getEventRoles(): array
    {
        return [
            'event_coordinator' => 'Koordinator Event',
            'event_secretary' => 'Sekretaris Event',
            'event_treasurer' => 'Bendahara Event',
            'liaison_officer' => 'Liaison Officer (LO)',
            'logistics' => 'Logistik',
            'mc' => 'Master of Ceremony (MC)',
            'sound_lighting' => 'Sound & Lighting Crew',
            'documentation' => 'Dokumentasi',
            'publication_design' => 'Publikasi & Desain',
            'consumption' => 'Konsumsi',
            'registration' => 'Registrasi & Tiket',
            'security' => 'Keamanan',
            'floor_manager' => 'Floor Manager',
            'sponsorship' => 'Sponsorship',
            'creative_team' => 'Creative / Concept Team',
        ];
    }

    // Get all roles (permanent + event)
    public static function getAllRoles(): array
    {
        return array_merge(
            self::getAvailableRoles(),
            self::getEventRoles()
        );
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function projectEvent()
    {
        return $this->belongsTo(ProjectEvent::class, 'project_event_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function claimedBy()
    {
        return $this->belongsTo(User::class, 'claimed_by');
    }

    public function targetUser()
    {
        return $this->belongsTo(User::class, 'target_user_id');
    }

    public function rab()
    {
        return $this->belongsTo(\App\Models\Rab::class, 'rab_id');
    }

    // Check if ticket can be claimed by user
    public function canBeClaimedBy(User $user): bool
    {
        // If ticket has specific target user, only that user can claim
        if ($this->target_user_id) {
            return $user->id === $this->target_user_id;
        }

        // If no target role, anyone can claim
        if (!$this->target_role) {
            return true;
        }

        // Check if user has the target role
        return $user->hasRole($this->target_role);
    }

    // Check if ticket is already claimed
    public function isClaimed(): bool
    {
        return !is_null($this->claimed_by);
    }
}
