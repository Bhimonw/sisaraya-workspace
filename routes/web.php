<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');
    Route::post('/notifications/{id}/mark-read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
});

require __DIR__.'/auth.php';

use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectChatController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\RoleChangeRequestController;
use App\Http\Controllers\Admin\UserController as AdminUserController;

Route::middleware(['auth'])->group(function () {
    Route::get('/app-dashboard', [DashboardController::class, 'index'])->name('app.dashboard');

    // Workspace (Meja Kerja) - Proyek aktif yang diikuti user
    Route::get('/workspace', [ProjectController::class, 'workspace'])->name('workspace');
    
    // Proyekku - Proyek yang sedang aktif (owner/member)
    Route::get('projects/mine', [ProjectController::class, 'mine'])->name('projects.mine');
    
    // Semua Projectku - ALL projects (active + completed) where user is owner/member
    Route::get('projects/all-mine', [ProjectController::class, 'allMine'])->name('projects.allMine');
    
    Route::resource('projects', ProjectController::class);
    Route::post('projects/{project}/events', [App\Http\Controllers\ProjectEventController::class, 'store'])->name('projects.events.store');
    Route::delete('project-events/{projectEvent}', [App\Http\Controllers\ProjectEventController::class, 'destroy'])->name('project-events.destroy');
    
    // Project Member Management (PM only)
    Route::post('projects/{project}/members', [App\Http\Controllers\ProjectMemberController::class, 'store'])->name('projects.members.store');
    Route::put('projects/{project}/members/{user}/role', [App\Http\Controllers\ProjectMemberController::class, 'updateRole'])->name('projects.members.updateRole');
    Route::delete('projects/{project}/members/{user}', [App\Http\Controllers\ProjectMemberController::class, 'destroy'])->name('projects.members.destroy');
    
    // General tickets (PM can create for all members)
    Route::middleware('role:pm')->group(function () {
        Route::get('tickets/general/create', [TicketController::class, 'createGeneral'])->name('tickets.createGeneral');
        Route::post('tickets/general', [TicketController::class, 'storeGeneral'])->name('tickets.storeGeneral');
        
        // Manajemen Tiket - PM only
        Route::get('tickets', [TicketController::class, 'index'])->name('tickets.index');
        Route::post('tickets', [TicketController::class, 'store'])->name('tickets.store');
    });
    
    // Ticket overview for all users
    Route::get('tickets/overview', [TicketController::class, 'overview'])->name('tickets.overview');
    Route::get('tickets/mine', [TicketController::class, 'mine'])->name('tickets.mine');
    
    Route::post('tickets/{ticket}/move', [TicketController::class, 'move'])->name('tickets.move');
    
    Route::resource('projects.tickets', TicketController::class)->shallow();

    Route::resource('documents', DocumentController::class)->only(['index','create','store','show']);
    
    // RAB (Rencana Anggaran Biaya) - Bendahara
    Route::resource('rabs', App\Http\Controllers\RabController::class);
    Route::post('rabs/{rab}/approve', [App\Http\Controllers\RabController::class, 'approve'])->name('rabs.approve');
    Route::post('rabs/{rab}/reject', [App\Http\Controllers\RabController::class, 'reject'])->name('rabs.reject');
    Route::get('rabs/{rab}/create-ticket', [App\Http\Controllers\TicketController::class, 'createForRab'])->name('tickets.createFromRab');

    // Businesses (Kewirausahaan)
    Route::resource('businesses', App\Http\Controllers\BusinessController::class)->only(['index','create','store','show']);
    Route::post('businesses/{business}/approve', [App\Http\Controllers\BusinessController::class, 'approve'])->name('businesses.approve');
    Route::post('businesses/{business}/reject', [App\Http\Controllers\BusinessController::class, 'reject'])->name('businesses.reject');
    
    // Business Reports
    Route::post('businesses/{business}/reports', [App\Http\Controllers\BusinessReportController::class, 'store'])->name('businesses.reports.store');
    Route::get('businesses/{business}/reports/{report}/download', [App\Http\Controllers\BusinessReportController::class, 'download'])->name('businesses.reports.download');
    Route::delete('businesses/{business}/reports/{report}', [App\Http\Controllers\BusinessReportController::class, 'destroy'])->name('businesses.reports.destroy');

    // Notes (Catatan Pribadi)
    Route::resource('notes', App\Http\Controllers\NoteController::class)->except(['show', 'create', 'edit']);
    Route::post('notes/{note}/toggle-pin', [App\Http\Controllers\NoteController::class, 'togglePin'])->name('notes.togglePin');

    // Votes (Voting System)
    Route::resource('votes', App\Http\Controllers\VoteController::class)->except(['edit']);
    Route::post('votes/{vote}/cast', [App\Http\Controllers\VoteController::class, 'castVote'])->name('votes.cast');
    Route::post('votes/{vote}/close', [App\Http\Controllers\VoteController::class, 'close'])->name('votes.close');
    
    // Ticket claiming
    Route::post('tickets/{ticket}/claim', [TicketController::class, 'claim'])->name('tickets.claim');
    Route::post('tickets/{ticket}/unclaim', [TicketController::class, 'unclaim'])->name('tickets.unclaim');
    Route::post('tickets/{ticket}/start', [TicketController::class, 'start'])->name('tickets.start');
    Route::post('tickets/{ticket}/complete', [TicketController::class, 'complete'])->name('tickets.complete');
    Route::patch('tickets/{ticket}/set-todo', [TicketController::class, 'setTodo'])->name('tickets.setTodo');
    
    // Calendar API endpoints
    Route::get('api/calendar/user/events', [App\Http\Controllers\Api\CalendarController::class, 'userEvents']);
    Route::get('api/calendar/project/{project}/events', [App\Http\Controllers\Api\CalendarController::class, 'projectEvents']);
    Route::get('api/calendar/project/{project}/tickets', [App\Http\Controllers\Api\CalendarController::class, 'projectTickets']);
    Route::get('api/calendar/user/projects', [App\Http\Controllers\Api\CalendarController::class, 'userProjects']);
    Route::get('api/calendar/all-personal-activities', [App\Http\Controllers\Api\CalendarController::class, 'allPersonalActivities']);
    
    // Test route to check last_seen_at (for debugging)
    Route::get('api/test-last-seen', function() {
        $user = auth()->user();
        return response()->json([
            'user_id' => $user->id,
            'name' => $user->name,
            'last_seen_at' => $user->last_seen_at,
            'last_seen_at_formatted' => $user->last_seen_at ? $user->last_seen_at->toDateTimeString() : null,
            'is_online' => $user->isOnline(),
            'current_time' => now()->toDateTimeString(),
        ]);
    });
    
    // Online Users API
    Route::get('api/online-users', function() {
        $onlineUsers = \App\Models\User::whereNotNull('last_seen_at')
            ->where('last_seen_at', '>=', now()->subMinutes(3))
            ->where(function($q) {
                $q->whereNull('guest_expired_at')
                  ->orWhere('guest_expired_at', '>', now());
            })
            ->orderBy('last_seen_at', 'desc')
            ->get()
            ->map(function($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'username' => $user->username,
                    'roles' => $user->getRoleNames(),
                    'last_seen_at' => $user->last_seen_at ? $user->last_seen_at->diffForHumans() : 'Baru saja',
                    'is_online' => $user->isOnline(),
                ];
            });
        
        return response()->json([
            'online_count' => $onlineUsers->count(),
            'users' => $onlineUsers,
        ]);
    })->name('api.online-users');
    
    // Project Chat API
    Route::prefix('api/projects/{project}/chat')->group(function () {
        Route::get('messages/initial', [ProjectChatController::class, 'getInitialMessages'])->name('api.projects.chat.initial');
        Route::get('messages', [ProjectChatController::class, 'getMessages'])->name('api.projects.chat.messages');
        Route::post('messages', [ProjectChatController::class, 'sendMessage'])->name('api.projects.chat.send');
        Route::get('unread', [ProjectChatController::class, 'getUnreadCount'])->name('api.projects.chat.unread');
    });
    
    // Calendar views - Personal only (Dashboard calendar integrated in main dashboard)
    Route::get('calendar/personal', [App\Http\Controllers\CalendarController::class, 'personal'])->name('calendar.personal');

    // Personal Activities
    Route::resource('personal-activities', App\Http\Controllers\PersonalActivityController::class)->except(['create', 'edit']);
    Route::get('api/personal-activities', [App\Http\Controllers\PersonalActivityController::class, 'index'])->name('api.personal-activities.index');

    // Evaluations (Researcher only for create/edit, others can view)
    Route::post('evaluations', [App\Http\Controllers\EvaluationController::class, 'store'])->name('evaluations.store');
    Route::put('evaluations/{evaluation}', [App\Http\Controllers\EvaluationController::class, 'update'])->name('evaluations.update');
    Route::delete('evaluations/{evaluation}', [App\Http\Controllers\EvaluationController::class, 'destroy'])->name('evaluations.destroy');

    // Project Ratings (All members can rate completed projects)
    Route::post('projects/{project}/ratings', [App\Http\Controllers\ProjectRatingController::class, 'store'])->name('projects.ratings.store');
    Route::delete('projects/{project}/ratings', [App\Http\Controllers\ProjectRatingController::class, 'destroy'])->name('projects.ratings.destroy');

    // Role Change Requests (User request, HR approve)
    Route::prefix('role-requests')->name('role-requests.')->group(function () {
        Route::get('create', [RoleChangeRequestController::class, 'create'])->name('create');
        Route::post('/', [RoleChangeRequestController::class, 'store'])->name('store');
        Route::get('my-requests', [RoleChangeRequestController::class, 'myRequests'])->name('my-requests');
    });

    Route::prefix('admin')->name('admin.')->group(function () {
        // Role Change Requests Management (HR only)
        Route::middleware('role:hr')->group(function () {
            Route::get('role-requests', [RoleChangeRequestController::class, 'index'])->name('role-requests.index');
            Route::post('role-requests/{roleChangeRequest}/approve', [RoleChangeRequestController::class, 'approve'])->name('role-requests.approve');
            Route::post('role-requests/{roleChangeRequest}/reject', [RoleChangeRequestController::class, 'reject'])->name('role-requests.reject');
        });
        
        // User Management (HR can view users and create new users)
        Route::middleware('role:hr')->group(function () {
            Route::get('users', [AdminUserController::class, 'index'])->name('users.index');
            Route::get('users/create', [AdminUserController::class, 'create'])->name('users.create');
            Route::post('users', [AdminUserController::class, 'store'])->name('users.store');
        });
    });
});
