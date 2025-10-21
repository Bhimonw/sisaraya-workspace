# HEAD Role Authorization Implementation

**Date**: October 21, 2025  
**Status**: ✅ Implemented

## Overview

Implementasi authorization untuk memastikan role `head` hanya memiliki akses **view-only** dan tidak dapat melakukan operasi create, update, atau delete pada resource utama (projects, businesses, general tickets).

## Permissions Update

### New Permission Added

Menambahkan `projects.delete` sebagai permission terpisah untuk lebih granular control.

### HEAD Role Permissions

```php
Role::where('name', 'head')->first()?->givePermissionTo([
    'projects.view',         // ✅ View all projects (READ ONLY)
    'tickets.view_all',      // ✅ View all tickets (READ ONLY)
    'tickets.update_status', // ✅ Can update status of claimed tickets
    'documents.view_all',    // ✅ View public documents (READ ONLY)
    'business.view'          // ✅ View all businesses (READ ONLY)
]);
```

**What HEAD CANNOT do** (permissions NOT granted):
- ❌ `projects.create` - Cannot create projects
- ❌ `projects.update` - Cannot edit projects
- ❌ `projects.delete` - Cannot delete projects
- ❌ `projects.manage_members` - Cannot add/remove members
- ❌ `tickets.create` - Cannot create general tickets
- ❌ `business.create` - Cannot create business proposals
- ❌ `business.update` - Cannot edit businesses
- ❌ `business.approve` - Cannot approve/reject proposals
- ❌ `documents.upload` - Cannot upload documents

## Controller Authorization

### 1. ProjectController

**Location**: `app/Http/Controllers/ProjectController.php`

#### create() method
```php
public function create()
{
    // Head role cannot create projects (view-only access)
    $user = Auth::user();
    if ($user->hasRole('head') && !$user->hasAnyRole(['pm', 'hr'])) {
        abort(403, 'Role Head tidak dapat membuat proyek baru. Silakan hubungi PM untuk membuat proyek.');
    }
    
    return view('projects.create');
}
```

#### store() method
```php
public function store(Request $request)
{
    // Head role cannot create projects (view-only access)
    $user = Auth::user();
    if ($user->hasRole('head') && !$user->hasAnyRole(['pm', 'hr'])) {
        return back()->withErrors(['error' => 'Role Head tidak dapat membuat proyek baru. Silakan hubungi PM untuk membuat proyek.'])->withInput();
    }
    
    // ... rest of code
}
```

#### edit() method
```php
public function edit(Project $project)
{
    // Head role cannot edit projects (view-only access)
    $user = Auth::user();
    if ($user->hasRole('head') && !$user->hasAnyRole(['pm', 'hr'])) {
        abort(403, 'Role Head tidak dapat mengedit proyek. Hanya dapat melihat informasi proyek.');
    }
    
    $project->load('members');
    return view('projects.edit', compact('project'));
}
```

#### update() method
```php
public function update(Request $request, Project $project)
{
    // Head role cannot update projects (view-only access)
    $user = Auth::user();
    if ($user->hasRole('head') && !$user->hasAnyRole(['pm', 'hr'])) {
        return back()->withErrors(['error' => 'Role Head tidak dapat mengedit proyek. Hanya dapat melihat informasi proyek.'])->withInput();
    }
    
    // ... rest of code
}
```

#### destroy() method
```php
public function destroy(Project $project)
{
    // Head role cannot delete projects (view-only access)
    $user = Auth::user();
    if ($user->hasRole('head') && !$user->hasAnyRole(['pm', 'hr'])) {
        return back()->with('error', 'Role Head tidak dapat menghapus proyek. Hanya dapat melihat informasi proyek.');
    }
    
    // Check if the authenticated user is the project owner
    if ($project->owner_id !== auth()->id()) {
        abort(403, 'Anda tidak memiliki izin untuk menghapus proyek ini.');
    }
    
    // ... rest of code
}
```

### 2. TicketController

**Location**: `app/Http/Controllers/TicketController.php`

