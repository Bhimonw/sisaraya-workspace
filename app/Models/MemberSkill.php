<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MemberSkill extends Model
{
    protected $fillable = [
        'user_id',
        'nama_skill',
        'tingkat_keahlian',
        'deskripsi',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
