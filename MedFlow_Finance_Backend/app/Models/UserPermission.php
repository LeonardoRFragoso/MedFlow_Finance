<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\Pivot;

class UserPermission extends Pivot
{
    use HasUuids;

    protected $table = 'user_permissions';
    public $timestamps = false;
    public $incrementing = false;
    protected $keyType = 'string';
}
