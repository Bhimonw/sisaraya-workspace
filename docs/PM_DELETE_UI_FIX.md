# Fix: PM Tidak Bisa Menghapus Proyek - UI Issue

**Date**: October 21, 2025  
**Status**: ✅ Fixed

## Problem

Setelah fix authorization di controller, PM **masih tidak bisa menghapus proyek** karena **tombol delete tidak muncul** di UI.

### Root Cause

**File**: `resources/views/projects/index.blade.php` (Line 221)

Kondisi untuk menampilkan menu dropdown terlalu ketat:

```blade
@if(auth()->user()->hasRole('pm') && $project->owner_id === auth()->id())
```

**Logic**: PM **AND** Owner (keduanya harus terpenuhi)

**Artinya**:
- ✅ PM yang juga owner → menu muncul
- ❌ PM yang bukan owner → menu **TIDAK muncul**
- ✅ Owner yang bukan PM → menu muncul

**Result**: PM yang bukan owner **tidak melihat tombol delete sama sekali**, walaupun authorization di controller sudah benar!

## Solution

### File Changed: `resources/views/projects/index.blade.php`

#### Before (Line 221):
```blade
{{-- Quick Actions Menu (PM only) --}}
@if(auth()->user()->hasRole('pm') && $project->owner_id === auth()->id())
    <div class="relative" x-data="{ open: false }">
        {{-- Menu dropdown dengan Edit, View, Delete --}}
    </div>
@endif
```

❌ **Problem**: Menu hanya muncul untuk PM yang **juga** owner

#### After (Line 221):
```blade
{{-- Quick Actions Menu (PM or Owner) --}}
@if(auth()->user()->hasRole('pm') || $project->owner_id === auth()->id())
    <div class="relative" x-data="{ open: false }">
        {{-- Menu dropdown dengan Edit, View, Delete --}}
    </div>
@endif
```

✅ **Solution**: Menu muncul untuk PM **atau** owner

### Logic Change

**Old Logic** (AND):
```php
hasRole('pm') && $project->owner_id === auth()->id()
```

**New Logic** (OR):
```php
hasRole('pm') || $project->owner_id === auth()->id()
```

### Truth Table

| User | Has PM? | Is Owner? | OLD (AND) | NEW (OR) | Menu Visible? |
|------|---------|-----------|-----------|----------|---------------|
| **Bhimo (PM)** | ✅ | ❌ | ❌ False | ✅ True | ✅ **NOW YES** |
| **Bhimo (PM + Owner)** | ✅ | ✅ | ✅ True | ✅ True | ✅ YES |
| **Dijah (Owner)** | ❌ | ✅ | ❌ False | ✅ True | ✅ YES |
| **Fachri (Member)** | ❌ | ❌ | ❌ False | ❌ False | ❌ NO |

**Key Change**: Bhimo (PM but not owner) now sees the menu!

## Full Authorization Flow

### Layer 1: UI (View) ✅
```blade
@if(auth()->user()->hasRole('pm') || $project->owner_id === auth()->id())
    {{-- Show menu with delete button --}}
@endif
```
- PM sees menu ✅
- Owner sees menu ✅
- Member doesn't see menu ✅

### Layer 2: Controller (Authorization) ✅
```php
// ProjectController@destroy
if ($project->owner_id !== auth()->id() && !auth()->user()->hasRole('pm')) {
    abort(403);
}
```
- PM can delete ✅
- Owner can delete ✅
- Member gets 403 ✅

### Layer 3: Business Logic (Status Check) ✅
```php
if (in_array($project->status, ['active', 'planning'])) {
    return back()->with('error', '...');
}
```
- Active/planning projects protected ✅
- Completed/archived can be deleted ✅

## Menu Items in Dropdown

When menu is visible, user can:

1. **Edit Proyek** - Modify project details
   - Visible for: PM or Owner
   
2. **Lihat Detail** - View project details
   - Visible for: PM or Owner (but everyone can access via card click)
   
3. **Hapus Proyek** - Delete project
   - Visible for: PM or Owner
   - Blocked by controller if: status is active/planning
   - Blocked by controller if: user is neither PM nor owner

## Why This Is Correct

### PM Should See Menu

PM (Project Manager) role has **full management capabilities**:
- Create projects ✅
- Edit any project ✅
- Delete completed projects ✅
- Manage project members ✅

Therefore, PM should see the management menu for **all projects**, not just their own.

