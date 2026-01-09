<?php

namespace App\Models\Traits;

use App\Models\Clinic;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Scope;

class TenantScope implements Scope
{
    public function apply(Builder $builder, \Illuminate\Database\Eloquent\Model $model)
    {
        if (auth()->check() && auth()->user()->clinic_id) {
            $builder->where('clinic_id', auth()->user()->clinic_id);
        }
    }
}

trait HasTenant
{
    protected static function booted()
    {
        static::addGlobalScope(new TenantScope());
    }

    public function clinic()
    {
        return $this->belongsTo(Clinic::class);
    }

    public function scopeForTenant(Builder $query, $clinicId = null)
    {
        $clinicId = $clinicId ?? auth()->user()?->clinic_id;
        
        if ($clinicId) {
            return $query->where('clinic_id', $clinicId);
        }

        return $query;
    }

    public function scopeWithoutTenantScope(Builder $query)
    {
        return $query->withoutGlobalScopes();
    }
}
