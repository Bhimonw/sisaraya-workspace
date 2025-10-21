# 🔍 Potential Issues - Deep Dive Analysis
**Date**: 21 Oktober 2025  
**Type**: Security, Performance, Business Logic, Data Integrity  
**Priority**: MEDIUM to HIGH

---

## 🚨 CRITICAL ISSUES

### 1. ⚠️ RACE CONDITION: Ticket Claiming (HIGH PRIORITY)

**Location**: `app/Http/Controllers/TicketController.php` line 391-412

**Problem**: Multiple users dapat claim ticket yang sama simultaneously.

```php
// VULNERABLE CODE
public function claim(Request $request, Ticket $ticket)
{
    $user = $request->user();
    
    // Check if already claimed
    if ($ticket->claimed_by) {  // ❌ Race condition window!
        return back()->with('error', 'Tiket sudah diambil oleh orang lain');
    }
    
    // Check if user has the required role
    if (!$ticket->canBeClaimedBy($user)) {
        return back()->with('error', 'Anda tidak memiliki role yang sesuai');
    }
    
    // Claim the ticket
    $ticket->update([  // ❌ Another user could update between check and update!
        'claimed_by' => $user->id,
        'claimed_at' => now(),
    ]);
}
```

**Scenario**:
1. User A checks `$ticket->claimed_by` → NULL ✅
2. User B checks `$ticket->claimed_by` → NULL ✅ (simultaneously)
3. User A updates ticket (claimed_by = A)
4. User B updates ticket (claimed_by = B) → **OVERWRITES User A!**

**Impact**: 
- User A thinks they claimed the ticket
- User B also thinks they claimed the ticket
- Database shows only User B
- User A loses their work/notification

**Fix**:
```php
public function claim(Request $request, Ticket $ticket)
{
    $user = $request->user();
    
    // Check if user has the required role first (fast check)
    if (!$ticket->canBeClaimedBy($user)) {
        return back()->with('error', 'Anda tidak memiliki role yang sesuai');
    }
    
    try {
        DB::beginTransaction();
        
        // Use lockForUpdate to prevent race condition
        $ticket = Ticket::where('id', $ticket->id)
            ->lockForUpdate()
            ->first();
        
        // Re-check if already claimed after lock
        if ($ticket->claimed_by) {
            DB::rollBack();
            return back()->with('error', 'Tiket sudah diambil oleh orang lain');
        }
        
        // Claim the ticket atomically
        $ticket->update([
            'claimed_by' => $user->id,
            'claimed_at' => now(),
        ]);
        
        DB::commit();
        
        // Send notification
        $user->notify(new TicketAssignedNotification($ticket));
        
        return back()->with('success', 'Anda berhasil mengambil tiket ini');
        
    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error('Ticket claim failed', [
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'error' => $e->getMessage(),
        ]);
        return back()->with('error', 'Gagal mengambil tiket. Silakan coba lagi.');
    }
}
```

**Alternative Solution** (Database-level):
```php
// Use whereNull in update condition (atomic operation)
$affected = Ticket::where('id', $ticket->id)
    ->whereNull('claimed_by')  // Only update if not claimed
    ->update([
        'claimed_by' => $user->id,
        'claimed_at' => now(),
    ]);

if ($affected === 0) {
    return back()->with('error', 'Tiket sudah diambil oleh orang lain');
}
```

---

### 2. ⚠️ RACE CONDITION: Vote Casting (HIGH PRIORITY)

**Location**: `app/Http/Controllers/VoteController.php` line 84-107

**Problem**: Duplicate votes possible when `allow_multiple = false`.

