<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory, HasUuids;

    public $timestamps = true;

    protected $fillable = [
        'name',
        'description',
        'role_type',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permissions');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_roles');
    }

    public function givePermissionTo($permission)
    {
        if (is_string($permission)) {
            $permission = Permission::whereName($permission)->firstOrFail();
        }

        $this->permissions()->syncWithoutDetaching($permission);

        return $this;
    }

    public function revokePermissionTo($permission)
    {
        if (is_string($permission)) {
            $permission = Permission::whereName($permission)->firstOrFail();
        }

        $this->permissions()->detach($permission);

        return $this;
    }

    public function hasPermissionTo($permission)
    {
        if (is_string($permission)) {
            $permission = Permission::whereName($permission)->first();
        }

        return $this->permissions()->where('id', $permission->id)->exists();
    }
}
