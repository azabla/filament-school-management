<?php

namespace App\Policies;

use App\Models\User;
use App\Models\student;
use Illuminate\Auth\Access\Response;

class StudentPolicy
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
     */
    public function view(User $user, student $student): bool
    {
        if($user->hasRole(['super_admin', 'Admin'])){
            return true;
        }
        if($user->hasRole('Teacher')){
            $teacher = $user->teacher;
            if($teacher && $teacher->sections->contains($student->section)){
                return true;
        }
        }

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
    public function update(User $user, student $student): bool
    {
        return $user->hasRole(['super_admin', 'Admin']);


    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, student $student): bool
    {
        return $user->hasRole(['super_admin', 'Admin']);

    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, student $student): bool
    {
        return $user->hasRole(['super_admin', 'Admin']);


    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, student $student): bool
    {
        return $user->hasRole(['super_admin', 'Admin']);


    }
}