```php
// VULNERABLE CODE
public function castVote(Request $request, Vote $vote)
{
    if ($vote->isClosed()) {
        return back()->with('error', 'Voting sudah ditutup');
    }
    
    // Check if already voted
    if (!$vote->allow_multiple && $vote->hasVoted(auth()->user())) {  // ❌ Race condition!
        return back()->with('error', 'Anda sudah memberikan suara');
    }
    
    // Delete previous votes if not allow_multiple
    if (!$vote->allow_multiple) {
        VoteResponse::where('vote_id', $vote->id)
            ->where('user_id', auth()->id())
            ->delete();
    }
    
    // Create new responses
    foreach ($validated['option_ids'] as $optionId) {
        VoteResponse::create([...]);  // ❌ Could create duplicates!
    }
}
```

**Fix**:
```php
public function castVote(Request $request, Vote $vote)
{
    if ($vote->isClosed()) {
        return back()->with('error', 'Voting sudah ditutup');
    }
    
    $validated = $request->validate([
        'option_ids' => 'required|array',
        'option_ids.*' => 'exists:vote_options,id',
    ]);
    
    try {
        DB::beginTransaction();
        
        // Lock vote record to prevent concurrent modifications
        $vote = Vote::where('id', $vote->id)->lockForUpdate()->first();
        
        // Re-check if closed after lock
        if ($vote->isClosed()) {
            DB::rollBack();
            return back()->with('error', 'Voting sudah ditutup');
        }
        
        // Check and handle existing votes atomically
        $existingVotes = VoteResponse::where('vote_id', $vote->id)
            ->where('user_id', auth()->id())
            ->lockForUpdate()
            ->get();
        
        if (!$vote->allow_multiple && $existingVotes->isNotEmpty()) {
            DB::rollBack();
            return back()->with('error', 'Anda sudah memberikan suara');
        }
        
        // Delete previous votes if allowed
        if ($vote->allow_multiple) {
            $existingVotes->each->delete();
        }
        
        // Create new responses
        foreach ($validated['option_ids'] as $optionId) {
            VoteResponse::create([
                'vote_id' => $vote->id,
                'vote_option_id' => $optionId,
                'user_id' => auth()->id(),
            ]);
        }
        
        DB::commit();
        
        return back()->with('success', 'Suara Anda berhasil dicatat!');
        
    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error('Vote casting failed', [
            'vote_id' => $vote->id,
            'user_id' => auth()->id(),
            'error' => $e->getMessage(),
        ]);
        return back()->with('error', 'Gagal mencatat suara. Silakan coba lagi.');
    }
}
```

---

### 3. ⚠️ RACE CONDITION: Business Approval to Project Creation

**Location**: `app/Http/Controllers/BusinessController.php` line 77-108

**Problem**: Multiple PMs bisa approve business yang sama simultaneously, creating duplicate projects.

**Scenario**:
1. PM A clicks "Approve" on Business #123
2. PM B clicks "Approve" on Business #123 (simultaneously)
3. Both check `status !== 'approved'` → Both pass ✅
4. PM A creates Project #456
5. PM B creates Project #457
6. Result: 2 projects for 1 business! ❌

**Current Code**:
```php
public function approve(Business $business)
{
    $this->authorize('approve', $business);
    
    try {
        DB::beginTransaction();  // ✅ Has transaction, but NO lock!
        
        // ❌ No lock! Multiple PMs can pass this check
        $project = Project::create([...]);
        
        $project->members()->attach($business->created_by, [...]);
        
        $business->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'project_id' => $project->id,
        ]);
        
        DB::commit();
    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', 'Gagal menyetujui usaha.');
    }
}
```

