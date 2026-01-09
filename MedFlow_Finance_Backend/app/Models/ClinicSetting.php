<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClinicSetting extends Model
{
    use HasFactory;

    protected $table = 'clinic_settings';

    protected $fillable = [
        'clinic_id',
        'default_billing_type',
        'currency',
        'enable_glosa_detection',
        'enable_compliance_check',
        'validation_rules',
        'data_retention_days',
        'auto_delete_old_files',
        'notify_on_upload_complete',
        'notify_on_error',
        'notification_email',
        'webhook_url',
        'webhook_secret',
    ];

    protected $casts = [
        'enable_glosa_detection' => 'boolean',
        'enable_compliance_check' => 'boolean',
        'auto_delete_old_files' => 'boolean',
        'notify_on_upload_complete' => 'boolean',
        'notify_on_error' => 'boolean',
        'data_retention_days' => 'integer',
        'validation_rules' => 'json',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function clinic()
    {
        return $this->belongsTo(Clinic::class);
    }
}
