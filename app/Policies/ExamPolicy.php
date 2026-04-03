<?php

namespace App\Policies;

use App\Models\Exam;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ExamPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['Admin', 'Super', 'Staff', 'Teacher']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Exam $exam): bool
    {
        if ($user->role === 'Admin' || $user->role === 'Super') {
            return true;
        }

        
        if ($user->role === 'Student') {
            return $user->student->class_id === $exam->class_id;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return in_array($user->role, ['Admin', 'Super', 'Staff', 'Teacher']);
    }

    public function release(User $user, Exam $exam): bool
    {

        return $user->role === 'Admin' || $user->role === 'Super';
    }
    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Exam $exam): bool
    {
        if ($user->role === 'Admin' || $user->role === 'Super') {
            return true;
        }


        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Exam $exam): bool
    {
        return $user->role === 'Admin' || $user->role === 'Super';
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Exam $exam): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Exam $exam): bool
    {
        return false;
    }
}
