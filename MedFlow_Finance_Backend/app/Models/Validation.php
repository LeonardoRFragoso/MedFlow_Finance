<?php

namespace App\Models;

use App\Models\Traits\HasTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Validation extends Model
{
    use HasFactory, HasTenant;

    public $timestamps = false;

    protected $fillable = [
        'clinic_id',
        'record_id',
        'upload_id',
        'rule_name',
        'rule_type',
        'is_valid',
        'severity',
        'field_name',
        'expected_value',
        'actual_value',
        'message',
        'rule_config',
        'created_at',
    ];

    protected $casts = [
        'is_valid' => 'boolean',
        'rule_config' => 'json',
        'created_at' => 'datetime',
    ];

    public function record()
    {
        return $this->belongsTo(Record::class);
    }

    public function upload()
    {
        return $this->belongsTo(Upload::class);
    }

    public function isError()
    {
        return $this->severity === 'error';
    }

    public function isWarning()
    {
        return $this->severity === 'warning';
    }

    public function isInfo()
    {
        return $this->severity === 'info';
    }
}
