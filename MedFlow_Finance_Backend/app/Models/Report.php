<?php

namespace App\Models;

use App\Models\Traits\HasTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Report extends BaseModel
{
    use HasFactory, SoftDeletes, HasTenant;

    protected $fillable = [
        'clinic_id',
        'user_id',
        'report_type',
        'period_start',
        'period_end',
        'total_records',
        'total_valid',
        'total_errors',
        'total_warnings',
        'total_amount',
        'content',
        'generated_at',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'total_records' => 'integer',
        'total_valid' => 'integer',
        'total_errors' => 'integer',
        'total_warnings' => 'integer',
        'total_amount' => 'decimal:2',
        'content' => 'json',
        'generated_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function exports()
    {
        return $this->hasMany(ReportExport::class);
    }

    public function isSummary()
    {
        return $this->report_type === 'summary';
    }

    public function isDetailed()
    {
        return $this->report_type === 'detailed';
    }

    public function isErrors()
    {
        return $this->report_type === 'errors';
    }

    public function isValidation()
    {
        return $this->report_type === 'validation';
    }

    public function isFinancial()
    {
        return $this->report_type === 'financial';
    }

    public function getSuccessRate()
    {
        if ($this->total_records === 0) {
            return 0;
        }

        return round(($this->total_valid / $this->total_records) * 100, 2);
    }
}
