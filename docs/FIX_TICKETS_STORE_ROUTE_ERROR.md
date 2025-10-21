# Fix: Route [tickets.store] Not Defined Error

**Date**: October 21, 2025  
**Status**: ✅ Fixed

## Error Details

### Symptom
```
Symfony\Component\Routing\Exception\RouteNotFoundException
Route [tickets.store] not defined.
```

**Stack Trace Origin**: 
- File: `resources/views/components/tickets/create-modal.blade.php:45`
- Request: `GET /tickets`
- Controller: `TicketController@index`
- Middleware: `web, auth, role:pm|head`

### Root Cause

When implementing HEAD role access to ticket monitoring page, route `GET /tickets` was moved from `role:pm` group to `role:pm|head` group. However, the modal component `create-modal.blade.php` was referencing route `tickets.store` which was never properly defined.

**Route conflict**:
```php
// Line 93 - Throttle group
Route::post('tickets', [TicketController::class, 'store'])->name('tickets.store.limited');

// The modal was looking for 'tickets.store' (doesn't exist)
// But only 'tickets.store.limited' was defined
```

Laravel cannot have two routes with same URI and method, so `tickets.store` was never registered.

## Solution

### Changed File: `resources/views/components/tickets/create-modal.blade.php`

#### Before (Line 45):
```blade
<form method="POST" action="{{ route('tickets.store') }}" class="p-8 space-y-6">
    @csrf
    <input type="hidden" name="project_id" value="">
    <input type="hidden" name="context" value="umum">
```

❌ **Problem**: Route `tickets.store` doesn't exist

#### After (Line 45):
```blade
<form method="POST" action="{{ route('tickets.storeGeneral') }}" class="p-8 space-y-6">
    @csrf
    <input type="hidden" name="context" value="umum">
```

✅ **Solution**: Use `tickets.storeGeneral` which is the correct route for creating general tickets

### Why This Is Correct

The modal component is specifically for **"Buat Tiket Umum"** (Create General Ticket), which is a PM-only feature. The route `tickets.storeGeneral` is the proper endpoint for this action:

```php
// routes/web.php Line 67
Route::middleware('role:pm')->group(function () {
    Route::get('tickets/general/create', [TicketController::class, 'createGeneral'])->name('tickets.createGeneral');
    Route::post('tickets/general', [TicketController::class, 'storeGeneral'])->name('tickets.storeGeneral');
});
```

**Authorization**: Both controller method and route are protected with `role:pm` middleware

## Route Architecture

### Ticket Creation Routes

| Route Name | URI | Method | Middleware | Purpose |
|------------|-----|--------|------------|---------|
| `tickets.storeGeneral` | `/tickets/general` | POST | `role:pm` | Create general ticket for all members (PM only) |
| `tickets.store.limited` | `/tickets` | POST | `throttle:20,1` | Rate-limited ticket creation (legacy/unused) |
| `projects.tickets.store` | `/projects/{project}/tickets` | POST | `auth` | Create ticket within specific project |

## Verification

### Routes Check

```bash
php artisan route:list --name=tickets.storeGeneral
```

Output:
```
POST  tickets/general  tickets.storeGeneral › TicketController@storeGeneral
```

✅ Route exists and accessible

## Testing

### Test Scenario: PM Creates General Ticket

1. Login as PM (e.g., `bhimo` - password: `password`)
2. Visit `/tickets` → Page loads ✅
3. Click "Buat Tiket Umum" button
4. Modal opens ✅
5. Fill form and submit → `POST /tickets/general`
6. Ticket created successfully ✅

### Test Scenario: HEAD Views Ticket Monitoring

1. Login as HEAD (e.g., `yahya` - password: `password`)
2. Visit `/tickets` → Page loads ✅ (no more route error)
3. Can view all tickets in list ✅

## Summary

**Problem**: Modal component referenced non-existent route `tickets.store`

**Solution**: Changed modal to use `tickets.storeGeneral` (the correct, existing route)

**Result**: 
- ✅ Error fixed
- ✅ HEAD can access `/tickets` monitoring page
- ✅ PM can create general tickets via modal
- ✅ No breaking changes