### Owner Should See Menu

Project owner has rights to manage their own project:
- Edit their project ✅
- Delete their completed project ✅
- Add/remove members ✅

### Member Should NOT See Menu

Regular members don't have project management rights:
- Cannot edit projects ❌
- Cannot delete projects ❌
- Can only view and participate ✅

## Testing

### Test 1: PM Sees Menu for Non-Owned Project

**Setup**:
- Project: "Test Project" (owner: `dijah`)
- User: `bhimo` (role: PM, not owner)

**Before Fix**:
1. Login as `bhimo`
2. Visit `/projects`
3. Find "Test Project"
4. ❌ NO "..." menu button (because `pm && owner` = `true && false` = `false`)

**After Fix**:
1. Login as `bhimo`
2. Visit `/projects`
3. Find "Test Project"
4. ✅ SEE "..." menu button (because `pm || owner` = `true || false` = `true`)
5. Click "..." → see Edit, View, Delete options ✅

### Test 2: PM Can Delete Non-Owned Completed Project

**Setup**:
- Project: "Completed Project" (owner: `dijah`, status: `completed`)
- User: `bhimo` (PM, not owner)

**Steps**:
1. Login as `bhimo`
2. Visit `/projects`
3. Click "..." on "Completed Project"
4. Click "Hapus Proyek"
5. Confirm deletion

**Expected**:
- ✅ Project deleted successfully
- ✅ Redirect to project list with success message

### Test 3: PM Cannot Delete Active Project

**Setup**:
- Project: "Active Project" (status: `active`)
- User: `bhimo` (PM)

**Steps**:
1. Click "..." → "Hapus Proyek"
2. Confirm

**Expected**:
- ❌ Error: "Tidak dapat menghapus proyek yang masih aktif..."
- ✅ Project NOT deleted

### Test 4: Member Doesn't See Menu

**Setup**:
- Project: "Any Project" (owner: `dijah`)
- User: `fachri` (member, not owner, not PM)

**Steps**:
1. Login as `fachri`
2. Visit `/projects`
3. Look for "..." button

**Expected**:
- ❌ No "..." button visible
- ✅ Can only click card to view details

### Test 5: Owner Sees Menu for Own Project

**Setup**:
- Project: "My Project" (owner: `dijah`)
- User: `dijah` (owner, not PM)

**Steps**:
1. Login as `dijah`
2. Visit `/projects`
3. Find "My Project"

**Expected**:
- ✅ See "..." button
- ✅ Can edit, view, delete (if completed/archived)

## Related Changes

This fix completes the PM delete permission implementation:

1. ✅ **Controller Authorization** (Previous fix)
   - File: `app/Http/Controllers/ProjectController.php`
   - Change: `owner OR pm` logic

2. ✅ **UI Menu Visibility** (This fix)
   - File: `resources/views/projects/index.blade.php`
   - Change: `pm OR owner` logic

Both layers now consistent: **PM OR Owner**

## Impact

### Before

**PM Experience**:
- Can create projects ✅
- Can edit any project (via direct URL) ✅
- **Cannot see delete button** ❌ (UI hidden)
- Cannot delete via UI ❌

**Owner Experience**:
- Can edit own project ✅
- Can delete own project ✅
- Menu visible ✅

### After

**PM Experience**:
- Can create projects ✅
- Can edit any project ✅
- **Can see delete button** ✅ (UI visible)
- Can delete completed projects ✅

**Owner Experience**:
- Can edit own project ✅
- Can delete own project ✅
- Menu visible ✅
- **No changes** (same as before)

## Documentation

- ✅ `docs/FIX_SVG_AND_PM_DELETE.md` - Controller authorization fix
- ✅ `docs/PM_DELETE_UI_FIX.md` - This document (UI visibility fix)
- ✅ `docs/CHANGELOG.md` - Updated

## Summary

**Problem**: PM tidak bisa menghapus proyek karena tombol tidak muncul

**Root Cause**: UI kondisional menggunakan AND (`pm && owner`) instead of OR

**Solution**: Changed to OR (`pm || owner`)

**Result**: 
- ✅ PM sekarang melihat menu delete untuk semua proyek
- ✅ PM bisa menghapus completed/archived projects
- ✅ Owner tetap bisa menghapus proyek sendiri
- ✅ Member tetap tidak bisa lihat menu
- ✅ Active/planning projects tetap protected
