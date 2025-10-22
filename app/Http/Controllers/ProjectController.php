<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Ticket;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProjectController extends Controller
{
    // Proyekku: Proyek yang sedang aktif (user adalah owner atau member)
    public function mine(Request $request)
    {
        $user = Auth::user();
        
        // Head (Yahya) dapat melihat SEMUA proyek aktif (auto-viewer)
        if ($user->hasRole('head')) {
            $myProjects = Project::withCount('tickets')
                ->with(['owner', 'members'])
                ->whereIn('status', ['planning', 'active'])
                ->latest()
                ->get();
        } else {
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
        }
        
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
        
        // Head (Yahya) dapat melihat SEMUA proyek (auto-viewer)
        if ($user->hasRole('head')) {
            // Get blackout projects (CRITICAL - shown first)
            $blackoutProjects = Project::withCount('tickets')
                ->with(['owner', 'members'])
                ->where('status', 'blackout')
                ->latest()
                ->get();
            
            // Get active projects
            $projects = Project::withCount('tickets')
                ->with(['owner', 'members'])
                ->where('status', 'active')
                ->latest()
                ->get();
        } else {
            // Get blackout projects where user is owner OR member (CRITICAL - shown first)
            $blackoutProjects = Project::withCount('tickets')
                ->with(['owner', 'members'])
                ->where(function($q) use ($user) {
                    $q->where('owner_id', $user->id)
                      ->orWhereHas('members', function($q2) use ($user) {
                          $q2->where('user_id', $user->id);
                      });
                })
                ->where('status', 'blackout')
                ->latest()
                ->get();
            
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
        }
        
        return view('projects.workspace', compact('projects', 'blackoutProjects'));
    }
    
    // Semua Projectku: All projects (active + completed) where user is owner or member
    public function allMine()
    {
        $user = Auth::user();
        
        // Head (Yahya) dapat melihat SEMUA proyek (auto-viewer)
        if ($user->hasRole('head')) {
            $projects = Project::withCount('tickets')
                ->with(['owner', 'members'])
                ->latest()
                ->get();
        } else {
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
        }
        
        return view('projects.all-mine', compact('projects'));
    }
    
    public function index(Request $request)
    {
        // Validate query parameters
        $validated = $request->validate([
            'status' => 'nullable|in:all,planning,active,on_hold,completed,blackout',
            'label' => 'nullable|in:UMKM,DIVISI,Kegiatan',
        ]);
        
        $status = $validated['status'] ?? 'all';
        $label = $validated['label'] ?? null;
        
        // Get counts for each status tab
        $totalCount = Project::count();
        $statusCounts = [
            'planning' => Project::where('status', 'planning')->count(),
            'active' => Project::where('status', 'active')->count(),
            'on_hold' => Project::where('status', 'on_hold')->count(),
            'completed' => Project::where('status', 'completed')->count(),
            'blackout' => Project::where('status', 'blackout')->count(),
        ];
        
        $query = Project::withCount('tickets')->with(['owner', 'members']);
        
        if ($status !== 'all') {
            $query->where('status', $status);
        }
        
        if ($label) {
            $query->where('label', $label);
        }
        
        $projects = $query->latest()->get();
        $labels = Project::getLabels();
        
        return view('projects.index', compact('projects', 'status', 'label', 'labels', 'totalCount', 'statusCounts'));
    }

    public function create()
    {
        // Head role cannot create projects (view-only access)
        $user = Auth::user();
        if ($user->hasRole('head') && !$user->hasAnyRole(['pm', 'hr'])) {
            abort(403, 'Role Head tidak dapat membuat proyek baru. Silakan hubungi PM untuk membuat proyek.');
        }
        
        return view('projects.create');
    }

    public function store(Request $request)
    {
        // Head role cannot create projects (view-only access)
        $user = Auth::user();
        if ($user->hasRole('head') && !$user->hasAnyRole(['pm', 'hr'])) {
            return back()->withErrors(['error' => 'Role Head tidak dapat membuat proyek baru. Silakan hubungi PM untuk membuat proyek.'])->withInput();
        }
        
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:planning,active,on_hold,completed,blackout',
            'label' => 'nullable|in:UMKM,DIVISI,Kegiatan',
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
            'label' => $data['label'] ?? null,
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
        // Head role cannot edit projects (view-only access)
        $user = Auth::user();
        if ($user->hasRole('head') && !$user->hasAnyRole(['pm', 'hr'])) {
            abort(403, 'Role Head tidak dapat mengedit proyek. Hanya dapat melihat informasi proyek.');
        }
        
        $project->load('members');
        return view('projects.edit', compact('project'));
    }

    public function update(Request $request, Project $project)
    {
        // Head role cannot update projects (view-only access)
        $user = Auth::user();
        if ($user->hasRole('head') && !$user->hasAnyRole(['pm', 'hr'])) {
            return back()->withErrors(['error' => 'Role Head tidak dapat mengedit proyek. Hanya dapat melihat informasi proyek.'])->withInput();
        }
        
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:planning,active,on_hold,completed,blackout',
            'label' => 'nullable|in:UMKM,DIVISI,Kegiatan',
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
            'label' => $data['label'] ?? null,
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
        
        // Get month and year from request, default to current
        $month = request('month', date('n'));
        $year = request('year', date('Y'));
        
        // Validate month and year
        $month = max(1, min(12, intval($month)));
        $year = max(2020, min(2100, intval($year)));
        
        // Prepare calendar data
        $calendarEvents = [];
        
        // Add project timeline if it has start and end dates
        if ($project->start_date && $project->end_date) {
            $calendarEvents[] = [
                'id' => 'project-' . $project->id,
                'title' => '[Proyek] ' . $project->name,
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
            $year,
            $month,
            $calendarEvents
        );
        
        return view('projects.show', compact('project', 'calendar'));
    }

    /**
     * Get calendar data via AJAX (for interactive navigation)
     */
    public function getCalendar(Project $project)
    {
        $project->load([
            'tickets',
            'events'
        ]);
        
        // Get month and year from request
        $month = request('month', date('n'));
        $year = request('year', date('Y'));
        
        // Validate month and year
        $month = max(1, min(12, intval($month)));
        $year = max(2020, min(2100, intval($year)));
        
        // Prepare calendar data
        $calendarEvents = [];
        
        // Add project timeline if it has start and end dates
        if ($project->start_date && $project->end_date) {
            $calendarEvents[] = [
                'id' => 'project-' . $project->id,
                'title' => '[Proyek] ' . $project->name,
                'start' => $project->start_date->format('Y-m-d'),
                'end' => $project->end_date->format('Y-m-d'),
                'type' => 'Project',
                'status' => $project->status,
                'description' => 'Timeline Proyek',
            ];
        }
        
        // Add project events to calendar
        foreach ($project->events as $event) {
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
                    'start' => $ticket->due_date,
                    'type' => 'Tiket',
                    'status' => $ticket->status,
                ];
            }
        }
        
        // Generate calendar HTML
        $calendar = \App\Helpers\CalendarHelper::generateMonthCalendar(
            $year,
            $month,
            $calendarEvents
        );
        
        // Return calendar HTML as partial view
        return view('projects.partials.calendar-grid', compact('calendar', 'project'))->render();
    }

    public function destroy(Project $project)
    {
        // Head role cannot delete projects (view-only access)
        $user = Auth::user();
        if ($user->hasRole('head') && !$user->hasAnyRole(['pm', 'hr'])) {
            return back()->with('error', 'Role Head tidak dapat menghapus proyek. Hanya dapat melihat informasi proyek.');
        }
        
        // Check if the authenticated user is the project owner OR has PM role
        if ($project->owner_id !== auth()->id() && !auth()->user()->hasRole('pm')) {
            abort(403, 'Anda tidak memiliki izin untuk menghapus proyek ini. Hanya owner proyek atau PM yang dapat menghapus.');
        }
        
        // Check if project has active status
        if (in_array($project->status, ['active', 'planning'])) {
            return back()->with('error', 'Tidak dapat menghapus proyek yang masih aktif. Silakan ubah status proyek terlebih dahulu.');
        }
        
        try {
            DB::beginTransaction();
            
            $projectId = $project->id;
            $projectName = $project->name;
            
            // 1. Release claimed tickets (set to null instead of deleting for audit trail)
            $releasedTickets = Ticket::where('project_id', $projectId)
                ->where('claimed_by', '!=', null)
                ->update([
                    'claimed_by' => null,
                    'claimed_at' => null,
                ]);
            
            // 2. Nullify project_id on tickets (keep tickets for audit trail)
            $nullifiedTickets = Ticket::where('project_id', $projectId)->update(['project_id' => null]);
            
            // 3. Delete project-related records
            $deletedDocs = $project->documents()->count();
            $project->documents()->delete(); // Will trigger file cleanup via model event
            
            $deletedRabs = $project->rabs()->count();
            $project->rabs()->delete(); // Will trigger file cleanup via model event
            
            $deletedEvents = $project->events()->count();
            $project->events()->delete();
            
            $deletedRatings = $project->ratings()->count();
            $project->ratings()->delete();
            
            // 4. Delete chat messages
            $deletedChats = \App\Models\ProjectChatMessage::where('project_id', $projectId)->count();
            \App\Models\ProjectChatMessage::where('project_id', $projectId)->delete();
            
            // 5. Detach all members (remove from pivot table)
            $detachedMembers = $project->members()->count();
            $project->members()->detach();
            
            // 6. FORCE DELETE the project from database (HARD DELETE)
            $deleted = $project->delete();
            
            if (!$deleted) {
                throw new \Exception('Failed to delete project from database');
            }
            
            // Verify deletion
            $stillExists = \App\Models\Project::find($projectId);
            if ($stillExists) {
                throw new \Exception('Project still exists in database after deletion');
            }
            
            DB::commit();
            
            \Log::info('Project PERMANENTLY deleted from database', [
                'project_id' => $projectId,
                'project_name' => $projectName,
                'deleted_by' => auth()->user()->username,
                'released_tickets' => $releasedTickets,
                'nullified_tickets' => $nullifiedTickets,
                'deleted_documents' => $deletedDocs,
                'deleted_rabs' => $deletedRabs,
                'deleted_events' => $deletedEvents,
                'deleted_ratings' => $deletedRatings,
                'deleted_chats' => $deletedChats,
                'detached_members' => $detachedMembers,
            ]);
            
            return redirect()->route('projects.index')->with('success', "Proyek '{$projectName}' berhasil DIHAPUS PERMANEN dari database!");
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::error('Project deletion failed', [
                'project_id' => $project->id,
                'owner_id' => auth()->id(),
                'error' => $e->getMessage(),
            ]);
            
            return back()->with('error', 'Gagal menghapus proyek. Silakan coba lagi.');
        }
    }
}
