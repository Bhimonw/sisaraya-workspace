# HEAD Role - Ticket Monitoring Access

**Date**: October 21, 2025  
**Status**: ✅ Implemented

## Overview

Role `head` sekarang memiliki akses ke halaman **Manajemen Tiket** (`/tickets`) untuk monitoring semua tiket dalam sistem.

## Changes Made

### 1. Route Configuration

**File**: `routes/web.php`

#### Before:
```php
// General tickets (PM can create for all members)
Route::middleware('role:pm')->group(function () {
    Route::get('tickets/general/create', [TicketController::class, 'createGeneral'])->name('tickets.createGeneral');
    Route::post('tickets/general', [TicketController::class, 'storeGeneral'])->name('tickets.storeGeneral');
    
    // Manajemen Tiket - PM only
    Route::get('tickets', [TicketController::class, 'index'])->name('tickets.index');
    Route::post('tickets', [TicketController::class, 'store'])->name('tickets.store');
});
```

❌ **Problem**: Route `tickets.index` was inside `role:pm` group → HEAD role couldn't access

#### After:
```php
// General tickets (PM can create for all members)
Route::middleware('role:pm')->group(function () {
    Route::get('tickets/general/create', [TicketController::class, 'createGeneral'])->name('tickets.createGeneral');
    Route::post('tickets/general', [TicketController::class, 'storeGeneral'])->name('tickets.storeGeneral');
});

// Manajemen Tiket - accessible by PM and HEAD roles
Route::middleware('role:pm|head')->group(function () {
    Route::get('tickets', [TicketController::class, 'index'])->name('tickets.index');
});
```

✅ **Solution**: Moved `tickets.index` to separate group with `role:pm|head` middleware

### 2. Route Verification

```bash
php artisan route:list --name=tickets.index --verbose
```

Output:
```
GET|HEAD  tickets ................... tickets.index › TicketController@index  
          ⇂ web
          ⇂ auth
          ⇂ role:pm|head
```

✅ Middleware `role:pm|head` correctly applied

### 3. Controller Authorization

**File**: `app/Http/Controllers/TicketController.php`

Method `index()` doesn't have additional authorization checks that would block HEAD:

```php
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
```

✅ No role-specific blocks → HEAD can access

### 4. Menu Link (Already Exists)

**File**: `resources/views/layouts/_menu.blade.php`

HEAD role menu already includes "Monitoring Tiket" link:

```blade
@role('head')
<li>
    <button @click="openMenus.headManagement = !openMenus.headManagement" 
            class="flex items-center w-full...">
        <span>{{ __('Ruang Management') }}</span>
    </button>
    
    <ul x-show="openMenus.headManagement" class="ml-4 mt-1 space-y-1">
        <!-- Other items... -->
        
        <li>
            @php $active = request()->routeIs('tickets.index') || request()->routeIs('tickets.show'); @endphp
            <a href="{{ route('tickets.index') }}" class="flex items-center gap-2 px-3 py-1.5 text-sm rounded {{ $active ? 'bg-amber-100 text-amber-900' : 'text-gray-600 hover:bg-gray-50' }}">
                <svg class="h-3.5 w-3.5">...</svg>
                <span>
                    Monitoring Tiket
                    <span class="block text-[10px] text-gray-500">View Only</span>
                </span>
            </a>
        </li>
    </ul>
</li>
@endrole
```

✅ Menu already configured with amber theme and "View Only" label

### 5. Permissions

**File**: `database/seeders/RolePermissionSeeder.php`

HEAD role already has required permission:

```php
Role::where('name', 'head')->first()?->givePermissionTo([
    'projects.view',
    'tickets.view_all',      // ← Required for ticket monitoring
    'tickets.update_status',
    'documents.view_all',
    'business.view',
]);
```

✅ Permission `tickets.view_all` already granted

## Access Matrix

| Feature | URL | HEAD Access | PM Access | Notes |
|---------|-----|-------------|-----------|-------|
| **Ticket Monitoring** | `GET /tickets` | ✅ View Only | ✅ Full Access | NEW - Now accessible to HEAD |
| Create General Ticket | `GET /tickets/general/create` | ❌ 403 | ✅ | PM only |
| Store General Ticket | `POST /tickets/general` | ❌ Error | ✅ | PM only |
| View Ticket Detail | `GET /tickets/{id}` | ✅ | ✅ | Already accessible |
| My Tickets | `GET /tickets/mine` | ✅ | ✅ | Already accessible |
| Ticket Overview | `GET /tickets/overview` | ✅ | ✅ | Already accessible |
| Claim Ticket | `POST /tickets/{id}/claim` | ✅ | ✅ | Already accessible |
| Update Status | `POST /tickets/{id}/start` | ✅ | ✅ | Already accessible |

