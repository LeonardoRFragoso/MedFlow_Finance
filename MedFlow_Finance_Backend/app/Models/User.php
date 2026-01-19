<?php

namespace App\Models;

use App\Models\Traits\HasTenant;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasTenant, HasUuids;

    protected $fillable = [
        'clinic_id',
        'name',
        'email',
        'password',
        'phone',
        'avatar_url',
        'status',
        'last_login_at',
        'two_factor_enabled',
        'two_factor_secret',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'two_factor_enabled' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function clinic()
    {
        return $this->belongsTo(Clinic::class);
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles');
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'user_permissions');
    }

    public function hasRole($role)
    {
        return $this->roles()->where('name', $role)->exists();
    }

    public function hasPermission($permission)
    {
        return $this->permissions()->where('name', $permission)->exists() 
            || $this->roles()->whereHas('permissions', function ($query) use ($permission) {
                $query->where('name', $permission);
            })->exists();
    }

    public function hasAnyRole($roles)
    {
        return $this->roles()->whereIn('name', (array) $roles)->exists();
    }

    public function isAdmin()
    {
        return $this->hasRole('admin');
    }

    public function isActive()
    {
        return $this->status === 'active';
    }

    public function uploads()
    {
        return $this->hasMany(Upload::class);
    }

    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class);
    }
}
