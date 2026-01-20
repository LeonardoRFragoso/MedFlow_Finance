<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Report;

class ReportPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('reports.view');
    }

    public function view(User $user, Report $report): bool
    {
        if (!$user->hasPermission('reports.view')) {
            return false;
        }

        if ($user->clinic_id !== $report->clinic_id) {
            return false;
        }

        if ($user->hasRole('admin') || $user->hasRole('gestor')) {
            return true;
        }

        return $user->id === $report->user_id;
    }

    public function create(User $user): bool
    {
        if (!$user->hasPermission('reports.create')) {
            return false;
        }

        if (!$user->clinic->is_active) {
            return false;
        }

        return true;
    }

    public function delete(User $user, Report $report): bool
    {
        if (!$user->hasPermission('reports.delete')) {
            return false;
        }

        if ($user->clinic_id !== $report->clinic_id) {
            return false;
        }

        if ($user->hasRole('admin')) {
            return true;
        }

        if ($user->hasRole('gestor')) {
            return true;
        }

        return $user->id === $report->user_id;
    }
}
