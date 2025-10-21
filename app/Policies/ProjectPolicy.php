<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Project;

class ProjectPolicy
{
    public function view(User $user, Project $project)
    {
        return $project->members->contains($user) 
            || $project->owner_id === $user->id 
            || $user->hasRole('hr') 
            || $user->hasRole('head'); // Head (Yahya) dapat melihat semua proyek
    }

    public function update(User $user, Project $project)
    {
        // Head TIDAK bisa update - hanya oversight/view
        return $project->owner_id === $user->id || $user->hasRole('hr') || $user->hasRole('pm');
    }

    public function manageMembers(User $user, Project $project)
    {
        // Head TIDAK bisa manage members - hanya oversight
        return $project->owner_id === $user->id || $user->hasRole('hr') || $user->hasRole('pm');
    }
}
