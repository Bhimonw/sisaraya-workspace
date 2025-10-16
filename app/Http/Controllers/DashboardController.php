<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Ticket;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $userRoles = $user->getRoleNames()->toArray();
        
        // Get active tickets for current user
        $activeTickets = $user->claimedTickets()
            ->whereIn('status', ['todo', 'doing'])
            ->with('project')
            ->latest()
            ->limit(5)
            ->get();
        
        // Get projects where user is involved (member or owner)
        $userProjects = Project::whereHas('members', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })
        ->orWhere('owner_id', $user->id)
        ->with('tickets')
        ->latest()
        ->limit(3)
        ->get();
        
        // Statistics relevant to user
        $stats = [
            'my_tickets_count' => $user->claimedTickets()->count(),
            'doing_tickets_count' => $user->claimedTickets()->where('status', 'doing')->count(),
            'my_projects_count' => Project::where(function($q) use ($user) {
                $q->whereHas('members', function($query) use ($user) {
                    $query->where('user_id', $user->id);
                })->orWhere('owner_id', $user->id);
            })->count(),
            'active_projects_count' => Project::where('status', 'active')
                ->where(function($q) use ($user) {
                    $q->whereHas('members', function($query) use ($user) {
                        $query->where('user_id', $user->id);
                    })->orWhere('owner_id', $user->id);
                })->count(),
            'available_tickets_count' => \App\Models\Ticket::whereNull('claimed_by')
                ->where(function($q) use ($user, $userRoles) {
                    $q->whereIn('target_role', $userRoles)
                      ->orWhere('target_user_id', $user->id)
                      ->orWhereNull('target_role'); // General tickets
                })->count(),
        ];
        
        return view('dashboard', compact('activeTickets', 'userProjects', 'stats'));
    }
}
