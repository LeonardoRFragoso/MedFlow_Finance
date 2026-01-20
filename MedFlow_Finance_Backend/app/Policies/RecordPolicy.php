<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Record;

class RecordPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('records.view');
    }

    public function view(User $user, Record $record): bool
    {
        return $user->hasPermission('records.view') 
            && $user->clinic_id === $record->clinic_id;
    }

    public function update(User $user, Record $record): bool
    {
        if (!$user->hasPermission('records.update')) {
            return false;
        }

        if ($user->clinic_id !== $record->clinic_id) {
            return false;
        }

        if ($record->status === 'approved') {
            return false;
        }

        if ($user->hasRole('admin') || $user->hasRole('gestor')) {
            return true;
        }

        return $user->id === $record->upload->user_id;
    }

    public function delete(User $user, Record $record): bool
    {
        return $user->hasRole('admin') 
            && $user->clinic_id === $record->clinic_id
            && $record->status !== 'approved';
    }
}
