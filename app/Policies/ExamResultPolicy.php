<?php

namespace App\Policies;

use App\Models\ExamResult;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Auth\Access\HandlesAuthorization;

class ExamResultPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any results.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['Admin', 'Super', 'Staff', 'Teacher']);
    }

    /**
     * Determine whether the user can view the specific result.
     */
    public function view(User $user, ExamResult $examResult): bool
    {

        return $user->can('view', $examResult->exam);
    }

    /**
     * Determine whether the user can create results.
     * Note: In your controller, 'create' takes an Exam, so you
     * usually authorize against the Exam model, not this policy.
     */
    public function create(User $user): bool
    {
        return in_array($user->role, ['Admin', 'Super', 'Staff', 'Teacher']);
    }

    /**
     * Determine whether the user can update the specific result.
     */
    public function update(User $user, ExamResult $examResult): bool
    {
        
        return $user->can('update', $examResult->exam);
    }

    /**
     * Determine whether the user can delete the result.
     */
    public function delete(User $user, ExamResult $examResult): bool
    {
        return $user->role === 'Admin' || $user->role === 'Super';
    }
}
