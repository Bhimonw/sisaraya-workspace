# HEAD Role UI Enhancement - Hide Create Ticket Button

**Date**: October 21, 2025  
**Status**: ✅ Implemented

## Overview

Tombol "Buat Tiket Umum" dan modal create ticket sekarang **disembunyikan** untuk role HEAD. HEAD hanya dapat melihat tiket (monitoring) tanpa bisa membuat tiket umum.

## Changes Made

### File: `resources/views/tickets/index.blade.php`

#### 1. Hide "Buat Tiket Umum" Button for HEAD

**Before**:
```blade
<div class="flex items-center gap-3">
    <button @click="showCreateModal = true" 
            class="inline-flex items-center...">
        <svg>...</svg>
        Buat Tiket Umum
    </button>
</div>
```

❌ **Problem**: Button visible to all users including HEAD

**After**:
```blade
<div class="flex items-center gap-3">
    @if(!auth()->user()->hasRole('head') || auth()->user()->hasRole('pm'))
        {{-- Button only visible to PM or users without HEAD role --}}
        <button @click="showCreateModal = true" 
                class="inline-flex items-center...">
            <svg>...</svg>
            Buat Tiket Umum
        </button>
    @else
        {{-- Info badge for HEAD role --}}
        <div class="inline-flex items-center px-4 py-2 bg-amber-50 border border-amber-200 rounded-lg">
            <svg class="h-5 w-5 text-amber-500 mr-2">...</svg>
            <span class="text-sm font-medium text-amber-700">Mode Monitoring</span>
            <span class="ml-2 text-xs text-amber-600">View Only</span>
        </div>
    @endif
</div>
```

✅ **Solution**: 
- Button only visible if user is NOT HEAD-only (PM can see it even with HEAD role)
- HEAD sees "Mode Monitoring - View Only" badge instead

#### 2. Don't Render Modal Component for HEAD

**Before**:
```blade
{{-- Modal Create Tiket Umum Component --}}
@include('components.tickets.create-modal')
```

❌ **Problem**: Modal always rendered (unnecessary for HEAD)

**After**:
```blade
{{-- Modal Create Tiket Umum Component (PM only) --}}
@if(!auth()->user()->hasRole('head') || auth()->user()->hasRole('pm'))
    @include('components.tickets.create-modal')
@endif
```

✅ **Solution**: Modal only rendered for users who can use it (PM)

## Logic Explanation

### Conditional Display Logic

```blade
@if(!auth()->user()->hasRole('head') || auth()->user()->hasRole('pm'))
    {{-- Show button/modal --}}
@else
    {{-- Show info badge --}}
@endif
```

**Truth Table**:

| User Role | Has HEAD? | Has PM? | Show Button? | Show Badge? |
|-----------|-----------|---------|--------------|-------------|
| PM only | ❌ | ✅ | ✅ Yes | ❌ No |
| HEAD only | ✅ | ❌ | ❌ No | ✅ Yes |
| PM + HEAD | ✅ | ✅ | ✅ Yes | ❌ No |
| HR only | ❌ | ❌ | ✅ Yes | ❌ No |
| Member only | ❌ | ❌ | ✅ Yes | ❌ No |

**Key Points**:
- HEAD-only users → See badge, no button
- PM (with or without HEAD) → See button
- Other roles → See button (but authorization will block at controller level)

## Visual Changes

### For HEAD Role (e.g., Yahya)

**Before**:
```
┌─────────────────────────────────────────┐
│ Manajemen Tiket     [Buat Tiket Umum]  │
└─────────────────────────────────────────┘
```
- Button visible
- Clicking would show modal
- Submitting would fail with 403

**After**:
```
┌─────────────────────────────────────────────────────┐
│ Manajemen Tiket   [👁 Mode Monitoring | View Only] │
└─────────────────────────────────────────────────────┘
```
- Badge replaces button
- No modal component loaded
- Clear indication of view-only access

### For PM Role (e.g., Bhimo)

**Unchanged**:
```
┌─────────────────────────────────────────┐
│ Manajemen Tiket     [Buat Tiket Umum]  │
└─────────────────────────────────────────┘
```
- Button still visible
- Modal still works
- Can create general tickets

### For PM + HEAD Role

**Shows PM functionality**:
```
┌─────────────────────────────────────────┐
│ Manajemen Tiket     [Buat Tiket Umum]  │
└─────────────────────────────────────────┘
```
- PM permissions override HEAD restrictions
- Full access to create general tickets

## Badge Styling

The "Mode Monitoring" badge uses amber color scheme to match HEAD's UI theme:

```blade
<div class="inline-flex items-center px-4 py-2 bg-amber-50 border border-amber-200 rounded-lg">
    <svg class="h-5 w-5 text-amber-500 mr-2">
        <!-- Eye icon -->
    </svg>
    <span class="text-sm font-medium text-amber-700">Mode Monitoring</span>
    <span class="ml-2 text-xs text-amber-600">View Only</span>
</div>
```