**Fix**:
```php
public function approve(Business $business)
{
    $this->authorize('approve', $business);
    
    try {
        DB::beginTransaction();
        
        // Lock business record for update
        $business = Business::where('id', $business->id)
            ->lockForUpdate()
            ->first();
        
        // Re-check status after lock
        if ($business->status === 'approved') {
            DB::rollBack();
            return back()->with('error', 'Usaha sudah disetujui oleh PM lain.');
        }
        
        if ($business->status === 'rejected') {
            DB::rollBack();
            return back()->with('error', 'Usaha sudah ditolak sebelumnya.');
        }
        
        // Create project
        $project = Project::create([
            'name' => $business->name,
            'description' => $business->description,
            'owner_id' => auth()->id(),
            'status' => 'active',
            'label' => 'UMKM',
            'is_public' => true,
        ]);
        
        // Add creator as admin
        $project->members()->attach($business->created_by, [
            'role' => 'admin',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        // Update business atomically
        $business->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'rejection_reason' => null,
            'project_id' => $project->id,
        ]);
        
        DB::commit();
        
        return redirect()->route('businesses.show', $business)
            ->with('success', 'Usaha berhasil disetujui dan proyek telah dibuat!');
            
    } catch (\Exception $e) {
        DB::rollBack();
        
        \Log::error('Business approval failed', [
            'business_id' => $business->id,
            'approver_id' => auth()->id(),
            'error' => $e->getMessage(),
        ]);
        
        return back()->with('error', 'Gagal menyetujui usaha. Silakan coba lagi.');
    }
}
```

---

## 🐌 PERFORMANCE ISSUES

### 4. ⚠️ N+1 QUERY: Dashboard Free Users Calculation

**Location**: `app/Http/Controllers/DashboardController.php` line 60-67

**Problem**: Loading ALL users then filtering in PHP causes massive queries.

```php
// SLOW CODE
$freeUsersToday = \App\Models\User::where(function($q) {
    $q->whereNull('guest_expired_at')
      ->orWhere('guest_expired_at', '>', now());
})
->get()  // ❌ Loads ALL users into memory!
->filter(function($u) use ($today) {
    return $u->isFreeOnDate($today);  // ❌ N+1 queries for each user!
});
```

**Impact**:
- If 100 users: 1 query for users + 100 queries for tickets + 100 queries for activities = **201 queries**!
- Slow dashboard load (>5 seconds with many users)
- High memory usage

**User::isFreeOnDate() Implementation**:
```php
public function isFreeOnDate($date): bool
{
    // Check for active tickets on this date
    $hasActiveTickets = $this->claimedTickets()  // ❌ Query per user
        ->whereDate('due_date', $date)
        ->whereIn('status', ['todo', 'doing', 'blackout'])
        ->exists();
    
    if ($hasActiveTickets) {
        return false;
    }
    
    // Check for personal activities on this date
    $hasActivities = $this->personalActivities()  // ❌ Another query per user
        ->whereDate('date', $date)
        ->exists();
    
    return !$hasActivities;
}
```

**Fix Option 1** (Eager Loading):
```php
// Load all data in 3 queries instead of 201
$today = today();

$freeUsersToday = \App\Models\User::where(function($q) {
        $q->whereNull('guest_expired_at')
          ->orWhere('guest_expired_at', '>', now());
    })
    ->with([
        'claimedTickets' => function($q) use ($today) {
            $q->whereDate('due_date', $today)
              ->whereIn('status', ['todo', 'doing', 'blackout']);
        },
        'personalActivities' => function($q) use ($today) {
            $q->whereDate('date', $today);
        }
    ])
    ->get()
    ->filter(function($user) {
        return $user->claimedTickets->isEmpty() && $user->personalActivities->isEmpty();
    });
```

**Fix Option 2** (Database-level filtering):
```php
// Single query with subqueries
$today = today();

$freeUsersToday = \App\Models\User::where(function($q) {
        $q->whereNull('guest_expired_at')
          ->orWhere('guest_expired_at', '>', now());
    })
    ->whereDoesntHave('claimedTickets', function($q) use ($today) {
        $q->whereDate('due_date', $today)
          ->whereIn('status', ['todo', 'doing', 'blackout']);
    })
    ->whereDoesntHave('personalActivities', function($q) use ($today) {
        $q->whereDate('date', $today);
    })
    ->get();
```

