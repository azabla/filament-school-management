<?php

namespace App\Policies;

use App\Models\Mark;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class MarkPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['super_admin', 'Student', 'Admin', 'Teacher']);

    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Mark $mark): bool
{
   return $user->hasRole(['Student', 'Admin', 'Teacher', 'super_admin']);
}


    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole(['super_admin', 'Admin', 'Teacher']);

    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Mark $mark): bool
    {
        return $user->hasRole(['super_admin', 'Admin', 'Teacher']);

    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Mark $mark): bool
    {
        return $user->hasRole(['super_admin', 'Admin', 'Teacher']);

    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Mark $mark): bool
    {
        return $user->hasRole(['super_admin', 'Admin', 'Teacher']);


    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Mark $mark): bool
    {
        return $user->hasRole(['super_admin', 'Admin', 'Teacher']);


    }
}