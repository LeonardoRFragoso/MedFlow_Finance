<?php

namespace App\Models;

use App\Models\Traits\HasTenant;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use HasFactory, HasTenant, HasUuids;

    public $timestamps = false;

    protected $table = 'audit_logs';

    protected $fillable = [
        'clinic_id',
        'user_id',
        'action',
        'resource_type',
        'resource_id',
        'description',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'http_method',
        'http_path',
        'http_status_code',
        'status',
        'error_message',
        'created_at',
    ];

    protected $casts = [
        'old_values' => 'json',
        'new_values' => 'json',
        'http_status_code' => 'integer',
        'created_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isSuccess()
    {
        return $this->status === 'success';
    }

    public function isFailure()
    {
        return $this->status === 'failure';
    }

    public function getChanges()
    {
        $changes = [];

        if ($this->old_values && $this->new_values) {
            foreach ($this->new_values as $key => $newValue) {
                $oldValue = $this->old_values[$key] ?? null;
                if ($oldValue !== $newValue) {
                    $changes[$key] = [
                        'old' => $oldValue,
                        'new' => $newValue,
                    ];
                }
            }
        }

        return $changes;
    }
}
