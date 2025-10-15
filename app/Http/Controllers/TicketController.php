<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Ticket;
use App\Models\User;
use App\Notifications\TicketAssigned;

class TicketController extends Controller
{
    /**
     * Show all tickets management page (PM only)
     */
    public function index(Request $request)
    {
        // Get all tickets including general tickets (where project_id is null)
        $allTickets = Ticket::with([
            'project', 
            'creator', 
            'claimedBy', 
            'projectEvent'
        ])->latest()->get();
        
        return view('tickets.index', compact('allTickets'));
    }

    public function store(Request $request, Project $project = null)
    {
        // If project is null, it's a general ticket (from Manajemen Tiket)
        if ($project === null) {
            // PM-only can create general tickets
            if (!$request->user()->hasRole('pm')) {
                abort(403, 'Only PM can create general tickets');
            }

            $data = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'priority' => 'nullable|in:low,medium,high,urgent',
                'weight' => 'nullable|integer|min:1|max:10',
                'target_type' => 'required|in:all,role,user',
                'target_role' => 'nullable|string',
                'target_user_id' => 'nullable|array',
                'target_user_id.*' => 'exists:users,id',
                'due_date' => 'nullable|date',
            ]);

            // Determine target based on target_type
            $targetRole = null;
            $targetUserIds = [];
            
            if ($data['target_type'] === 'role') {
                $targetRole = $data['target_role'] ?? null;
            } elseif ($data['target_type'] === 'user') {
                $targetUserIds = $data['target_user_id'] ?? [];
            }
            // If 'all', both remain null/empty

            $ticketsCreated = 0;

            // If multiple users selected, create one ticket per user
            if (!empty($targetUserIds)) {
                foreach ($targetUserIds as $userId) {
                    $ticket = Ticket::create([
                        'title' => $data['title'],
                        'description' => $data['description'] ?? null,
                        'status' => 'todo',
                        'context' => 'umum',
                        'priority' => $data['priority'] ?? 'medium',
                        'weight' => $data['weight'] ?? 5,
                        'target_role' => null,
                        'target_user_id' => $userId,
                        'due_date' => $data['due_date'] ?? null,
                        'creator_id' => $request->user()->id,
                        'project_id' => null, // General ticket
                    ]);

                    // Load relationships for notification
                    $ticket->load(['project', 'projectEvent.project']);

                    // Send notification to specific user
                    $targetUser = User::find($userId);
                    if ($targetUser && $targetUser->id !== $request->user()->id) {
                        $targetUser->notify(new TicketAssigned($ticket, $request->user(), true));
                    }
                    
                    $ticketsCreated++;
                }

                $message = $ticketsCreated === 1 
                    ? 'Tiket umum berhasil dibuat!' 
                    : "Berhasil membuat {$ticketsCreated} tiket umum untuk {$ticketsCreated} user!";
                
                return redirect()->route('tickets.index')->with('success', $message);
            }

            // Single ticket for role or all
            $ticket = Ticket::create([
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'status' => 'todo',
                'context' => 'umum',
                'priority' => $data['priority'] ?? 'medium',
                'weight' => $data['weight'] ?? 5,
                'target_role' => $targetRole,
                'target_user_id' => null,
                'due_date' => $data['due_date'] ?? null,
                'creator_id' => $request->user()->id,
                'project_id' => null, // General ticket
            ]);

            // Load relationships for notification
            $ticket->load(['project', 'projectEvent.project']);

            // Send notifications based on target type
            if ($ticket->target_role) {
                // Send notification to all users with the role
                $usersWithRole = User::role($ticket->target_role)->get();
                foreach ($usersWithRole as $user) {
                    if ($user->id !== $request->user()->id) {
                        $user->notify(new TicketAssigned($ticket, $request->user(), false));
                    }
                }
            }
            // If target_type is 'all', no notifications sent

