<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Rab extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'project_id', 'amount', 'description', 'file_path', 'created_by', 'funds_status'
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    /**
     * Boot method to register model events
     */
    protected static function boot()
    {
        parent::boot();

        // Automatically delete file from storage when RAB is deleted
        static::deleting(function ($rab) {
            if ($rab->file_path && Storage::disk('public')->exists($rab->file_path)) {
                Storage::disk('public')->delete($rab->file_path);
            }
        });
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function creator()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    public function approver()
    {
        return $this->belongsTo(\App\Models\User::class, 'approved_by');
    }
}
