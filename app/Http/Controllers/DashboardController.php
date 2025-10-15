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
        
        // Get active tickets for current user
        $activeTickets = $user->claimedTickets()
            ->whereIn('status', ['todo', 'doing'])
            ->with('project')
            ->latest()
            ->limit(5)
            ->get();
        
        // Get projects where user is involved
        $userProjects = Project::whereHas('members', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })
        ->orWhere('owner_id', $user->id)
        ->with('tickets')
        ->latest()
        ->limit(3)
        ->get();
        
        return view('dashboard', compact('activeTickets', 'userProjects'));
    }
}
