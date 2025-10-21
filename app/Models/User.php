<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use NotificationChannels\WebPush\HasPushSubscriptions;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, HasPushSubscriptions;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'password',
        'bio',
        'photo_path',
        'phone',
        'whatsapp',
        'guest_expired_at',
        'last_seen_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'guest_expired_at' => 'datetime',
            'last_seen_at' => 'datetime',
        ];
    }

    /**
     * Check if user is currently online (active within last 3 minutes)
     */
    public function isOnline(): bool
    {
        if (!$this->last_seen_at) {
            return false;
        }
        
        return $this->last_seen_at->diffInMinutes(now()) < 3;
    }

    /**
     * Get user's online status text
     */
    public function getOnlineStatusText(): string
    {
        if (!$this->last_seen_at) {
            return 'Belum pernah online';
        }
        
        if ($this->isOnline()) {
            return 'Online sekarang';
        }
        
        return 'Terakhir online ' . $this->last_seen_at->diffForHumans();
    }

    /**
     * Get tickets created by this user
     */
    public function createdTickets()
    {
        return $this->hasMany(\App\Models\Ticket::class, 'creator_id');
    }

    /**
     * Get tickets claimed by this user
     */
    public function claimedTickets()
    {
        return $this->hasMany(\App\Models\Ticket::class, 'claimed_by');
    }

    /**
     * Get projects where user is a member
     */
    public function projects()
    {
        return $this->belongsToMany(\App\Models\Project::class, 'project_user');
    }

    /**
     * Get projects owned by this user
     */
    public function ownedProjects()
    {
        return $this->hasMany(\App\Models\Project::class, 'owner_id');
    }

    /**
     * Get personal activities for this user
     */
    public function personalActivities()
    {
        return $this->hasMany(\App\Models\PersonalActivity::class);
    }

    /**
     * Get member skills
     */
    public function skills()
    {
        return $this->hasMany(\App\Models\MemberSkill::class);
    }

    /**
     * Get member modals (contributions)
     */
    public function modals()
    {
        return $this->hasMany(\App\Models\MemberModal::class);
    }

    /**
     * Get member links
     */
    public function links()
    {
        return $this->hasMany(\App\Models\MemberLink::class);
    }

    /**
     * Get notes for this user
     */
    public function notes()
    {
        return $this->hasMany(\App\Models\Note::class);
    }

    /**
     * Check if user is free on a specific date
     * Free = no active tickets AND no personal activities on that date
     */
    public function isFreeOnDate($date): bool
    {
        // Convert to Carbon instance if string
        $date = $date instanceof \Carbon\Carbon ? $date : \Carbon\Carbon::parse($date);

        // Check for active tickets with due_date on this date
        $hasActiveTickets = $this->claimedTickets()
            ->whereDate('due_date', $date)
            ->whereIn('status', ['todo', 'doing', 'blackout'])
            ->exists();

        if ($hasActiveTickets) {
            return false;
        }

        // Check for personal activities on this date
        $hasActivities = $this->personalActivities()
            ->whereDate('date', $date)
            ->exists();

        return !$hasActivities;
    }

    /**
     * Get user's workload for a specific date
     * Returns array with tickets count and activities count
     */
    public function getWorkloadOnDate($date): array
    {
        $date = $date instanceof \Carbon\Carbon ? $date : \Carbon\Carbon::parse($date);

        $activeTickets = $this->claimedTickets()
            ->whereDate('due_date', $date)
            ->whereIn('status', ['todo', 'doing', 'blackout'])
            ->get();

        $activities = $this->personalActivities()
            ->whereDate('date', $date)
            ->get();

        return [
            'tickets_count' => $activeTickets->count(),
            'tickets' => $activeTickets,
            'activities_count' => $activities->count(),
            'activities' => $activities,
            'is_free' => $activeTickets->isEmpty() && $activities->isEmpty(),
        ];
    }

    /**
     * Get user's availability for a date range
     * Returns array of dates with availability status
     */
    public function getAvailabilityRange($startDate, $endDate): array
    {
        $start = $startDate instanceof \Carbon\Carbon ? $startDate : \Carbon\Carbon::parse($startDate);
        $end = $endDate instanceof \Carbon\Carbon ? $endDate : \Carbon\Carbon::parse($endDate);
        
        $availability = [];
        $currentDate = $start->copy();

        while ($currentDate <= $end) {
            $workload = $this->getWorkloadOnDate($currentDate);
            
            $availability[] = [
                'date' => $currentDate->format('Y-m-d'),
                'day_name' => $currentDate->locale('id')->isoFormat('dddd'),
                'is_free' => $workload['is_free'],
                'tickets_count' => $workload['tickets_count'],
                'activities_count' => $workload['activities_count'],
            ];

            $currentDate->addDay();
        }

        return $availability;
    }
}
