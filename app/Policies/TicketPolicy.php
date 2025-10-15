<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Ticket;

class TicketPolicy
{
    public function view(User $user, Ticket $ticket)
    {
        return $ticket->assignee_id === $user->id || $ticket->creator_id === $user->id || $user->hasRole('HR');
    }

    public function move(User $user, Ticket $ticket)
    {
        // allow if assignee, creator, project owner (via relation), PM or HR
        if ($ticket->assignee_id === $user->id || $ticket->creator_id === $user->id) return true;
        if ($user->hasRole('HR') || $user->hasRole('PM')) return true;
        if ($ticket->project && $ticket->project->owner_id === $user->id) return true;
        return false;
    }
}
