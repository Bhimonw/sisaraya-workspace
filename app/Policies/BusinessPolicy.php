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
        // User must have business.approve permission and business must be pending
        return $user->can('business.approve') && $business->isPending();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Business $business): bool
    {
        // Creator can update if still pending and has permission
        return $user->can('business.update') 
            && $user->id === $business->created_by 
            && $business->isPending();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Business $business): bool
    {
        // Creator can delete if still pending, PM can delete anytime
        return $user->can('business.delete')
            && (
                ($user->id === $business->created_by && $business->isPending())
                || $user->hasRole('pm')
            );
    }
    
    /**
     * Determine whether the user can upload reports.
     */
    public function uploadReport(User $user, Business $business): bool
    {
        // Creator or anyone with upload permission can upload if approved
        return $user->can('business.upload_reports')
            && ($user->id === $business->created_by || $user->hasRole('pm'))
            && $business->isApproved();
    }
}