#### createGeneral() method
```php
public function createGeneral()
{
    // Head role cannot create general tickets (view-only access)
    $user = Auth::user();
    if ($user->hasRole('head') && !$user->hasAnyRole(['pm'])) {
        abort(403, 'Role Head tidak dapat membuat tiket umum. Hanya PM yang dapat membuat tiket untuk semua anggota.');
    }
    
    return view('tickets.create_general');
}
```

#### storeGeneral() method
```php
public function storeGeneral(Request $request)
{
    // Head role cannot create general tickets (view-only access)
    $user = Auth::user();
    if ($user->hasRole('head') && !$user->hasAnyRole(['pm'])) {
        return back()->withErrors(['error' => 'Role Head tidak dapat membuat tiket umum. Hanya PM yang dapat membuat tiket untuk semua anggota.'])->withInput();
    }
    
    // ... rest of code
}
```

**Note**: HEAD can still claim and update tickets that are assigned to roles (this is allowed per business requirements).

### 3. BusinessController

**Location**: `app/Http/Controllers/BusinessController.php`

#### create() method
```php
public function create()
{
    // Head role cannot create business proposals (view-only access)
    $user = Auth::user();
    if ($user->hasRole('head') && !$user->hasAnyRole(['pm', 'kewirausahaan'])) {
        abort(403, 'Role Head tidak dapat membuat proposal usaha. Hanya dapat melihat informasi usaha.');
    }
    
    // Redirect to index with modal open flag
    return redirect()->route('businesses.index')->with('openCreateModal', true);
}
```

#### store() method
```php
public function store(Request $request)
{
    // Head role cannot create business proposals (view-only access)
    $user = Auth::user();
    if ($user->hasRole('head') && !$user->hasAnyRole(['pm', 'kewirausahaan'])) {
        return back()->withErrors(['error' => 'Role Head tidak dapat membuat proposal usaha. Hanya dapat melihat informasi usaha.'])->withInput();
    }
    
    // ... rest of code
}
```

#### approve() method
```php
public function approve(Business $business)
{
    // Head role cannot approve business proposals (view-only access)
    $user = Auth::user();
    if ($user->hasRole('head') && !$user->hasAnyRole(['pm'])) {
        return back()->with('error', 'Role Head tidak dapat menyetujui proposal usaha. Hanya PM yang dapat menyetujui.');
    }
    
    $this->authorize('approve', $business);
    
    // ... rest of code
}
```

## Authorization Pattern

Semua authorization checks mengikuti pattern yang konsisten:

```php
// Pattern untuk abort (GET requests)
$user = Auth::user();
if ($user->hasRole('head') && !$user->hasAnyRole(['pm', 'hr'])) {
    abort(403, 'Pesan error yang jelas');
}

// Pattern untuk redirect (POST/PUT/DELETE requests)
$user = Auth::user();
if ($user->hasRole('head') && !$user->hasAnyRole(['pm', 'hr'])) {
    return back()->withErrors(['error' => 'Pesan error yang jelas'])->withInput();
}
```

**Rationale**:
- Check `hasRole('head')` first untuk identify HEAD users
- Check `!hasAnyRole(['pm', 'hr'])` untuk allow multi-role users
- Jika user adalah `head` + `pm`, maka full access tetap diberikan
- Jika user adalah `head` only, maka blocked dengan pesan yang jelas

## Multi-Role Support

Authorization checks tetap support multi-role system:

| User Roles | Projects Access | Tickets Access | Business Access |
|------------|----------------|----------------|-----------------|
| `head` only | ❌ View only | ❌ View + Claim only | ❌ View only |
| `head` + `pm` | ✅ Full access | ✅ Full access | ✅ Full access |
| `head` + `hr` | ✅ Can create projects | ❌ View + Claim only | ❌ View only |
| `pm` only | ✅ Full access | ✅ Full access | ✅ Full access |

## Error Messages

Error messages dirancang untuk user-friendly dan memberikan guidance:

