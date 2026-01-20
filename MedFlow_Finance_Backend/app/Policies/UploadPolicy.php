<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Upload;

class UploadPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('uploads.view');
    }

    public function view(User $user, Upload $upload): bool
    {
        if (!$user->hasPermission('uploads.view')) {
            return false;
        }

        if ($user->clinic_id !== $upload->clinic_id) {
            return false;
        }

        if (!$user->hasRole('admin') && !$user->hasRole('gestor')) {
            return $user->id === $upload->user_id;
        }

        return true;
    }

    public function create(User $user): bool
    {
        if (!$user->hasPermission('uploads.create')) {
            return false;
        }

        if (!$user->clinic->is_active) {
            return false;
        }

        return true;
    }

    public function update(User $user, Upload $upload): bool
    {
        if (!$user->hasPermission('uploads.update')) {
            return false;
        }

        if ($user->clinic_id !== $upload->clinic_id) {
            return false;
        }

        if (in_array($upload->status, ['processing', 'completed'])) {
            return false;
        }

        if ($user->hasRole('admin') || $user->hasRole('gestor')) {
            return true;
        }

        return $user->id === $upload->user_id;
    }

    public function delete(User $user, Upload $upload): bool
    {
        if (!$user->hasPermission('uploads.delete')) {
            return false;
        }

        if ($user->clinic_id !== $upload->clinic_id) {
            return false;
        }

        if ($upload->status === 'processing') {
            return false;
        }

        if ($user->hasRole('admin')) {
            return true;
        }

        if ($user->hasRole('gestor')) {
            return true;
        }

        return $user->id === $upload->user_id && $upload->status === 'failed';
    }
}