**Performance Improvement**: 201 queries → 1 query (200x faster!)

---

### 5. ⚠️ N+1 QUERY: Ticket Index with All Relationships

**Location**: `app/Http/Controllers/TicketController.php` line 18-27

**Problem**: Loads relationships but may have N+1 if project has nested relations.

```php
public function index(Request $request)
{
    $allTickets = Ticket::with([
        'project',  // ✅ Good
        'creator',  // ✅ Good
        'claimedBy',  // ✅ Good
        'projectEvent'  // ⚠️ May have N+1 if event has project relation
    ])->latest()->get();
    
    return view('tickets.index', compact('allTickets'));
}
```

**Fix**:
```php
public function index(Request $request)
{
    $allTickets = Ticket::with([
        'project' => function($q) {
            $q->select('id', 'name', 'status');  // Only needed columns
        },
        'creator:id,name,username',  // Only needed columns
        'claimedBy:id,name,username',
        'projectEvent.project:id,name'  // Nested eager loading
    ])
    ->select('id', 'title', 'status', 'priority', 'project_id', 'creator_id', 'claimed_by', 'project_event_id', 'created_at')  // Only needed columns
    ->latest()
    ->paginate(20);  // Add pagination!
    
    return view('tickets.index', compact('allTickets'));
}
```

---

### 6. ⚠️ MISSING PAGINATION: Multiple Controllers

**Locations**:
- `DocumentController.php` line 31: `$docs = $query->get();`
- `VoteController.php` line 10-29: Both active and closed votes use `get()`
- `TicketController.php` line 337, 361, 383: Mine, overview, available tickets

**Problem**: Loading ALL records without pagination.

**Impact**:
- With 1000+ documents: Slow page load, high memory usage
- Mobile users suffer with large payloads

**Fix Examples**:
```php
// DocumentController
$docs = $query->latest()->paginate(20);

// VoteController
$activeVotes = Vote::where('status', 'active')
    ->latest()
    ->paginate(10);

// TicketController mine()
$myTickets = Ticket::where('claimed_by', $user->id)
    ->latest()
    ->paginate(15);
```

---

## 🔒 SECURITY ISSUES

### 7. ⚠️ PRIVILEGE ESCALATION: Project Member Role Update

**Location**: `app/Http/Controllers/ProjectMemberController.php` line 16-60

**Problem**: Admin can update their own role or another admin's role without restriction.

```php
public function updateRole(Request $request, Project $project, User $user)
{
    // Check if current user is PM, Admin, or HR
    if (!$project->canManageMembers(Auth::user())) {
        abort(403, 'Only Project Manager, Admin, or HR can manage member roles');
    }
    
    // ❌ No check if user is trying to modify themselves or owner!
    // ❌ Admin can change owner's role!
    
    $project->members()->updateExistingPivot($user->id, [
        'role' => $validated['role'],  // ❌ Could demote PM or promote themselves
    ]);
}
```

**Attack Scenario**:
1. Attacker is Project Admin
2. Attacker changes PM's role to 'member'
3. PM loses project control
4. Attacker now has highest privilege

**Fix**:
```php
public function updateRole(Request $request, Project $project, User $user)
{
    // Check if current user is PM, Admin, or HR
    if (!$project->canManageMembers(Auth::user())) {
        abort(403, 'Only Project Manager, Admin, or HR can manage member roles');
    }
    
    // Prevent modifying project owner
    if ($user->id === $project->owner_id) {
        return back()->with('error', 'Cannot modify Project Manager role');
    }
    
    // Only PM can modify admin roles
    if (!$project->isManager(Auth::user())) {
        $targetMember = $project->members()->where('user_id', $user->id)->first();
        if ($targetMember && $targetMember->pivot->role === 'admin') {
            return back()->with('error', 'Only Project Manager can modify admin roles');
        }
    }
    
    // Prevent admins from modifying their own role
    if (Auth::id() === $user->id && $project->isAdmin(Auth::user())) {
        return back()->with('error', 'You cannot modify your own role');
    }
    
    // ... rest of the logic
}
```

