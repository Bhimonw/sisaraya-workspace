<?php

namespace App\Policies;

use App\Models\Business;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class BusinessPolicy
{
    /**
     * Determine whether the user can approve the business.
     */
    public function approve(User $user, Business $business): bool
    {
        // Only PM can approve and only if status is pending
        return $user->hasRole('pm') && $business->isPending();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Business $business): bool
    {
        // Creator can update if still pending
        return $user->id === $business->created_by && $business->isPending();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Business $business): bool
    {
        // Creator can delete if still pending, PM can delete anytime
        return ($user->id === $business->created_by && $business->isPending()) 
            || $user->hasRole('pm');
    }
}
