<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'user_id',
        'title',
        'description',
        'file_path',
        'file_name',
        'file_type',
        'file_size',
        'report_type',
        'report_date',
    ];

    protected $casts = [
        'report_date' => 'date',
    ];

    /**
     * Get the business that owns the report
     */
    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    /**
     * Get the user who uploaded the report
     */
    public function uploader()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get formatted file size
     */
    public function getFormattedFileSizeAttribute()
    {
        $bytes = $this->file_size;
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }
        return $bytes . ' B';
    }

    /**
     * Get report type color for badge
     */
    public function getReportTypeColorAttribute()
    {
        return match($this->report_type) {
            'penjualan' => 'green',
            'keuangan' => 'blue',
            'operasional' => 'yellow',
            'lainnya' => 'gray',
            default => 'gray',
        };
    }

    /**
     * Get report type label
     */
    public function getReportTypeLabelAttribute()
    {
        return match($this->report_type) {
            'penjualan' => 'Laporan Penjualan',
            'keuangan' => 'Laporan Keuangan',
            'operasional' => 'Laporan Operasional',
            'lainnya' => 'Laporan Lainnya',
            default => 'Laporan',
        };
    }
}