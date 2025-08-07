<?php

namespace App\Policies;

use App\Models\Analysis;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AnalysisPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Analysis $analysis): bool
    {
        // Owner can always view
        if ($user->id === $analysis->user_id) {
            return true;
        }
        // Admin can view only if there is a report or feedback
        if ($user->isAdmin() && ($analysis->report_flag || !is_null($analysis->user_feedback))) {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Analysis $analysis): bool
    {
        return $user->id === $analysis->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Analysis $analysis): bool
    {
        return $user->id === $analysis->user_id || $user->isAdmin();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Analysis $analysis): bool
    {
        return $user->id === $analysis->user_id || $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Analysis $analysis): bool
    {
        return $user->id === $analysis->user_id || $user->isAdmin();
    }

    /**
     * Determine whether the user can perform owner-specific actions.
     */
    public function ownerActions(User $user, Analysis $analysis): bool
    {
        return $user->id === $analysis->user_id;
    }

    /**
     * Determine whether the user is an admin.
     */
    public function adminActions(User $user): bool
    {
        return $user->isAdmin();
    }
} 