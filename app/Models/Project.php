<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = ['name','description','owner_id','status','is_public','start_date','end_date'];

    protected $casts = [
        'is_public' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

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
            default => 'gray',
        };
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function events()
    {
        return $this->hasMany(ProjectEvent::class);
    }

    public function members()
    {
        return $this->belongsToMany(User::class, 'project_user')
                    ->withPivot('role', 'event_roles')
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
}