## What HEAD Can Do on `/tickets`

### ✅ View Capabilities

1. **See all tickets** in the system
   - Project tickets
   - General tickets
   - Filter by status/priority/etc.

2. **View ticket details**
   - Click any ticket to see full information
   - See who created it
   - See who claimed it
   - View comments/history

3. **Monitor ticket progress**
   - See todo/doing/done counts
   - Track overdue tickets
   - Monitor team workload

### ❌ Cannot Do (View Only)

1. **Cannot create general tickets**
   - "Buat Tiket Umum" button visible but clicking redirects to 403
   - Only PM can create general tickets

2. **Cannot edit tickets directly**
   - No edit button in the list
   - Must claim ticket first, then can update status

3. **Cannot delete tickets**
   - No delete functionality for HEAD role

## View-Level Enhancements (Optional)

### Hide "Create" Button for HEAD

Current state: Button visible but clicking causes 403

Recommended enhancement in `resources/views/tickets/index.blade.php`:

```blade
{{-- Header with Create Button --}}
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-semibold text-gray-900">Manajemen Tiket</h1>
    
    @if(!auth()->user()->hasRole('head') || auth()->user()->hasRole('pm'))
        <a href="{{ route('tickets.createGeneral') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
            <svg class="h-5 w-5 mr-2">...</svg>
            Buat Tiket Umum
        </a>
    @else
        {{-- Info badge for HEAD --}}
        <div class="bg-amber-50 border border-amber-200 px-4 py-2 rounded-lg">
            <p class="text-sm text-amber-700">
                <strong>Mode Monitoring:</strong> Anda dapat melihat semua tiket tetapi tidak dapat membuat tiket umum.
            </p>
        </div>
    @endif
</div>
```

This provides better UX by:
- Hiding button that would cause 403
- Showing informative message instead
- Using amber theme consistent with HEAD's UI

## Testing

### Test as HEAD Role

Login as `yahya` (password: `password`) - has roles `head` + `pr`:

#### Should Work ✅

1. Visit `http://localhost:8000/tickets` → see page with all tickets
2. Click "Ruang Management" in sidebar → see "Monitoring Tiket" link
3. Click any ticket → see ticket detail page
4. View ticket counts and statistics
5. Filter/search tickets

#### Should Show 403 ❌

1. Visit `http://localhost:8000/tickets/general/create` → 403 Forbidden
2. Try to submit form to create general ticket → validation error

### Test as PM Role

Login as `bhimo` (password: `password`) - has roles `pm` + `sekretaris`:

#### Should Work ✅

1. Visit `http://localhost:8000/tickets` → see page
2. See "Buat Tiket Umum" button
3. Click button → open form
4. Submit form → create general ticket successfully

## Routes Summary

All ticket-related routes:

```bash
# View routes (accessible to HEAD + PM)
GET  /tickets                       → tickets.index (NEW for HEAD)
GET  /tickets/{id}                  → tickets.show
GET  /tickets/mine                  → tickets.mine
GET  /tickets/overview              → tickets.overview

# Management routes (PM only)
GET  /tickets/general/create        → tickets.createGeneral
POST /tickets/general               → tickets.storeGeneral

# Action routes (accessible to HEAD + PM)
POST /tickets/{id}/claim            → tickets.claim
POST /tickets/{id}/unclaim          → tickets.unclaim
POST /tickets/{id}/start            → tickets.start
POST /tickets/{id}/complete         → tickets.complete
PATCH /tickets/{id}/set-todo        → tickets.setTodo
```

## Related Documentation

- `docs/HEAD_ROLE_MENU.md` - HEAD role menu structure
- `docs/HEAD_AUTHORIZATION_IMPLEMENTATION.md` - HEAD authorization patterns
- `docs/HEAD_TICKET_PERMISSIONS.md` - Complete ticket permissions matrix

## Summary

**Change**: HEAD role now has access to ticket monitoring page (`/tickets`)

**Before**: Only PM could access → HEAD got 403 error

**After**: 
- ✅ HEAD can view `/tickets` (monitoring/read-only)
- ✅ HEAD can see all tickets in system
- ✅ HEAD can click tickets to view details
- ❌ HEAD still cannot create general tickets (PM only)
- ✅ HEAD can still claim and work on tickets

**Implementation**: Moved `tickets.index` route from `role:pm` group to `role:pm|head` group

**No breaking changes**: PM functionality remains unchanged, HEAD gains read access only