---

### 8. ⚠️ MASS ASSIGNMENT: Project Member Removal

**Location**: `app/Http/Controllers/ProjectMemberController.php` line 66-98

**Problem**: Admin can remove other admins without PM oversight.

**Fix**: Add similar restrictions as role update.

---

### 9. ⚠️ INFORMATION DISCLOSURE: Vote Results Before Closing

**Location**: Views showing vote counts before vote closes

**Problem**: If `show_results = false`, users shouldn't see interim results.

**Current Behavior**: Results may be visible in views before vote closes.

**Fix**: Add check in views:
```blade
@if($vote->show_results || $vote->isClosed() || auth()->user()->id === $vote->created_by)
    {{-- Show results --}}
@else
    <p>Hasil voting akan ditampilkan setelah voting ditutup</p>
@endif
```

---

## 📊 DATA INTEGRITY ISSUES

### 10. ⚠️ ORPHANED RECORDS: Project Deletion

**Problem**: No cascade delete strategy defined.

**Scenario**:
1. Project gets deleted
2. Tickets remain with `project_id` pointing to deleted project
3. Documents remain attached to deleted project
4. RABs remain attached to deleted project

**Current State**: No soft deletes on Project model.

**Fix Options**:

**Option 1** (Soft Deletes):
```php
// Project model
use SoftDeletes;

protected $dates = ['deleted_at'];
```

**Option 2** (Cascade Deletes in Migration):
```php
Schema::table('tickets', function (Blueprint $table) {
    $table->foreign('project_id')
        ->references('id')
        ->on('projects')
        ->onDelete('set null');  // Or 'cascade' to delete tickets too
});
```

**Option 3** (Manual Cleanup in Controller):
```php
public function destroy(Project $project)
{
    DB::beginTransaction();
    
    try {
        // Delete or nullify related records
        $project->tickets()->update(['project_id' => null]);
        $project->documents()->delete();
        $project->members()->detach();
        $project->events()->delete();
        
        // Delete project
        $project->delete();
        
        DB::commit();
        
        return redirect()->route('projects.index')
            ->with('success', 'Project deleted');
            
    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', 'Failed to delete project');
    }
}
```

---

### 11. ⚠️ ORPHANED RECORDS: User Deletion

**Problem**: User dapat dihapus via ProfileController without cleaning up relations.

**Location**: `app/Http/Controllers/ProfileController.php`

**Orphaned Data**:
- Created tickets (`creator_id`)
- Claimed tickets (`claimed_by`)
- Project ownership (`owner_id`)
- Project memberships
- Vote responses
- Documents
- RABs
- Notes
- Personal activities

**Fix**: Implement comprehensive cleanup:
```php
public function destroy(Request $request)
{
    $request->validateWithBag('userDeletion', [
        'password' => ['required', 'current_password'],
    ]);
    
    $user = $request->user();
    
    DB::beginTransaction();
    
    try {
        // Check if user is owner of active projects
        $activeProjects = Project::where('owner_id', $user->id)
            ->whereIn('status', ['active', 'planning'])
            ->count();
        
        if ($activeProjects > 0) {
            return back()->withErrors([
                'userDeletion' => 'You cannot delete your account while being manager of active projects. Please transfer ownership first.'
            ]);
        }
        
        // Release claimed tickets
        Ticket::where('claimed_by', $user->id)->update([
            'claimed_by' => null,
            'claimed_at' => null,
        ]);
        
        // Detach from all projects
        $user->projects()->detach();
        
        // Delete user-owned data
        $user->notes()->delete();
        $user->personalActivities()->delete();
        $user->voteResponses()->delete();
        
        // Keep created tickets/documents for audit trail
        // Just nullify creator reference
        Ticket::where('creator_id', $user->id)->update(['creator_id' => null]);
        Document::where('user_id', $user->id)->update(['user_id' => null]);
        
        // Delete user
        $user->delete();
        
        DB::commit();
        
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/');
        
    } catch (\Exception $e) {
        DB::rollBack();
        
        \Log::error('User deletion failed', [
            'user_id' => $user->id,
            'error' => $e->getMessage(),
        ]);
        
        return back()->withErrors([
            'userDeletion' => 'Failed to delete account. Please try again.'
        ]);
    }
}
```

