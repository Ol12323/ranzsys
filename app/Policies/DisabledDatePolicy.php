<?php

namespace App\Policies;

use App\Models\DisabledDate;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class DisabledDatePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->role->name === 'Owner';
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, DisabledDate $disabledDate): bool
    {
        return $user->role->name === 'Owner';
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->role->name === 'Owner';
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, DisabledDate $disabledDate): bool
    {
        return $user->role->name === 'Owner';
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, DisabledDate $disabledDate): bool
    {
        return $user->role->name === 'Owner';
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, DisabledDate $disabledDate): bool
    {
        return $user->role->name === 'Owner';
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, DisabledDate $disabledDate): bool
    {
        return $user->role->name === 'Owner';
    }
}
