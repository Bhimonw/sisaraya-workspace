<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    // Proyekku: Proyek yang sedang aktif (user adalah owner atau member)
    public function mine(Request $request)
    {
        $user = Auth::user();
        
        // Get projects where user is owner or member, and status is active
        $myProjects = Project::withCount('tickets')
            ->with(['owner', 'members'])
            ->where(function($q) use ($user) {
                $q->where('owner_id', $user->id)
                  ->orWhereHas('members', function($q2) use ($user) {
                      $q2->where('user_id', $user->id);
                  });
            })
            ->whereIn('status', ['planning', 'active'])
            ->latest()
            ->get();
        
        // Get available public projects (not owner, not member yet)
        $availableProjects = Project::withCount('tickets')
            ->with(['owner', 'members'])
            ->where('is_public', true)
            ->where('owner_id', '!=', $user->id)
            ->whereDoesntHave('members', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->whereIn('status', ['planning', 'active'])
            ->latest()
            ->get();
        
        return view('projects.mine', compact('myProjects', 'availableProjects'));
    }
    
    // Halaman Meja Kerja: Semua tiket yang terkait dengan user
    public function workspace()
    {
        $user = Auth::user();
        
        // Get active projects where user is owner OR member
        // This is "Projectku" - only ACTIVE projects user is participating in
        $projects = Project::withCount('tickets')
            ->with(['owner', 'members'])
            ->where(function($q) use ($user) {
                $q->where('owner_id', $user->id)
                  ->orWhereHas('members', function($q2) use ($user) {
                      $q2->where('user_id', $user->id);
                  });
            })
            ->where('status', 'active')
            ->latest()
            ->get();
        
        return view('projects.workspace', compact('projects'));
    }
    
    // Semua Projectku: All projects (active + completed) where user is owner or member
    public function allMine()
    {
        $user = Auth::user();
        
        // Get ALL projects where user is owner OR member (including completed)
        $projects = Project::withCount('tickets')
            ->with(['owner', 'members'])
            ->where(function($q) use ($user) {
                $q->where('owner_id', $user->id)
                  ->orWhereHas('members', function($q2) use ($user) {
                      $q2->where('user_id', $user->id);
                  });
            })
            ->latest()
            ->get();
        
        return view('projects.all-mine', compact('projects'));
    }
    
    public function index(Request $request)
    {
        $status = $request->get('status', 'all');
        
        $query = Project::withCount('tickets')->with(['owner', 'members']);
        
        if ($status !== 'all') {
            $query->where('status', $status);
        }
        
        $projects = $query->latest()->get();
        
        return view('projects.index', compact('projects', 'status'));
    }

    public function create()
    {
        return view('projects.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:planning,active,on_hold,completed',
            'is_public' => 'boolean',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'member_ids' => 'nullable|array',
            'member_ids.*' => 'exists:users,id',
        ]);

        $project = Project::create($data + [
            'owner_id' => $request->user()->id,
            'is_public' => $request->has('is_public'),
            'start_date' => $data['start_date'] ?? null,
            'end_date' => $data['end_date'] ?? null,
        ]);
        
        // Attach members with their roles
        if ($request->filled('member_ids')) {
            foreach ($request->input('member_ids') as $memberId) {
                // Get role from request (role_{userId})
                $role = $request->input("role_{$memberId}", 'member');
                
                $project->members()->attach($memberId, [
                    'role' => $role,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
        
        return redirect()->route('projects.show', $project)->with('success', 'Proyek berhasil dibuat!');
    }

    public function edit(Project $project)
    {
        $project->load('members');
        return view('projects.edit', compact('project'));
    }

    public function update(Request $request, Project $project)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:planning,active,on_hold,completed',
            'is_public' => 'boolean',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'member_ids' => 'nullable|array',
            'member_ids.*' => 'exists:users,id'
        ]);

        // Update project basic info
        $project->update([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'status' => $data['status'],
            'is_public' => $request->has('is_public'),
            'start_date' => $data['start_date'] ?? null,
            'end_date' => $data['end_date'] ?? null,
        ]);
        
        // Sync members with their roles
        $syncData = [];
        if ($request->filled('member_ids')) {
            foreach ($request->input('member_ids') as $memberId) {
                $role = $request->input("role_{$memberId}", 'member');
                $syncData[$memberId] = [
                    'role' => $role,
                    'updated_at' => now(),
                ];
            }
        }
        $project->members()->sync($syncData);
        
        return redirect()->route('projects.show', $project)->with('success', 'Proyek berhasil diperbarui!');
    }

    public function show(Project $project)
    {
        $project->load([
            'tickets.claimedBy',
            'tickets.creator', 
            'tickets.projectEvent',
            'members',
            'events.tickets.claimedBy',
            'events.tickets.creator'
        ]);
        
        // Prepare calendar data
        $calendarEvents = [];
        
        // Add project timeline if it has start and end dates
        if ($project->start_date && $project->end_date) {
            $calendarEvents[] = [
                'id' => 'project-' . $project->id,
                'title' => '📊 ' . $project->name,
                'start' => $project->start_date->format('Y-m-d'),
                'end' => $project->end_date->format('Y-m-d'),
                'type' => 'Project',
                'status' => $project->status,
                'description' => 'Timeline Proyek',
            ];
        }
        
        // Add project events to calendar
        foreach ($project->events as $event) {
            // Format: YYYY-MM-DD HH:MM:SS (properly concatenated)
            $startDateTime = $event->start_date . ' ' . $event->start_time;
            
            $calendarEvents[] = [
                'id' => 'event-' . $event->id,
                'title' => $event->title,
                'start' => $startDateTime,
                'type' => 'Event',
                'description' => $event->description,
                'location' => $event->location,
            ];
        }
        
        // Add tickets with due_date to calendar
        foreach ($project->tickets as $ticket) {
            if ($ticket->due_date) {
                $calendarEvents[] = [
                    'id' => 'ticket-' . $ticket->id,
                    'title' => $ticket->title,
                    'start' => $ticket->due_date, // Already in proper format
                    'type' => 'Tiket',
                    'status' => $ticket->status,
                ];
            }
        }
        
        // Generate calendar HTML
        $calendar = \App\Helpers\CalendarHelper::generateMonthCalendar(
            date('Y'),
            date('n'),
            $calendarEvents
        );
        
        return view('projects.show', compact('project', 'calendar'));
    }
}
