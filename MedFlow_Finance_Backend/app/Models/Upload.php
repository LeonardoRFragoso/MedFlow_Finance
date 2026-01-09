<?php

namespace App\Models;

use App\Models\Traits\HasTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Upload extends BaseModel
{
    use HasFactory, SoftDeletes, HasTenant;

    protected $fillable = [
        'clinic_id',
        'user_id',
        'original_filename',
        'file_path',
        'file_size_bytes',
        'file_type',
        'file_hash',
        'status',
        'processing_started_at',
        'processing_completed_at',
        'processing_error_message',
        'total_rows',
        'valid_rows',
        'error_rows',
        'warning_rows',
        'billing_period_start',
        'billing_period_end',
        'description',
        'tags',
    ];

    protected $casts = [
        'total_rows' => 'integer',
        'valid_rows' => 'integer',
        'error_rows' => 'integer',
        'warning_rows' => 'integer',
        'file_size_bytes' => 'integer',
        'processing_started_at' => 'datetime',
        'processing_completed_at' => 'datetime',
        'billing_period_start' => 'date',
        'billing_period_end' => 'date',
        'tags' => 'json',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function records()
    {
        return $this->hasMany(Record::class);
    }

    public function validations()
    {
        return $this->hasMany(Validation::class);
    }

    public function errors()
    {
        return $this->hasMany(Error::class);
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isProcessing()
    {
        return $this->status === 'processing';
    }

    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    public function isFailed()
    {
        return $this->status === 'failed';
    }

    public function getSuccessRate()
    {
        if ($this->total_rows === 0) {
            return 0;
        }

        return round(($this->valid_rows / $this->total_rows) * 100, 2);
    }
}