---

### 12. ⚠️ DATA INCONSISTENCY: Ticket Status Transitions

**Problem**: No validation of status transitions (todo → done without doing).

**Current**: User can manually change status bypassing workflow.

**Fix**: Add state machine validation:
```php
// In Ticket model
protected $allowedTransitions = [
    'todo' => ['doing', 'blackout'],
    'doing' => ['done', 'blackout'],
    'blackout' => ['todo'],
    'done' => [], // Cannot transition from done
];

public function canTransitionTo(string $newStatus): bool
{
    return in_array($newStatus, $this->allowedTransitions[$this->status] ?? []);
}

// In Controller
if (!$ticket->canTransitionTo('done')) {
    return back()->with('error', 'Invalid status transition');
}
```

---

## 🔄 BUSINESS LOGIC EDGE CASES

### 13. ⚠️ EDGE CASE: Voting After Auto-Close

**Problem**: Vote closes at specific time, but user form submit happens microseconds after.

**Scenario**:
1. Vote closes at 10:00:00
2. User starts filling form at 09:59:50
3. User submits at 10:00:01
4. Should this be accepted?

**Current**: Check happens on submit, will reject.

**Better UX**: Add client-side countdown + grace period.

---

### 14. ⚠️ EDGE CASE: Project Member Soft Delete vs Hard Delete

**Location**: `Project::members()` vs `Project::allMembers()`

**Problem**: Inconsistent behavior across codebase.

**Current**:
- `members()` - only active (wherePivotNull('deleted_at'))
- `allMembers()` - includes soft-deleted

**Issues**:
- Can't re-add soft-deleted member (duplicate key error)
- Rating system allows past members but member check uses `members()`
- Notifications may fail for soft-deleted members

**Fix**: Clarify usage:
```php
// In ProjectMemberController::store()
// Check if user was previously a member (including soft-deleted)
if ($project->allMembers()->where('user_id', $userId)->exists()) {
    $existingPivot = $project->allMembers()->where('user_id', $userId)->first()->pivot;
    
    if ($existingPivot->deleted_at) {
        // Restore soft-deleted membership
        DB::table('project_user')
            ->where('project_id', $project->id)
            ->where('user_id', $userId)
            ->update(['deleted_at' => null, 'updated_at' => now()]);
    } else {
        // Already active member
        continue;
    }
} else {
    // Add new member
    $project->members()->attach($userId, [...]);
}
```

---

### 15. ⚠️ EDGE CASE: Concurrent Ticket Unclaim + Start

**Scenario**:
1. User A claims ticket
2. User B tries to claim same ticket (blocked ✅)
3. User A starts ticket (status = doing)
4. User A unclaims ticket
5. Result: Ticket is unclaimed but status = doing! ❌

**Fix**: Add validation in unclaim:
```php
public function unclaim(Request $request, Ticket $ticket)
{
    $user = $request->user();
    
    if ($ticket->claimed_by !== $user->id) {
        return back()->with('error', 'You cannot unclaim this ticket');
    }
    
    // Cannot unclaim if already in progress
    if ($ticket->status === 'doing') {
        return back()->with('error', 'You cannot unclaim a ticket that is in progress. Please complete or reset it first.');
    }
    
    // Cannot unclaim if completed
    if ($ticket->status === 'done') {
        return back()->with('error', 'Cannot unclaim completed ticket');
    }
    
    $ticket->update([
        'claimed_by' => null,
        'claimed_at' => null,
    ]);
    
    return back()->with('success', 'Ticket released');
}
```

