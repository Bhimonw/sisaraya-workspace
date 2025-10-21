# HEAD Role - Ticket Permissions

**Date**: October 21, 2025  
**Status**: ✅ Clarified

## Overview

Role `head` memiliki akses **view + claim + execute** untuk tiket, tetapi **tidak bisa management** (create tiket untuk orang lain).

## What HEAD CAN Do ✅

### 1. View Tickets
- ✅ **Lihat semua tiket** (`tickets.index`)
  - Route: `GET /tickets`
  - Akses halaman "Manajemen Tiket" (read-only)
  - Lihat semua tiket (project tickets + general tickets)

- ✅ **Lihat detail tiket** (`tickets.show`)
  - Route: `GET /tickets/{id}`
  - Lihat informasi lengkap tiket
  - Lihat history & comments

- ✅ **Lihat tiketku** (`tickets.mine`)
  - Route: `GET /tickets/mine`
  - Lihat tiket yang di-claim oleh HEAD
  - Lihat tiket yang assigned ke role HEAD

- ✅ **Lihat overview tiket** (`tickets.overview`)
  - Route: `GET /tickets/overview`
  - Lihat semua tiket yang pernah dikerjakan
  - Filter & search

### 2. Claim & Unclaim Tickets
- ✅ **Claim tiket** (`tickets.claim`)
  - Route: `POST /tickets/{id}/claim`
  - Claim tiket yang available (belum di-claim)
  - Claim tiket yang assigned ke role HEAD

- ✅ **Unclaim tiket** (`tickets.unclaim`)
  - Route: `POST /tickets/{id}/unclaim`
  - Release tiket yang sudah di-claim
  - Kembalikan tiket ke pool

### 3. Execute Tasks (Update Status)
- ✅ **Start tiket** (`tickets.start`)
  - Route: `POST /tickets/{id}/start`
  - Ubah status dari `todo` → `doing`
  - Set `started_at` timestamp

- ✅ **Complete tiket** (`tickets.complete`)
  - Route: `POST /tickets/{id}/complete`
  - Ubah status dari `doing` → `done`
  - Set `completed_at` timestamp

- ✅ **Set to Todo** (`tickets.setTodo`)
  - Route: `POST /tickets/{id}/set-todo`
  - Ubah status kembali ke `todo`
  - Reset timestamps

### 4. Participate in Projects
- ✅ **Create tiket di proyek** (`tickets.store` with project)
  - Route: `POST /projects/{project}/tickets`
  - Buat tiket untuk proyek yang HEAD ikuti
  - Assign ke diri sendiri atau member proyek

## What HEAD CANNOT Do ❌

### 1. Create General Tickets
- ❌ **Create tiket umum** (`tickets.createGeneral`)
  - Route: `GET /tickets/general/create`
  - **Blocked with 403**
  - Hanya PM yang bisa create tiket untuk semua anggota

- ❌ **Store tiket umum** (`tickets.storeGeneral`)
  - Route: `POST /tickets/general`
  - **Blocked with error message**
  - Redirect ke index dengan error

### 2. Manage Tickets for Others
- ❌ **Assign tiket ke user lain**
  - Tidak bisa set `target_user_id` untuk orang lain
  - Hanya bisa assign ke diri sendiri

- ❌ **Edit tiket orang lain**
  - Tidak ada akses ke form edit tiket general
  - Hanya bisa edit tiket yang di-claim sendiri

- ❌ **Delete tiket**
  - Tidak ada permission untuk delete
  - Hanya PM/owner yang bisa delete

## Permission Matrix

| Action | HEAD Only | HEAD + PM | PM Only | Notes |
|--------|-----------|-----------|---------|-------|
| **View All Tickets** | ✅ | ✅ | ✅ | Index page |
| **View Ticket Detail** | ✅ | ✅ | ✅ | Show page |
| **View My Tickets** | ✅ | ✅ | ✅ | Mine page |
| **Claim Ticket** | ✅ | ✅ | ✅ | Available tickets |
| **Unclaim Ticket** | ✅ | ✅ | ✅ | Own tickets |
| **Start/Complete** | ✅ | ✅ | ✅ | Own tickets |
| **Create in Project** | ✅ | ✅ | ✅ | Project member |
| **Create General** | ❌ 403 | ✅ | ✅ | PM only |
| **Assign to Others** | ❌ | ✅ | ✅ | PM only |
| **Delete Ticket** | ❌ | ✅ | ✅ (owner) | PM or owner |

## Routes Overview

### Accessible to HEAD ✅

```php
// View routes
GET  /tickets                    → index (all tickets)
GET  /tickets/mine               → mine (my tickets)
GET  /tickets/overview           → overview (history)
GET  /tickets/{id}               → show (detail)

// Action routes (claimed tickets)
POST /tickets/{id}/claim         → claim
POST /tickets/{id}/unclaim       → unclaim
POST /tickets/{id}/start         → start (todo → doing)
POST /tickets/{id}/complete      → complete (doing → done)
POST /tickets/{id}/set-todo      → setTodo (reset to todo)

// Create in project (if member)
POST /projects/{project}/tickets → store (create ticket in project)
```

### Blocked for HEAD ❌

