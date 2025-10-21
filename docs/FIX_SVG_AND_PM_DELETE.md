# Fix: SVG Error dan PM Delete Permission

**Date**: October 21, 2025  
**Status**: ✅ Fixed

## Overview

Dua masalah diperbaiki:
1. **SVG Path Error** - Error arc flag pada icon blackout di kanban tickets
2. **PM Delete Permission** - PM tidak bisa menghapus proyek

## Problem 1: SVG Path Error

### Error Details

**Error Message**:
```
Error: <path> attribute d: Expected arc flag ('0' or '1'), 
"…28 12.728A9 9 0 715.636 5.636m12…".
```

**Location**: Halaman detail proyek (`projects.show`) - Kanban view untuk tickets

**Root Cause**: 
Invalid SVG arc command in blackout icon path. Missing spaces in arc parameters:
```
A9 9 0 715.636  ← WRONG (missing space, "715" should be "7 1 5")
```

### Solution

**File**: `resources/views/projects/show.blade.php` (Lines 542 & 873)

#### Before:
```blade
'blackout' => [
    'label' => 'Blackout', 
    'color' => 'gray', 
    'icon' => 'M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 715.636 5.636m12.728 12.728L5.636 5.636'
]
```

❌ **Problem**: `A9 9 0 7` - Missing space before arc flag "0"

#### After:
```blade
'blackout' => [
    'label' => 'Blackout', 
    'color' => 'gray', 
    'icon' => 'M18.364 18.364A9 9 0 0 0 5.636 5.636m12.728 12.728A9 9 0 0 0 5.636 5.636m12.728 12.728L5.636 5.636'
]
```

✅ **Solution**: `A9 9 0 0 0` - Added space before arc flags

### SVG Arc Command Format

```
A rx ry x-axis-rotation large-arc-flag sweep-flag x y
```

**Correct**: `A9 9 0 0 0 5.636 5.636`
- `rx=9` - x-radius
- `ry=9` - y-radius
- `rotation=0` - x-axis rotation
- `large-arc-flag=0` - 0 or 1
- `sweep-flag=0` - 0 or 1
- `x=5.636` - end x coordinate
- `y=5.636` - end y coordinate

**Wrong**: `A9 9 0 715.636 5.636` ← Missing spaces, flags merged with coordinates

## Problem 2: PM Delete Permission

### Issue Details

**Problem**: PM tidak dapat menghapus proyek (termasuk proyek completed/archived)

**Error Message**:
```
403 Forbidden
Anda tidak memiliki izin untuk menghapus proyek ini.
```

**Root Cause**: 
Authorization check di `ProjectController@destroy` hanya mengizinkan **owner** proyek untuk menghapus:

```php
if ($project->owner_id !== auth()->id()) {
    abort(403, 'Anda tidak memiliki izin...');
}
```

PM yang bukan owner tidak bisa menghapus proyek, padahal PM seharusnya punya akses penuh management.

### Solution

**File**: `app/Http/Controllers/ProjectController.php` (Line 361)

#### Before:
```php
// Check if the authenticated user is the project owner
if ($project->owner_id !== auth()->id()) {
    abort(403, 'Anda tidak memiliki izin untuk menghapus proyek ini.');
}
```

❌ **Problem**: Hanya owner yang bisa hapus

#### After:
```php
// Check if the authenticated user is the project owner OR has PM role
if ($project->owner_id !== auth()->id() && !auth()->user()->hasRole('pm')) {
    abort(403, 'Anda tidak memiliki izin untuk menghapus proyek ini. Hanya owner proyek atau PM yang dapat menghapus.');
}
```

✅ **Solution**: Owner **OR** PM bisa hapus

### Authorization Logic

```php
// Full method with proper authorization
public function destroy(Project $project)
{
    // 1. HEAD cannot delete (view-only)
    $user = Auth::user();
    if ($user->hasRole('head') && !$user->hasAnyRole(['pm', 'hr'])) {
        return back()->with('error', 'Role Head tidak dapat menghapus proyek...');
    }
    
    // 2. Owner OR PM can delete
    if ($project->owner_id !== auth()->id() && !auth()->user()->hasRole('pm')) {
        abort(403, 'Anda tidak memiliki izin... Hanya owner proyek atau PM yang dapat menghapus.');
    }
    
    // 3. Cannot delete active/planning projects
    if (in_array($project->status, ['active', 'planning'])) {
        return back()->with('error', 'Tidak dapat menghapus proyek yang masih aktif...');
    }
    
    // 4. Proceed with deletion
    // ...
}
```

### Permission Matrix