            return redirect()->route('tickets.index')->with('success', 'Tiket umum berhasil dibuat!');
        }

        // Original project-based ticket creation
        // Check if user can manage project (PM or Admin)
        if (!$project->canManage($request->user())) {
            abort(403, 'Only Project Manager or Admin can create tickets');
        }

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:todo,doing,done,blackout',
            'priority' => 'nullable|in:low,medium,high,urgent',
            'weight' => 'nullable|integer|min:1|max:10',
            'context' => 'required|in:event,proyek',
            'project_event_id' => 'nullable|exists:project_events,id',
            'target_role' => 'nullable|string',
            'target_user_id' => 'nullable|exists:users,id',
            'due_date' => 'nullable|date',
            'rab_id' => 'nullable|exists:rabs,id',
        ]);

        // Set default values
        $data['priority'] = $data['priority'] ?? 'medium';
        $data['weight'] = $data['weight'] ?? 5;

        // If context is 'proyek', set project_id
        if ($data['context'] === 'proyek') {
            $data['project_id'] = $project->id;
        } else {
            $data['project_id'] = null;
        }

        // If target_user_id is provided, auto-claim to that user
        if (!empty($data['target_user_id'])) {
            $data['claimed_by'] = $data['target_user_id'];
            $data['claimed_at'] = now();
        }

        $ticket = Ticket::create($data + ['creator_id' => $request->user()->id]);
        
        // Reload ticket with necessary relationships for notification
        $ticket->load(['project', 'projectEvent.project']);
        
        // Send notification based on target type
        $this->sendTicketNotifications($ticket, $request->user(), $project);
        
        return redirect()->route('projects.show', $project)->with('success', 'Tiket berhasil dibuat!');
    }

    /**
     * Send notifications for newly created ticket
     */
    private function sendTicketNotifications(Ticket $ticket, User $creator, Project $project)
    {
        // Case 1: Tiket untuk user spesifik
        if ($ticket->target_user_id) {
            $targetUser = User::find($ticket->target_user_id);
            if ($targetUser) {
                $targetUser->notify(new TicketAssigned($ticket, $creator, true));
            }
            return;
        }

        // Case 2: Tiket untuk role tertentu (notifikasi ke semua user dengan role tersebut di project)
        if ($ticket->target_role) {
            $notifiedUsers = [];
            
            foreach ($project->members as $member) {
                // Skip if already notified
                if (in_array($member->id, $notifiedUsers)) {
                    continue;
                }
                
                // Check permanent role from Spatie
                $hasPermanentRole = $member->hasRole($ticket->target_role);
                
                // Check event role from pivot
                $eventRoles = $member->pivot->event_roles ? json_decode($member->pivot->event_roles, true) : [];
                $hasEventRole = in_array($ticket->target_role, $eventRoles);
                
                // If user has the role, send notification
                if ($hasPermanentRole || $hasEventRole) {
                    $member->notify(new TicketAssigned($ticket, $creator, false));
                    $notifiedUsers[] = $member->id;
                }
            }
            return;
        }

        // Case 3: Tiket umum (no target_user_id & no target_role) - notifikasi ke semua member project
        $notifiedUsers = [];
        foreach ($project->members as $member) {
            if (!in_array($member->id, $notifiedUsers)) {
                $member->notify(new TicketAssigned($ticket, $creator, false));
                $notifiedUsers[] = $member->id;
            }
        }
    }

    // show a small create-ticket form prefilled from RAB
    public function createForRab(Request $request, \App\Models\Rab $rab)
    {
        $project = $rab->project;
        return view('tickets.create_from_rab', compact('rab','project'));
    }

    public function move(Request $request, Ticket $ticket)
    {
        $data = $request->validate(['status' => 'required|in:todo,doing,done']);
        $ticket->update($data);

        // If this ticket is a permintaan_dana linked to a RAB and it's now done,
        // mark the RAB funds_status as 'requested'
        if ($ticket->type === 'permintaan_dana' && $ticket->rab && $data['status'] === 'done') {
            $ticket->rab->update(['funds_status' => 'requested']);
        }
        return back();
    }

    /**
     * Show ALL user's tickets including history (done tickets)
     * This is "Semua Tiketku" - complete history
     */
    public function overview(Request $request)
    {
        $user = $request->user();
        $userRoles = $user->getRoleNames()->toArray();
        
        $tickets = Ticket::with([
            'project', 
            'creator', 
            'claimedBy', 
            'projectEvent.project'
        ])
        ->where(function($q) use ($user, $userRoles) {
            // Tiket yang sudah diambil user (semua status termasuk done)
            $q->where('claimed_by', $user->id)
              // Atau tiket yang ditargetkan ke user spesifik
              ->orWhere('target_user_id', $user->id)
              // Atau tiket yang ditargetkan ke role user (termasuk yang sudah done)
              ->orWhereIn('target_role', $userRoles);
        })
        ->latest()
        ->get();
        
        return view('tickets.overview', compact('tickets'));
    }

    /**
     * Show user's ACTIVE tickets only (todo and doing)
     * This is "Tiketku" - only active work
     */
    public function mine(Request $request)
    {
        $user = $request->user();
        $userRoles = $user->getRoleNames()->toArray();
        
        // Tiket yang sudah diambil user (aktif: todo, doing, dan blackout)
        $myTickets = Ticket::with([
            'project', 
            'creator', 
            'claimedBy', 
            'projectEvent.project'
        ])
        ->where('claimed_by', $user->id)
        ->whereIn('status', ['todo', 'doing', 'blackout'])
        ->latest()
        ->get();
        
        // Tiket yang tersedia untuk diambil (belum claimed, sesuai role/target user)
        $availableTickets = Ticket::with([
            'project', 
            'creator', 
            'projectEvent.project'
        ])
        ->where(function($q) use ($user, $userRoles) {
            // Tiket yang ditargetkan ke role user
            $q->whereIn('target_role', $userRoles)
              // Atau tiket yang ditargetkan ke user spesifik
              ->orWhere('target_user_id', $user->id);
        })
        ->whereNull('claimed_by') // Belum diambil siapapun
        ->whereIn('status', ['todo', 'doing']) // Hanya yang aktif
        ->latest()
        ->get();
        
        return view('tickets.mine', compact('myTickets', 'availableTickets'));
    }

    /**
     * Claim a ticket that is targeted to user's role
     */
    public function claim(Request $request, Ticket $ticket)
    {
        $user = $request->user();

        // Check if ticket is already claimed
        if ($ticket->isClaimed()) {
            return back()->with('error', 'Tiket sudah diambil oleh ' . $ticket->claimedBy->name);
        }

        // Check if user has the required role
        if (!$ticket->canBeClaimedBy($user)) {
            return back()->with('error', 'Anda tidak memiliki role yang sesuai untuk mengambil tiket ini');
        }

        // Claim the ticket
        $ticket->update([
            'claimed_by' => $user->id,
            'claimed_at' => now(),
        ]);

        return back()->with('success', 'Anda berhasil mengambil tiket ini');
    }

    /**
     * Release a claimed ticket (unclaim)
     */
    public function unclaim(Request $request, Ticket $ticket)
    {
        $user = $request->user();

        // Check if user is the one who claimed it
        if ($ticket->claimed_by !== $user->id) {
            return back()->with('error', 'Anda tidak bisa melepas tiket yang bukan milik Anda');
        }

        // Release the ticket
        $ticket->update([
            'claimed_by' => null,
            'claimed_at' => null,
        ]);

        return back()->with('success', 'Tiket berhasil dilepas');
    }

    /**
     * Start working on a ticket (change status from todo to doing)
     */
    public function start(Request $request, Ticket $ticket)
    {
        $user = $request->user();

        // Check if user has claimed this ticket
        if ($ticket->claimed_by !== $user->id) {
            return back()->with('error', 'Anda harus mengambil tiket terlebih dahulu');
        }

        // Check current status
        if ($ticket->status !== 'todo') {
            return back()->with('error', 'Tiket sudah dalam proses atau selesai');
        }

        // Change status to doing
        $ticket->update([
            'status' => 'doing',
            'started_at' => now(),
        ]);

        return back()->with('success', 'Tiket berhasil dimulai! Status: Sedang Dikerjakan');
    }

    /**
     * Complete a ticket (change status from doing to done)
     */
    public function complete(Request $request, Ticket $ticket)
    {
        $user = $request->user();

        // Check if user has claimed this ticket
        if ($ticket->claimed_by !== $user->id) {
            return back()->with('error', 'Anda tidak bisa menyelesaikan tiket yang bukan milik Anda');
        }

        // Check current status
        if ($ticket->status !== 'doing') {
            return back()->with('error', 'Tiket harus dalam status "Sedang Dikerjakan" terlebih dahulu');
        }

        // Change status to done
        $ticket->update([
            'status' => 'done',
            'completed_at' => now(),
        ]);

        return back()->with('success', 'Selamat! Tiket berhasil diselesaikan 🎉');
    }

    /**
     * Set blackout ticket to todo status (user who claimed the ticket)
     */
    public function setTodo(Request $request, Ticket $ticket)
    {
        $user = $request->user();

        // Check if user has claimed this ticket
        if ($ticket->claimed_by !== $user->id) {
            return back()->with('error', 'Anda tidak bisa mengubah status tiket yang bukan milik Anda');
        }

        // Check current status
        if ($ticket->status !== 'blackout') {
            return back()->with('error', 'Hanya tiket dengan status "Bank Ide" yang bisa diubah ke "To Do"');
        }

        // Change status to todo
        $ticket->update([
            'status' => 'todo',
        ]);

        return back()->with('success', 'Tiket berhasil dipindahkan ke To Do! Siap untuk dikerjakan 🚀');
    }

    /**
     * Show form to create general ticket (PM only)
     */
    public function createGeneral()
    {
        return view('tickets.create_general');
    }

    /**
     * Store general ticket broadcast to all main roles (PM only)
     */
    public function storeGeneral(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'target_roles' => 'required|array|min:1',
            'target_roles.*' => 'required|in:member,pm,bendahara,sekretaris,hr,kewirausahaan,researcher',
            'due_date' => 'nullable|date',
            'priority' => 'required|in:low,medium,high,urgent',
            'weight' => 'nullable|integer|min:1|max:10',
        ]);

        // Create separate ticket for each selected role
        $ticketsCreated = 0;
        $notifiedUsers = [];
        
        foreach ($data['target_roles'] as $role) {
            $ticket = Ticket::create([
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'status' => 'todo',
                'context' => 'umum',
                'type' => 'broadcast', // Mark as broadcast ticket
                'priority' => $data['priority'] ?? 'medium',
                'weight' => $data['weight'] ?? 5,
                'target_role' => $role,
                'due_date' => $data['due_date'] ?? null,
                'creator_id' => $request->user()->id,
                'project_id' => null,
            ]);
            
            // Load relationships for notification
            $ticket->load(['project', 'projectEvent.project']);
            
            $ticketsCreated++;
            
            // Send notification to all users with this role
            $usersWithRole = User::role($role)->get();
            foreach ($usersWithRole as $user) {
                // Skip creator and already notified users
                if ($user->id === $request->user()->id || in_array($user->id, $notifiedUsers)) {
                    continue;
                }
                
                $user->notify(new TicketAssigned($ticket, $request->user(), false));
                $notifiedUsers[] = $user->id;
            }
        }

        return redirect()->route('tickets.overview')
            ->with('success', "Berhasil membuat {$ticketsCreated} tiket umum untuk role yang dipilih. Notifikasi telah dikirim.");
    }
}