```php
// Management routes (PM only)
GET  /tickets/general/create     → createGeneral (403)
POST /tickets/general            → storeGeneral (error)

// Move/reassign (PM only)
POST /tickets/{id}/move          → move (change project)
```

## Implementation

### Authorization in TicketController

```php
// Already implemented
public function createGeneral()
{
    $user = Auth::user();
    if ($user->hasRole('head') && !$user->hasAnyRole(['pm'])) {
        abort(403, 'Role Head tidak dapat membuat tiket umum...');
    }
    return view('tickets.create_general');
}

public function storeGeneral(Request $request)
{
    $user = Auth::user();
    if ($user->hasRole('head') && !$user->hasAnyRole(['pm'])) {
        return back()->withErrors([...])->withInput();
    }
    // ... rest of code
}
```

### No Additional Authorization Needed

Methods yang sudah accessible tanpa tambahan authorization:
- `index()` - View all tickets ✅
- `show()` - View detail ✅
- `mine()` - View my tickets ✅
- `overview()` - View history ✅
- `claim()` - Claim ticket ✅
- `unclaim()` - Release ticket ✅
- `start()` - Start work ✅
- `complete()` - Mark done ✅
- `setTodo()` - Reset to todo ✅
- `store()` - Create in project ✅

## Use Cases

### Scenario 1: HEAD Claims General Ticket

1. PM creates general ticket for role `pr`
2. HEAD (Yahya has role `pr` + `head`) sees ticket in `/tickets/mine`
3. HEAD clicks "Claim" → ticket assigned to Yahya
4. HEAD clicks "Start" → status changes to `doing`
5. HEAD works on task
6. HEAD clicks "Complete" → status changes to `done`

✅ **This flow works perfectly for HEAD**

### Scenario 2: HEAD Tries to Create General Ticket

1. HEAD visits `/tickets` (Manajemen Tiket)
2. HEAD sees "Buat Tiket Umum" button (if not hidden)
3. HEAD clicks → redirects to `/tickets/general/create`
4. ❌ **403 Forbidden** with message:
   > "Role Head tidak dapat membuat tiket umum. Hanya PM yang dapat membuat tiket untuk semua anggota."

✅ **This is expected behavior**

### Scenario 3: HEAD Creates Ticket in Project

1. HEAD is member of "Project X"
2. HEAD visits project detail page
3. HEAD clicks "Buat Tiket" in project
4. HEAD fills form (title, description, assign to self)
5. HEAD submits → ticket created in project
6. ✅ **HEAD can create tickets in projects they're part of**

✅ **This is allowed and expected**

## View-Level Improvements (Optional)

Hide "Create General Ticket" button untuk HEAD:

```blade
{{-- tickets/index.blade.php --}}
@if(!auth()->user()->hasRole('head') || auth()->user()->hasRole('pm'))
    <a href="{{ route('tickets.createGeneral') }}" class="btn btn-primary">
        <svg>...</svg>
        Buat Tiket Umum
    </a>
@endif
```

Add info badge for HEAD:

```blade
{{-- tickets/index.blade.php --}}
@if(auth()->user()->hasRole('head') && !auth()->user()->hasRole('pm'))
    <div class="bg-amber-50 border-l-4 border-amber-400 p-4 mb-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-amber-400">...</svg>
            </div>
            <div class="ml-3">
                <p class="text-sm text-amber-700">
                    <strong>Mode View & Execute:</strong> Anda dapat melihat dan mengerjakan tiket, tetapi tidak dapat membuat tiket umum untuk anggota lain.
                </p>
            </div>
        </div>
    </div>
@endif
```

## Summary

**HEAD Role Ticket Access:**
- ✅ **View**: All tickets, my tickets, overview, detail
- ✅ **Claim**: Available tickets assigned to HEAD's roles
- ✅ **Execute**: Start, complete, todo for claimed tickets
- ✅ **Create**: Tickets in projects where HEAD is member
- ❌ **Manage**: Cannot create general tickets for all members
- ❌ **Assign**: Cannot assign tickets to other users

**This is the intended behavior** - HEAD dapat berkontribusi langsung dengan mengerjakan tiket, tetapi tidak dapat melakukan management activities yang seharusnya dilakukan oleh PM.

## Testing

Login as `yahya` (role: `head` + `pr`):

### Should Work ✅
1. Go to `/tickets` → see all tickets
2. Go to `/tickets/mine` → see tickets for PR role
3. Click "Claim" on available ticket → success
4. Click "Start" on claimed ticket → status changes
5. Click "Complete" → status changes to done
6. Go to project detail (where Yahya is member) → can create ticket

### Should Fail ❌
1. Go to `/tickets/general/create` → 403 Forbidden
2. Try to access form to create general ticket → blocked
3. Try to assign ticket to other user (if form accessible) → validation error

## Related Files

- ✅ `app/Http/Controllers/TicketController.php` - Authorization implemented
- ✅ `database/seeders/RolePermissionSeeder.php` - Permissions defined
- 📝 `resources/views/tickets/index.blade.php` - Can hide button (optional)
- 📝 `resources/views/tickets/mine.blade.php` - Already works as-is
- 📝 `resources/views/tickets/show.blade.php` - Already works as-is