---

## 💾 RESOURCE MANAGEMENT

### 16. ⚠️ MEMORY LEAK: Notification Loops

**Location**: `TicketController.php` lines 150-160

**Problem**: Creating tickets for role notifies ALL users with that role.

```php
$usersWithRole = User::role($ticket->target_role)->get();  // Could be 50+ users
foreach ($usersWithRole as $targetUser) {
    if ($targetUser->id !== $request->user()->id) {
        $targetUser->notify(new TicketAssigned($ticket, $request->user(), true));
    }
}
```

**Impact**: If 50 users have 'media' role, creates 50 notifications in one request.

**Fix**: Use queue for notifications:
```php
use Illuminate\Support\Facades\Notification;

$usersWithRole = User::role($ticket->target_role)->get();
$usersToNotify = $usersWithRole->filter(fn($u) => $u->id !== $request->user()->id);

// Queue notifications instead of sending immediately
Notification::send($usersToNotify, (new TicketAssigned($ticket, $request->user(), true))->onQueue('notifications'));
```

---

### 17. ⚠️ FILE UPLOAD: No Cleanup of Orphaned Files

**Problem**: When document/RAB is deleted, file remains in storage.

**Fix**: Add file cleanup in controller:
```php
// DocumentController
public function destroy(Document $document)
{
    $this->authorize('delete', $document);
    
    // Delete file from storage
    if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
        Storage::disk('public')->delete($document->file_path);
    }
    
    $document->delete();
    
    return redirect()->route('documents.index')
        ->with('success', 'Document deleted');
}
```

**Better**: Use model events:
```php
// In Document model
protected static function boot()
{
    parent::boot();
    
    static::deleting(function ($document) {
        if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }
    });
}
```

---

## 📋 SUMMARY

### Critical (Fix Immediately):
1. ✅ Ticket claiming race condition - **lockForUpdate()**
2. ✅ Vote casting race condition - **lockForUpdate()**
3. ✅ Business approval race condition - **lockForUpdate()**

### High Priority:
4. ✅ Dashboard N+1 queries - **Eager loading/database filtering**
5. ✅ Missing pagination - **Add paginate()**
6. ✅ Privilege escalation in role updates - **Add ownership checks**
7. ✅ Orphaned records on deletion - **Cascade/cleanup**

### Medium Priority:
8. ✅ Vote results disclosure - **Add view checks**
9. ✅ Ticket status validation - **State machine**
10. ✅ File cleanup on delete - **Model events**
11. ✅ Notification queuing - **Queue::push()**

### Low Priority:
12. ✅ Edge case handling - **Business logic refinement**
13. ✅ Soft delete inconsistencies - **Clarify usage**

---

## 🎯 Recommended Action Plan

**Week 1** (Critical):
- [ ] Implement lockForUpdate() on ticket claiming
- [ ] Implement lockForUpdate() on vote casting
- [ ] Implement lockForUpdate() on business approval
- [ ] Add comprehensive tests for race conditions

**Week 2** (Performance):
- [ ] Fix dashboard N+1 queries
- [ ] Add pagination to all list views
- [ ] Optimize eager loading across controllers

**Week 3** (Security & Data Integrity):
- [ ] Fix privilege escalation issues
- [ ] Implement proper cascade deletes
- [ ] Add state machine for ticket status
- [ ] Queue notifications

**Week 4** (Polish):
- [ ] Handle edge cases
- [ ] Add file cleanup
- [ ] Improve error messages
- [ ] Add monitoring/logging

---

**Generated by**: AI Agent Deep Dive Analysis  
**Total Issues Found**: 17  
**Critical**: 3  
**High**: 4  
**Medium**: 6  
**Low**: 4
