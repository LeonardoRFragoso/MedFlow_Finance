<?php

namespace App\Models;

use App\Models\Traits\HasTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Error extends Model
{
    use HasFactory, HasTenant;

    protected $table = 'errors';

    protected $fillable = [
        'clinic_id',
        'upload_id',
        'record_id',
        'error_type',
        'error_code',
        'error_message',
        'row_number',
        'field_name',
        'raw_value',
        'stack_trace',
        'status',
        'resolved_at',
        'resolved_by',
        'resolution_notes',
    ];

    protected $casts = [
        'row_number' => 'integer',
        'resolved_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function upload()
    {
        return $this->belongsTo(Upload::class);
    }

    public function record()
    {
        return $this->belongsTo(Record::class);
    }

    public function resolvedBy()
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    public function isNew()
    {
        return $this->status === 'new';
    }

    public function isAcknowledged()
    {
        return $this->status === 'acknowledged';
    }

    public function isResolved()
    {
        return $this->status === 'resolved';
    }

    public function resolve($userId, $notes = null)
    {
        $this->update([
            'status' => 'resolved',
            'resolved_at' => now(),
            'resolved_by' => $userId,
            'resolution_notes' => $notes,
        ]);

        return $this;
    }

    public function acknowledge()
    {
        $this->update(['status' => 'acknowledged']);

        return $this;
    }
}
