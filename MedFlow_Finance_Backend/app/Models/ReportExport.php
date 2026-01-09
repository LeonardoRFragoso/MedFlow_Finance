<?php

namespace App\Models;

use App\Models\Traits\HasTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportExport extends Model
{
    use HasFactory, HasTenant;

    protected $table = 'report_exports';

    protected $fillable = [
        'clinic_id',
        'report_id',
        'user_id',
        'export_format',
        'file_path',
        'file_size_bytes',
        'downloaded_at',
        'expires_at',
    ];

    protected $casts = [
        'file_size_bytes' => 'integer',
        'downloaded_at' => 'datetime',
        'expires_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    public function report()
    {
        return $this->belongsTo(Report::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isExpired()
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function isDownloaded()
    {
        return $this->downloaded_at !== null;
    }

    public function markAsDownloaded()
    {
        $this->update(['downloaded_at' => now()]);

        return $this;
    }
}