**Colors**:
- Background: `bg-amber-50` (light amber)
- Border: `border-amber-200` (medium amber)
- Icon: `text-amber-500` (amber)
- Text: `text-amber-700` (dark amber)
- Secondary: `text-amber-600` (medium-dark amber)

Consistent with HEAD menu sidebar amber theme.

## Benefits

### 1. Better UX
- ✅ HEAD doesn't see buttons they can't use
- ✅ Clear visual indication of access level
- ✅ No confusing 403 errors after clicking button

### 2. Performance
- ✅ Modal component not loaded for HEAD (saves DOM elements)
- ✅ No unnecessary JavaScript in Alpine.js state
- ✅ Slightly faster page load

### 3. Security (Defense in Depth)
- ✅ UI-level hiding (first defense)
- ✅ Route middleware `role:pm` (second defense)
- ✅ Controller authorization check (third defense)

Even if someone bypasses UI, route and controller will still block.

## Authorization Layers

### Layer 1: UI (View)
```blade
@if(!auth()->user()->hasRole('head') || auth()->user()->hasRole('pm'))
    <button>Buat Tiket Umum</button>
@endif
```
✅ Button hidden for HEAD-only users

### Layer 2: Route Middleware
```php
// routes/web.php
Route::middleware('role:pm')->group(function () {
    Route::post('tickets/general', [TicketController::class, 'storeGeneral']);
});
```
✅ Route only accessible to PM

### Layer 3: Controller Authorization
```php
// TicketController@storeGeneral
if ($user->hasRole('head') && !$user->hasAnyRole(['pm'])) {
    return back()->withErrors(['message' => 'Role Head tidak dapat membuat tiket umum...']);
}
```
✅ Controller checks role before processing

**Result**: HEAD role is blocked at all three layers.

## Testing

### Test as HEAD (Yahya)

**Username**: `yahya`  
**Password**: `password`  
**Roles**: `['pr', 'head']`

**Expected Behavior**:
1. Visit `/tickets` → Page loads ✅
2. See header with "Mode Monitoring - View Only" badge ✅
3. No "Buat Tiket Umum" button visible ✅
4. Can view all tickets in list ✅
5. Can click tickets to view details ✅
6. Can claim and work on tickets ✅

**Should NOT see**:
- ❌ "Buat Tiket Umum" button
- ❌ Create ticket modal

### Test as PM (Bhimo)

**Username**: `bhimo`  
**Password**: `password`  
**Roles**: `['pm', 'sekretaris']`

**Expected Behavior**:
1. Visit `/tickets` → Page loads ✅
2. See "Buat Tiket Umum" button ✅
3. Click button → Modal opens ✅
4. Fill form and submit → Ticket created ✅

**Should see**:
- ✅ "Buat Tiket Umum" button
- ✅ Create ticket modal

### Test as PM + HEAD (Hypothetical)

If a user has both `pm` and `head` roles:

**Expected Behavior**:
- PM permissions take precedence
- See "Buat Tiket Umum" button ✅
- Can create general tickets ✅

Logic: `(!hasRole('head') || hasRole('pm'))` = `(!true || true)` = `true`

## Comparison with Other Views

### Tickets Mine (`tickets/mine.blade.php`)
- No create button needed (personal view)
- HEAD can access ✅

### Tickets Overview (`tickets/overview.blade.php`)
- Historical view only
- HEAD can access ✅

### Tickets Index (`tickets/index.blade.php`)
- Management view with create button
- HEAD can access for monitoring ✅
- Create button hidden for HEAD ✅

### Tickets Create General (`tickets/create_general.blade.php`)
- Full form page (alternative to modal)
- HEAD blocked by route middleware (`role:pm`) ✅

## Related Documentation

- `docs/HEAD_ROLE_MENU.md` - HEAD menu structure
- `docs/HEAD_AUTHORIZATION_IMPLEMENTATION.md` - Authorization patterns
- `docs/HEAD_TICKET_PERMISSIONS.md` - Ticket permissions matrix
- `docs/HEAD_TICKET_MONITORING_ACCESS.md` - Monitoring access implementation

## Summary

**Changes**:
1. ✅ Hidden "Buat Tiket Umum" button for HEAD-only users
2. ✅ Hidden modal component for HEAD-only users
3. ✅ Added "Mode Monitoring - View Only" badge for HEAD
4. ✅ PM retains full access (even with HEAD role)

**Benefits**:
- Better UX (no confusing buttons)
- Clearer access indication
- Slightly better performance
- Defense in depth (UI + route + controller)

**No breaking changes**:
- PM functionality unchanged
- HEAD can still monitor tickets
- Multi-role users (PM+HEAD) work correctly
