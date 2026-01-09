<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Clinic extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'cnpj',
        'email',
        'phone',
        'address',
        'city',
        'state',
        'zip_code',
        'billing_type',
        'default_currency',
        'status',
        'plan_type',
        'plan_started_at',
        'plan_expires_at',
        'max_users',
        'max_monthly_uploads',
        'max_file_size_mb',
        'logo_url',
        'custom_domain',
    ];

    protected $casts = [
        'plan_started_at' => 'datetime',
        'plan_expires_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function settings()
    {
        return $this->hasOne(ClinicSetting::class);
    }

    public function uploads()
    {
        return $this->hasMany(Upload::class);
    }

    public function records()
    {
        return $this->hasMany(Record::class);
    }

    public function reports()
    {
        return $this->hasMany(Report::class);
    }

    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class);
    }

    public function isActive()
    {
        return $this->status === 'active';
    }

    public function canUpload()
    {
        return $this->isActive() && (!$this->plan_expires_at || $this->plan_expires_at->isFuture());
    }
}