| User Role | Is Owner? | Can Delete? | Notes |
|-----------|-----------|-------------|-------|
| **PM** | No | ✅ Yes | NEW - PM can delete any completed project |
| **PM** | Yes | ✅ Yes | Owner + PM = full access |
| **HEAD** | No | ❌ No | View-only role |
| **HEAD** | Yes | ❌ No | HEAD restriction overrides ownership |
| **HR** | Yes | ✅ Yes | Owner can delete own project |
| **Member** | Yes | ✅ Yes | Owner can delete own project |
| **Member** | No | ❌ No | Not owner, not PM |

### Deletion Rules

**Can delete IF**:
- ✅ User is project owner OR has PM role
- ✅ Project status is `completed` or `archived`
- ✅ User does not have HEAD-only role

**Cannot delete IF**:
- ❌ Project status is `active` or `planning`
- ❌ User has HEAD-only role (even if owner)
- ❌ User is neither owner nor PM

### Status Protection

Projects with certain statuses **cannot be deleted**:

```php
if (in_array($project->status, ['active', 'planning'])) {
    return back()->with('error', 'Tidak dapat menghapus proyek yang masih aktif. Silakan ubah status proyek terlebih dahulu.');
}
```

**Protected statuses**:
- `active` - Project is currently running
- `planning` - Project is in planning phase

**Deletable statuses**:
- `completed` - Project finished successfully
- `archived` - Project archived/cancelled

**Workflow to delete active project**:
1. Change status to `completed` or `archived`
2. Then delete the project

## Testing

### Test 1: SVG Icon Renders Correctly

**Steps**:
1. Login as any user
2. Visit a project detail page (`/projects/{id}`)
3. Scroll to "Tiket" tab
4. View kanban columns

**Expected**:
- ✅ All 4 columns render (Blackout, To Do, Doing, Done)
- ✅ Blackout icon (crossed circle) displays correctly
- ✅ No SVG errors in browser console

### Test 2: PM Can Delete Completed Project

**Setup**:
- Create test project (owner: `dijah`)
- Set status to `completed`
- Login as `bhimo` (PM role)

**Steps**:
1. Visit project list (`/projects`)
2. Find the test project
3. Click "..." menu → "Hapus Proyek"
4. Confirm deletion

**Expected**:
- ✅ Project deleted successfully
- ✅ No 403 error
- ✅ Redirect to project list with success message

### Test 3: PM Cannot Delete Active Project

**Setup**:
- Project with status `active`
- Login as PM

**Steps**:
1. Try to delete active project

**Expected**:
- ❌ Error message: "Tidak dapat menghapus proyek yang masih aktif..."
- ✅ Project NOT deleted
- ✅ User redirected back to previous page

### Test 4: HEAD Cannot Delete Project

**Setup**:
- Login as `yahya` (HEAD role)
- Project with status `completed`

**Steps**:
1. Try to delete project (if button visible)

**Expected**:
- ❌ Error message: "Role Head tidak dapat menghapus proyek..."
- ✅ Project NOT deleted

### Test 5: Member Cannot Delete Other's Project

**Setup**:
- Project owned by `dijah`
- Login as `fachri` (member, not PM)

**Steps**:
1. Try to delete project

**Expected**:
- ❌ 403 error: "Anda tidak memiliki izin... Hanya owner proyek atau PM yang dapat menghapus."
- ✅ Project NOT deleted

## Related Files

### Modified Files:
1. `resources/views/projects/show.blade.php` - Fixed SVG path (2 locations)
2. `app/Http/Controllers/ProjectController.php` - Updated delete authorization

### Related Documentation:
- `docs/HEAD_AUTHORIZATION_IMPLEMENTATION.md` - HEAD authorization patterns
- `docs/DOUBLE_ROLE_IMPLEMENTATION.md` - Multi-role system

## Benefits

### SVG Fix:
- ✅ No more console errors
- ✅ Blackout icon displays correctly
- ✅ Better user experience

### PM Delete Permission:
- ✅ PM can manage projects fully (including deletion)
- ✅ PM can clean up completed/archived projects
- ✅ Consistent with PM role responsibilities
- ✅ Owner retains deletion rights
- ✅ HEAD protection still enforced

## Summary

**Problem 1**: SVG arc flag syntax error in blackout icon
**Solution**: Added proper spacing in arc command parameters

**Problem 2**: PM tidak bisa hapus proyek
**Solution**: PM sekarang bisa hapus proyek (owner OR PM logic)

**Impact**: 
- ✅ UI displays correctly without SVG errors
- ✅ PM has full project management capabilities
- ✅ HEAD remains view-only (cannot delete)
- ✅ Active projects protected from accidental deletion
