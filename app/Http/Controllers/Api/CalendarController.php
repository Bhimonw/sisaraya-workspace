<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    /**
     * Get user's tickets and events (for personal calendar)
     * Returns tickets claimed by user + their personal activities + project timeline + available tickets
     */
    public function userEvents(Request $request)
    {
        $user = $request->user();
        $userRoles = $user->getRoleNames()->toArray();
        $events = [];

        // 1. User's claimed tickets with due_date
        $claimedTickets = $user->claimedTickets()->whereNotNull('due_date')->with('project')->get();
        foreach ($claimedTickets as $ticket) {
            $events[] = [
                'id' => 'ticket-' . $ticket->id,
                'title' => '📋 ' . $ticket->title,
                'start' => $ticket->due_date,
                'backgroundColor' => $this->getTicketColor($ticket->status),
                'borderColor' => $this->getTicketColor($ticket->status),
                'extendedProps' => [
                    'type' => 'Tiket Saya',
                    'status' => $ticket->status,
                    'project_name' => $ticket->project?->name ?? 'Umum',
                    'description' => $ticket->description,
                    'url' => route('tickets.show', $ticket->id),
                ]
            ];
        }

        // 2. Available tickets (unclaimed, matching user's role or targeted to user) with due_date
        $availableTickets = \App\Models\Ticket::whereNull('claimed_by')
            ->whereNotNull('due_date')
            ->where(function($q) use ($user, $userRoles) {
                $q->whereIn('target_role', $userRoles)
                  ->orWhere('target_user_id', $user->id)
                  ->orWhereNull('target_role'); // General tickets
            })
            ->with('project')
            ->get();

        foreach ($availableTickets as $ticket) {
            $events[] = [
                'id' => 'available-ticket-' . $ticket->id,
                'title' => '🎫 ' . $ticket->title . ' (Tersedia)',
                'start' => $ticket->due_date,
                'backgroundColor' => '#f59e0b', // Orange for available tickets
                'borderColor' => '#f59e0b',
                'extendedProps' => [
                    'type' => 'Tiket Tersedia',
                    'status' => $ticket->status,
                    'project_name' => $ticket->project?->name ?? 'Umum',
                    'description' => $ticket->description,
                    'target_role' => $ticket->target_role,
                    'url' => route('tickets.show', $ticket->id),
                ]
            ];
        }

        // 3. User's personal activities
        $activities = \App\Models\PersonalActivity::where('user_id', $user->id)->get();
        foreach ($activities as $activity) {
            $events[] = [
                'id' => 'activity-' . $activity->id,
                'title' => $activity->title,
                'start' => $activity->start_time,
                'end' => $activity->end_time,
                'backgroundColor' => $activity->color,
                'borderColor' => $activity->color,
                'extendedProps' => [
                    'type' => \App\Models\PersonalActivity::getTypeLabel($activity->type),
                    'description' => $activity->description,
                    'location' => $activity->location,
                    'is_public' => $activity->is_public,
                ]
            ];
        }

        // 4. Project Events from user's projects
        $projectEvents = \App\Models\ProjectEvent::whereHas('project', function($query) use ($user) {
            // User can see events from projects they're member of or owner
            $query->whereHas('members', function($q) use ($user) {
                $q->where('users.id', $user->id);
            })->orWhere('owner_id', $user->id);
        })->with('project')->get();

        foreach ($projectEvents as $event) {
            $events[] = [
                'id' => 'project-event-' . $event->id,
                'title' => '📅 ' . $event->title,
                'start' => $event->start_date . ' ' . $event->start_time,
                'end' => $event->end_date . ' ' . ($event->end_time ?? $event->start_time),
                'backgroundColor' => '#8b5cf6',
                'borderColor' => '#8b5cf6',
                'extendedProps' => [
                    'type' => 'Event Proyek',
                    'project_name' => $event->project->name,
                    'description' => $event->description,
                    'location' => $event->location,
                    'url' => route('projects.show', $event->project_id),
                ]
            ];
        }

        // 5. Projects Timeline (user's projects with start/end dates)
        $userProjects = Project::where(function($q) use ($user) {
            $q->where('owner_id', $user->id)
              ->orWhereHas('members', function($q2) use ($user) {
                  $q2->where('user_id', $user->id);
              });
        })
        ->where(function($q) {
            $q->whereNotNull('start_date')
              ->orWhereNotNull('end_date');
        })
        ->get();

        foreach ($userProjects as $project) {
            $events[] = [
                'id' => 'project-' . $project->id,
                'title' => '📦 ' . $project->name,
                'start' => $project->start_date ?? $project->created_at,
                'end' => $project->end_date,
                'backgroundColor' => $this->getProjectStatusColor($project->status),
                'borderColor' => $this->getProjectStatusColor($project->status),
                'display' => 'background', // Show as background event
                'extendedProps' => [
                    'type' => 'Proyek',
                    'status' => Project::getStatusLabel($project->status),
                    'project_name' => $project->name,
                    'description' => $project->description,
                    'url' => route('projects.show', $project->id),
                ]
            ];
        }

        return response()->json($events);
    }

    /**
     * Get project events and tickets (for project calendar)
     */
    public function projectEvents(Request $request, Project $project)
    {
        $events = [];
        
        // Add project events
        foreach ($project->events as $event) {
            $events[] = [
                'id' => 'event-' . $event->id,
                'title' => $event->title,
                'start' => $event->start_date . ' ' . $event->start_time,
                'end' => $event->end_date . ' ' . ($event->end_time ?? $event->start_time),
                'backgroundColor' => '#8b5cf6',
                'borderColor' => '#8b5cf6',
                'extendedProps' => [
                    'type' => 'Event',
                    'description' => $event->description,
                    'location' => $event->location,
                ]
            ];
        }
        
        // Add project tickets with due_date
        foreach ($project->tickets as $ticket) {
            if ($ticket->due_date) {
                $events[] = [
                    'id' => 'ticket-' . $ticket->id,
                    'title' => $ticket->title,
                    'start' => $ticket->due_date,
                    'backgroundColor' => $this->getTicketColor($ticket->status),
                    'borderColor' => $this->getTicketColor($ticket->status),
                    'extendedProps' => [
                        'type' => 'Tiket',
                        'status' => $ticket->status,
                        'creator' => $ticket->creator?->name,
                    ]
                ];
            }
        }
        
        return response()->json($events);
    }

    /**
     * Get calendar tickets for a specific project
     * Returns tickets with due_date in FullCalendar format
     */
    public function projectTickets(Request $request, Project $project)
    {
        $events = [];
        
        // Add project tickets with due_date as calendar events
        foreach ($project->tickets as $ticket) {
            if ($ticket->due_date) {
                $events[] = [
                    'id' => 'ticket-' . $ticket->id,
                    'title' => $ticket->title,
                    'start' => $ticket->due_date,
                    'backgroundColor' => $this->getTicketColor($ticket->status),
                    'borderColor' => $this->getTicketColor($ticket->status),
                    'extendedProps' => [
                        'type' => 'ticket',
                        'status' => $ticket->status,
                        'creator' => $ticket->creator?->name,
                    ]
                ];
            }
        }
        
        return response()->json($events);
    }

    /**
     * Get user's projects timeline (for dashboard calendar)
     */
    public function userProjects(Request $request)
    {
        $user = $request->user();
        $events = [];

        // All projects are public now
        $projects = Project::all();

        foreach ($projects as $project) {
            // Add project start/end as timeline if dates exist
            if ($project->start_date || $project->end_date) {
                $events[] = [
                    'id' => 'project-' . $project->id,
                    'title' => '📦 ' . $project->name,
                    'start' => $project->start_date ?? $project->created_at,
                    'end' => $project->end_date,
                    'backgroundColor' => $this->getProjectStatusColor($project->status),
                    'borderColor' => $this->getProjectStatusColor($project->status),
                    'extendedProps' => [
                        'type' => 'Proyek',
                        'project_name' => $project->name,
                        'description' => $project->description,
                        'status' => Project::getStatusLabel($project->status),
                    ]
                ];
            }

            // Add tickets with due_date from projects
            $tickets = $project->tickets()->whereNotNull('due_date')->get();
            foreach ($tickets as $ticket) {
                $events[] = [
                    'id' => 'ticket-' . $ticket->id,
                    'title' => '📋 ' . $ticket->title,
                    'start' => $ticket->due_date,
                    'backgroundColor' => $this->getTicketColor($ticket->status),
                    'borderColor' => $this->getTicketColor($ticket->status),
                    'extendedProps' => [
                        'type' => 'Tiket',
                        'status' => $ticket->status,
                        'project_name' => $project->name,
                    ]
                ];
            }
        }

        return response()->json($events);
    }

    /**
     * Get all public personal activities from all members (for dashboard calendar)
     */
    public function allPersonalActivities(Request $request)
    {
        $user = $request->user();
        
        // Guest shouldn't see personal activities
        if ($user->hasRole('guest')) {
            return response()->json([]);
        }

        $activities = \App\Models\PersonalActivity::where('is_public', true)
            ->with('user')
            ->get();

        $events = [];
        foreach ($activities as $activity) {
            $events[] = [
                'id' => 'activity-' . $activity->id,
                'title' => '👤 ' . $activity->user->name . ': ' . $activity->title,
                'start' => $activity->start_time,
                'end' => $activity->end_time,
                'backgroundColor' => $activity->color,
                'borderColor' => $activity->color,
                'extendedProps' => [
                    'type' => \App\Models\PersonalActivity::getTypeLabel($activity->type),
                    'description' => $activity->description,
                    'location' => $activity->location,
                    'user_name' => $activity->user->name,
                ]
            ];
        }

        return response()->json($events);
    }
    
    private function getTicketColor($status)
    {
        return match($status) {
            'todo' => '#6b7280',
            'doing' => '#3b82f6',
            'done' => '#22c55e',
            default => '#6b7280',
        };
    }
    
    private function getProjectStatusColor($status)
    {
        return match($status) {
            'planning' => '#6b7280',
            'active' => '#3b82f6',
            'on_hold' => '#f59e0b',
            'completed' => '#22c55e',
            default => '#6b7280',
        };
    }
}
