<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Evaluation extends Model
{
    protected $fillable = [
        'evaluable_type',
        'evaluable_id',
        'researcher_id',
        'notes',
        'status',
        'evaluated_at',
    ];

    protected $casts = [
        'evaluated_at' => 'datetime',
    ];

    /**
     * Get the parent evaluable model (Project or ProjectEvent).
     */
    public function evaluable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the researcher who created this evaluation.
     */
    public function researcher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'researcher_id');
    }
}
