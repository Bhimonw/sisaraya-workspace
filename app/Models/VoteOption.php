<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VoteOption extends Model
{
    protected $fillable = [
        'vote_id',
        'option_text',
        'order',
    ];

    public function vote(): BelongsTo
    {
        return $this->belongsTo(Vote::class);
    }

    public function responses(): HasMany
    {
        return $this->hasMany(VoteResponse::class);
    }

    public function getVoteCountAttribute(): int
    {
        return $this->responses()->count();
    }

    public function getVotePercentageAttribute(): float
    {
        $totalVotes = $this->vote->responses()->count();
        if ($totalVotes === 0) return 0;
        
        return ($this->vote_count / $totalVotes) * 100;
    }
}
