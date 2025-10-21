<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MemberLink extends Model
{
    protected $fillable = [
        'user_id',
        'nama',
        'bidang',
        'url',
        'contact',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