| Action | Error Message |
|--------|---------------|
| Create Project | "Role Head tidak dapat membuat proyek baru. Silakan hubungi PM untuk membuat proyek." |
| Edit Project | "Role Head tidak dapat mengedit proyek. Hanya dapat melihat informasi proyek." |
| Delete Project | "Role Head tidak dapat menghapus proyek. Hanya dapat melihat informasi proyek." |
| Create General Ticket | "Role Head tidak dapat membuat tiket umum. Hanya PM yang dapat membuat tiket untuk semua anggota." |
| Create Business | "Role Head tidak dapat membuat proposal usaha. Hanya dapat melihat informasi usaha." |
| Approve Business | "Role Head tidak dapat menyetujui proposal usaha. Hanya PM yang dapat menyetujui." |

## Testing

### Manual Test Checklist

Login sebagai user dengan role `head` only (username: `yahya`):

#### Projects
- [ ] ✅ Bisa akses `/projects` (index)
- [ ] ✅ Bisa klik detail proyek
- [ ] ✅ Bisa lihat chat proyek
- [ ] ❌ **Cannot** akses `/projects/create` → 403 error
- [ ] ❌ **Cannot** akses `/projects/{id}/edit` → 403 error
- [ ] ❌ **Cannot** delete proyek → Error message

#### Tickets
- [ ] ✅ Bisa akses `/tickets` (index)
- [ ] ✅ Bisa claim tiket
- [ ] ✅ Bisa update status tiket yang di-claim
- [ ] ❌ **Cannot** akses `/tickets/general/create` → 403 error

#### Businesses
- [ ] ✅ Bisa akses `/businesses` (index)
- [ ] ✅ Bisa klik detail business
- [ ] ❌ **Cannot** create business baru → 403 error
- [ ] ❌ **Cannot** approve/reject business → Error message

### Automated Test (Optional)

```php
// tests/Feature/HeadRoleAuthorizationTest.php
public function test_head_cannot_create_project()
{
    $head = User::factory()->create();
    $head->assignRole('head');
    
    $response = $this->actingAs($head)->get(route('projects.create'));
    
    $response->assertStatus(403);
    $response->assertSee('Role Head tidak dapat membuat proyek baru');
}

public function test_head_with_pm_can_create_project()
{
    $user = User::factory()->create();
    $user->assignRole(['head', 'pm']);
    
    $response = $this->actingAs($user)->get(route('projects.create'));
    
    $response->assertStatus(200);
}
```

## View-Level Protection (Optional Enhancement)

Untuk pengalaman user yang lebih baik, hide action buttons di views:

```blade
{{-- projects/show.blade.php --}}
@if(!auth()->user()->hasRole('head') || auth()->user()->hasAnyRole(['pm', 'hr']))
    <a href="{{ route('projects.edit', $project) }}" class="btn btn-primary">
        Edit Proyek
    </a>
@endif

{{-- businesses/index.blade.php --}}
@if(!auth()->user()->hasRole('head') || auth()->user()->hasAnyRole(['pm', 'kewirausahaan']))
    <button onclick="openCreateModal()" class="btn btn-success">
        Buat Proposal Usaha
    </button>
@endif
```

## Related Files

- ✅ `database/seeders/RolePermissionSeeder.php` - Permission definitions
- ✅ `app/Http/Controllers/ProjectController.php` - Project authorization
- ✅ `app/Http/Controllers/TicketController.php` - Ticket authorization
- ✅ `app/Http/Controllers/BusinessController.php` - Business authorization
- 📝 `resources/views/projects/*.blade.php` - View-level button hiding (optional)
- 📝 `resources/views/businesses/*.blade.php` - View-level button hiding (optional)

## Summary

✅ **HEAD role sekarang fully protected dengan:**
1. Permission-based access control di database
2. Controller-level authorization checks di semua CRUD operations
3. Clear, user-friendly error messages
4. Multi-role support (HEAD+PM tetap full access)
5. Consistent authorization pattern across controllers

**Next Steps**:
- [ ] Add view-level button hiding untuk UX improvement
- [ ] Add automated tests untuk authorization
- [ ] Consider custom middleware untuk HEAD role checks
