<?php

namespace App\Policies;

use App\Models\Classes;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ClassesPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['super_admin', 'Admin', 'Teacher']);

    }

    /**
     * Determine whether the user can view the model.
     */public function view(User $user, Classes $classes): bool
{
    // Allow super_admin and Admin to view any class
    if ($user->hasRole(['super_admin', 'Admin'])) {
        return true;
    }

    // Check if the user is a teacher and if they are associated with the given class
    if ($user->hasRole('Teacher')) {
        // Get the teacher associated with the user
        $teacher = $user->teacher;

        // Check if the teacher is associated with the specified class
        if ($teacher && $teacher->classes->contains($classes)) {
            return true;
        }
    }

    // Deny access by default
    return false;
}


    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole(['super_admin', 'Admin']);

    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Classes $classes): bool
    {
        return $user->hasRole(['super_admin', 'Admin']);

    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Classes $classes): bool
    {
        return $user->hasRole(['super_admin', 'Admin']);

    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Classes $classes): bool
    {
        return $user->hasRole(['super_admin', 'Admin']);

    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Classes $classes): bool
    {
        return $user->hasRole(['super_admin', 'Admin']);

    }
}