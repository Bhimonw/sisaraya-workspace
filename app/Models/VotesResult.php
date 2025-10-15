<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VotesResult extends Model
{
    use HasFactory;
    protected $table = 'votes_results';
    protected $fillable = [
        'candidate_id', 'context', 'yes_count', 'no_count', 'eligible_count', 'accepted', 'finalized_at', 'finalized_by'
    ];
    protected $casts = [
        'accepted' => 'boolean',
        'finalized_at' => 'datetime',
    ];

    public function candidate()
    {
        return $this->belongsTo(User::class, 'candidate_id');
    }
    public function finalizedBy()
    {
        return $this->belongsTo(User::class, 'finalized_by');
    }
}
