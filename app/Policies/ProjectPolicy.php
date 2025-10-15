<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Project;

class ProjectPolicy
{
    public function view(User $user, Project $project)
    {
        return $project->members->contains($user) || $project->owner_id === $user->id || $user->hasRole('HR');
    }

    public function update(User $user, Project $project)
    {
        return $project->owner_id === $user->id || $user->hasRole('HR') || $user->hasRole('PM');
    }

    public function manageMembers(User $user, Project $project)
    {
        return $project->owner_id === $user->id || $user->hasRole('HR') || $user->hasRole('PM');
    }
}
