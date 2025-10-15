<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PersonalActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'start_time',
        'end_time',
        'location',
        'type',
        'color',
        'is_public',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'is_public' => 'boolean',
    ];

    /**
     * Get the user that owns the activity
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get color for activity type
     */
    public static function getTypeColor(string $type): string
    {
        return match($type) {
            'personal' => '#3b82f6',    // Blue
            'family' => '#10b981',       // Green
            'work_external' => '#f59e0b', // Amber
            'study' => '#8b5cf6',        // Purple
            'health' => '#ef4444',       // Red
            'other' => '#6b7280',        // Gray
            default => '#3b82f6',
        };
    }

    /**
     * Get type label
     */
    public static function getTypeLabel(string $type): string
    {
        return match($type) {
            'personal' => 'Pribadi',
            'family' => 'Keluarga',
            'work_external' => 'Pekerjaan Luar',
            'study' => 'Pendidikan',
            'health' => 'Kesehatan',
            'other' => 'Lainnya',
            default => 'Pribadi',
        };
    }
}