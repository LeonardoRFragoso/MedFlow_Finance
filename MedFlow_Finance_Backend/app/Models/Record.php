<?php

namespace App\Models;

use App\Models\Traits\HasTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Record extends BaseModel
{
    use HasFactory, SoftDeletes, HasTenant;

    protected $fillable = [
        'clinic_id',
        'upload_id',
        'patient_name',
        'patient_cpf',
        'patient_id',
        'procedure_code',
        'procedure_name',
        'procedure_date',
        'amount_billed',
        'amount_paid',
        'amount_pending',
        'status',
        'provider_name',
        'provider_id',
        'insurance_name',
        'insurance_id',
        'authorization_number',
        'raw_data',
    ];

    protected $casts = [
        'procedure_date' => 'date',
        'amount_billed' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'amount_pending' => 'decimal:2',
        'raw_data' => 'json',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function upload()
    {
        return $this->belongsTo(Upload::class);
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

    public function isApproved()
    {
        return $this->status === 'approved';
    }

    public function isRejected()
    {
        return $this->status === 'rejected';
    }

    public function isDisputed()
    {
        return $this->status === 'disputed';
    }

    public function hasErrors()
    {
        return $this->errors()->where('status', 'new')->exists();
    }

    public function hasWarnings()
    {
        return $this->validations()->where('severity', 'warning')->exists();
    }
}
