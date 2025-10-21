<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = ['name','description','owner_id','status','is_public','start_date','end_date','label'];

    protected $casts = [
        'is_public' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    /**
     * Get available project labels
     */
    public static function getLabels(): array
    {
        return ['UMKM', 'DIVISI', 'Kegiatan'];
    }

    /**
     * Get label badge color
     */
    public static function getLabelColor(?string $label): string
    {
        return match($label) {
            'UMKM' => 'purple',
            'DIVISI' => 'blue',
            'Kegiatan' => 'green',
            default => 'gray',
        };
    }

    /**
     * Scope: Filter by label
     */
    public function scopeByLabel($query, ?string $label)
    {
        if ($label) {
            return $query->where('label', $label);
        }
        return $query;
    }

    /**
     * Scope: Get blackout projects
     */
    public function scopeBlackout($query)
    {
        return $query->where('status', 'blackout');
    }

    /**
     * Scope: Get active projects (excluding blackout)
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Get status label
     */
    public static function getStatusLabel(string $status): string
    {
        return match($status) {
            'planning' => 'Perencanaan',
            'active' => 'Aktif',
            'on_hold' => 'Tertunda',
            'completed' => 'Selesai',
            'blackout' => 'Blackout',
            default => 'Perencanaan',
        };
    }

    /**
     * Get status color
     */
    public static function getStatusColor(string $status): string
    {
        return match($status) {
            'planning' => 'gray',
            'active' => 'blue',
            'on_hold' => 'yellow',
            'completed' => 'green',
            'blackout' => 'red',
            default => 'gray',
        };
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    public function rabs()
    {
        return $this->hasMany(Rab::class);
    }

    public function events()
    {
        return $this->hasMany(ProjectEvent::class);
    }

    public function members()
    {
        return $this->belongsToMany(User::class, 'project_user')
                    ->withPivot('role', 'event_roles', 'deleted_at')
                    ->withTimestamps()
                    ->wherePivotNull('deleted_at'); // Only active members
    }

    /**
     * Get all members including past members (soft deleted)
     */
    public function allMembers()
    {
        return $this->belongsToMany(User::class, 'project_user')
                    ->withPivot('role', 'event_roles', 'deleted_at')
                    ->withTimestamps();
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function evaluations()
    {
        return $this->morphMany(Evaluation::class, 'evaluable');
    }

    public function ratings()
    {
        return $this->hasMany(ProjectRating::class);
    }

    public function chatMessages()
    {
        return $this->hasMany(ProjectChatMessage::class);
    }

    /**
     * Get average rating for this project
     */
    public function averageRating(): float
    {
        return round($this->ratings()->avg('rating') ?? 0, 1);
    }

    /**
     * Check if user is project manager (owner)
     */
    public function isManager(User $user): bool
    {
        return $this->owner_id === $user->id;
    }

    /**
     * Check if user is project admin (has admin role in pivot)
     */
    public function isAdmin(User $user): bool
    {
        $member = $this->members()->where('user_id', $user->id)->first();
        return $member && $member->pivot->role === 'admin';
    }

    /**
     * Check if user can manage project (PM or admin)
     */
    public function canManage(User $user): bool
    {
        return $this->isManager($user) || $this->isAdmin($user);
    }

    /**
     * Check if user can manage members (PM, admin, or HR)
     */
    public function canManageMembers(User $user): bool
    {
        // PM or Admin can manage
        if ($this->isManager($user) || $this->isAdmin($user)) {
            return true;
        }
        
        // HR can also manage members
        if ($user->hasRole('hr')) {
            return true;
        }
        
        return false;
    }

    /**
     * Check if user is member of project
     */
    public function isMember(User $user): bool
    {
        return $this->members()->where('user_id', $user->id)->exists();
    }

    /**
     * Check if user was ever a member (including past members)
     */
    public function wasEverMember(User $user): bool
    {
        return $this->allMembers()->where('user_id', $user->id)->exists();
    }
}
