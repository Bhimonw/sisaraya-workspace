<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MemberModal extends Model
{
    protected $fillable = [
        'user_id',
        'jenis',
        'nama_item',
        'jumlah_uang',
        'deskripsi',
        'dapat_dipinjam',
    ];

    protected $casts = [
        'jumlah_uang' => 'decimal:2',
        'dapat_dipinjam' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
