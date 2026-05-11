<?php

namespace App\Policies;

use App\Models\Rebate;
use App\Models\User;

class RebatePolicy
{
    /**
     * Determine whether the user can view any rebates.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can view a rebate.
     */
    public function view(User $user, Rebate $rebate): bool
    {
        return $user->isAdmin() && $user->business_id === optional($rebate->purchase)->business_id;
    }

    /**
     * Determine whether the user can create rebates.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can update rebates.
     */
    public function update(User $user, Rebate $rebate): bool
    {
        return $user->isAdmin() && $user->business_id === optional($rebate->purchase)->business_id;
    }

    /**
     * Determine whether the user can delete rebates.
     */
    public function delete(User $user, Rebate $rebate): bool
    {
        return $user->isAdmin() && $user->business_id === optional($rebate->purchase)->business_id;
    }

    /**
     * Determine whether the user can restore rebates.
     */
    public function restore(User $user, Rebate $rebate): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete rebates.
     */
    public function forceDelete(User $user, Rebate $rebate): bool
    {
        return false;
    }
}

