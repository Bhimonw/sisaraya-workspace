<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

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
        'guest_expired_at',
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
        ];
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
     * Get notes for this user
     */
    public function notes()
    {
        return $this->hasMany(\App\Models\Note::class);
    }
}
