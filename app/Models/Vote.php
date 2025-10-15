<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vote extends Model
{
    use HasFactory;

    protected $fillable = [
        'created_by',
        'title',
        'description',
        'allow_multiple',
        'show_results',
        'is_anonymous',
        'closes_at',
        'status',
    ];

    protected $casts = [
        'allow_multiple' => 'boolean',
        'show_results' => 'boolean',
        'is_anonymous' => 'boolean',
        'closes_at' => 'datetime',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function options(): HasMany
    {
        return $this->hasMany(VoteOption::class)->orderBy('order');
    }

    public function responses(): HasMany
    {
        return $this->hasMany(VoteResponse::class);
    }

    public function hasVoted(User $user): bool
    {
        return $this->responses()->where('user_id', $user->id)->exists();
    }

    public function isClosed(): bool
    {
        return $this->status === 'closed' || 
               ($this->closes_at && $this->closes_at->isPast());
    }
}
