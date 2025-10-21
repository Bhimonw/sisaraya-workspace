# Role Head of SISARAYA (Yahya) - Highest Oversight

**Tanggal**: 21 Oktober 2025  
**Branch**: Notifikasi  
**Status**: ✅ Implemented  
**Role Name**: `head`

## 🎯 Tujuan & Karakteristik

Role **Head** adalah role khusus untuk Yahya sebagai Head/Kepala SISARAYA dengan karakteristik:

1. **Auto-Viewer** - Otomatis terhubung ke semua proyek tanpa perlu ditambahkan sebagai member
2. **Read-Only Projects** - Dapat melihat semua proyek tetapi TIDAK bisa update/delete
3. **Active Participation** - Dapat aktif di fitur chat proyek
4. **Task Execution** - Dapat mengambil (claim) dan mengerjakan tiket
5. **Oversight Role** - Fungsi utama adalah mengawasi berjalannya proyek

---

## 🔑 Permissions & Access

### ✅ Yang BISA Dilakukan

| Fitur | Access Level | Keterangan |
|-------|--------------|------------|
| **View Projects** | ✅ Full | Melihat SEMUA proyek (planning, active, blackout, completed) |
| **View Project Details** | ✅ Full | Melihat detail proyek, timeline, members, events |
| **View Tickets** | ✅ Full | Melihat semua tiket di semua proyek |
| **Claim Tickets** | ✅ Yes | Mengambil tiket yang tersedia |
| **Update Ticket Status** | ✅ Yes | Mengubah status tiket yang diklaim (todo → doing → done) |
| **Project Chat** | ✅ Active | Berpartisipasi aktif di chat semua proyek |
| **View Documents** | ✅ Full | Melihat dokumen di semua proyek |

### ❌ Yang TIDAK Bisa Dilakukan

| Fitur | Restriction | Alasan |
|-------|-------------|--------|
| **Create Project** | ❌ No | Bukan coordinator |
| **Update Project** | ❌ No | Read-only access |
| **Delete Project** | ❌ No | Read-only access |
| **Manage Members** | ❌ No | Bukan PM/HR |
| **Create Tickets** | ❌ No | Hanya execute, tidak create |
| **Update Project Settings** | ❌ No | Oversight only |

---

## 📊 Implementasi Technical

### 1. Role & Permissions (`RolePermissionSeeder.php`)

```php
$roles = [
    'member', 'hr', 'pm', 'sekretaris', 'media', 'pr', 
    'talent_manager', 'researcher', 'talent', 'guest', 
    'bendahara', 'kewirausahaan', 'ketua'  // ← New role (Ketua SISARAYA)
];

// Permissions untuk ketua (Yahya sebagai Ketua SISARAYA)
Role::where('name', 'ketua')->first()?->givePermissionTo([
    'projects.view',         // View all projects
    'tickets.view_all',      // View all tickets
    'tickets.update_status', // Update claimed tickets
    'documents.view_all'     // View all documents
]);
```

**Tidak diberikan**:
- ❌ `projects.create`
- ❌ `projects.update`
- ❌ `projects.manage_members`
- ❌ `tickets.create`

---

### 2. Project Policy (`ProjectPolicy.php`)

```php
public function view(User $user, Project $project)
{
    return $project->members->contains($user) 
        || $project->owner_id === $user->id 
        || $user->hasRole('HR') 
        || $user->hasRole('ketua'); // ← Ketua auto-viewer
}

public function update(User $user, Project $project)
{
    // Ketua TIDAK bisa update - hanya oversight
    return $project->owner_id === $user->id 
        || $user->hasRole('HR') 
        || $user->hasRole('PM');
}

public function manageMembers(User $user, Project $project)
{
    // Ketua TIDAK bisa manage members - hanya oversight
    return $project->owner_id === $user->id 
        || $user->hasRole('HR') 
        || $user->hasRole('PM');
}
```

**Logic**:
- ✅ `view()` - Ketua added → bisa lihat semua proyek
- ❌ `update()` - Ketua NOT added → tidak bisa edit
- ❌ `manageMembers()` - Ketua NOT added → tidak bisa manage

---

### 3. Project Controller (`ProjectController.php`)

#### Method `mine()` - Proyekku
```php
public function mine(Request $request)
{
    $user = Auth::user();
    
    // Ketua dapat melihat SEMUA proyek aktif
    if ($user->hasRole('ketua')) {
        $myProjects = Project::whereIn('status', ['planning', 'active'])
            ->latest()
            ->get();
    } else {
        // Regular users: only their projects
        $myProjects = Project::where(function($q) use ($user) {
            $q->where('owner_id', $user->id)
              ->orWhereHas('members', ...);
        })->whereIn('status', ['planning', 'active'])->get();
    }
}
```

#### Method `workspace()` - Meja Kerja
```php
public function workspace()
{
    $user = Auth::user();
    
    // Ketua dapat melihat SEMUA proyek (blackout & active)
    if ($user->hasRole('ketua')) {
        $blackoutProjects = Project::where('status', 'blackout')->get();
        $projects = Project::where('status', 'active')->get();
    } else {
        // Regular users: only their projects
        ...
    }
}
```

#### Method `allMine()` - Semua Projectku
```php
public function allMine()
{
    $user = Auth::user();
    
    // Ketua dapat melihat SEMUA proyek (all status)
    if ($user->hasRole('ketua')) {
        $projects = Project::latest()->get();
    } else {
        // Regular users: only their projects
        ...
    }
}
```

**Pattern**: Auto-membership via conditional query, bukan pivot table

---

### 4. Ticket Model (`Ticket.php`)

```php
public static function getAvailableRoles(): array
{
    return [
        'pm' => 'Project Manager',
        'hr' => 'Human Resources',
        // ... other roles
        'ketua' => 'Ketua SISARAYA', // ← Added
    ];
}
```

**Impact**: Ketua bisa ditargetkan di tiket dan muncul di dropdown role

---

### 5. Chat Controller (`ProjectChatController.php`)

```php
private function isMember(Project $project, $user): bool
{
    return $project->owner_id === $user->id 
        || $project->members()->where('user_id', $user->id)->exists()
        || $user->hasRole('ketua'); // ← Auto-participant in all chats
}
```

**Logic**: Ketua otomatis bisa chat di semua proyek tanpa explicit membership

---

## 🎭 Use Cases

### Use Case 1: Monitoring Proyek
```
Head Yahya login
→ Dashboard menampilkan semua proyek aktif
→ Klik "Proyekku" → melihat semua 15 proyek yang sedang berjalan
→ Klik detail proyek → melihat tiket, timeline, members
→ TIDAK bisa edit nama proyek atau settings
```

### Use Case 2: Berpartisipasi di Chat
```
Yahya (Ketua) melihat Project X
→ Buka tab Chat
→ Baca percakapan tim
→ Kirim pesan: "Bagaimana progress desain logo?"
→ Tim respond di chat
→ Yahya memberi feedback
```

### Use Case 3: Mengambil Tiket
```
Yahya melihat tiket di "Tiketku"
→ Ada tiket "Review Proposal Event" dengan target role "ketua"
→ Klik "Ambil Tiket"
→ Tiket masuk ke "My Tickets" (status: todo)
→ Klik "Mulai" → status: doing
→ Selesai review → Klik "Selesai" → status: done
```

### Use Case 4: Oversight Tanpa Intervensi
```
Yahya melihat Project Y status "blackout"
→ Buka detail proyek → lihat alasan blackout
→ Masuk chat → tanya kendala ke PM
→ Beri saran tanpa mengubah settings proyek
→ Tim execute saran
→ PM update status proyek (Yahya tidak bisa update)
```

---

## 📋 Comparison Matrix

| Feature | Member | PM | HR | **Ketua (Yahya)** |
|---------|--------|----|----|-------------------|
| View Own Projects | ✅ | ✅ | ✅ | ✅ |
| View All Projects | ❌ | ✅ | ✅ | **✅** |
| Create Project | ❌ | ✅ | ✅ | **❌** |
| Update Project | Owner only | ✅ | ✅ | **❌** |
| Delete Project | Owner only | ✅ | ✅ | **❌** |
| Manage Members | Owner only | ✅ | ✅ | **❌** |
| View Tickets | Project only | ✅ | ✅ | **✅** |
| Claim Tickets | ✅ | ✅ | ✅ | **✅** |
| Create Tickets | Project only | ✅ | ❌ | **❌** |
| Project Chat | Project only | Project only | Project only | **✅ All** |
| View Documents | Project only | ✅ | ✅ | **✅** |

**Key Differences**:
- **PM/HR**: Can create/update projects, manage members
- **Ketua (Yahya)**: View-only for projects, active participant in chat & tickets, highest oversight
- **Member**: Limited to projects they're in

---

## 🔒 Security & Privacy

### Access Control Hierarchy

```
Level 1: Ketua SISARAYA (Highest Oversight)  ← NEW
├── Level 2: Owner (Full Project Control)
├── Level 3: Admin (Manage Content)
├── Level 4: PM/HR (Global Management)
└── Level 5: Member (Project-specific)
```

**Note**: Ketua memiliki visibility tertinggi tapi akses modifikasi terbatas (oversight, bukan management)

### Data Filtering

**Ketua (Yahya) sees**:
- ✅ All projects (all status)
- ✅ All project details (timeline, members, events)
- ✅ All tickets
- ✅ All documents
- ✅ All chat messages

**Ketua CANNOT modify**:
- ❌ Project settings
- ❌ Project members
- ❌ Project status
- ❌ Other people's tickets (only their claimed tickets)

---

## 🧪 Testing Checklist

### Test Case 1: Auto-View Projects
```php
$yahya = User::factory()->create();
$yahya->assignRole('ketua');

$project = Project::factory()->create(['owner_id' => User::factory()]);

// Expected
$yahya->can('view', $project); // ✅ True
$yahya->can('update', $project); // ❌ False

// In controller
$response = $this->actingAs($yahya)->get('/projects/mine');
$response->assertSee($project->name); // ✅ Should see all projects
```

### Test Case 2: Chat Access
```php
$yahya = User::factory()->create()->assignRole('ketua');
$project = Project::factory()->create();

// Expected
$response = $this->actingAs($yahya)
    ->post("/api/projects/{$project->id}/chat/messages", [
        'message' => 'Hello team'
    ]);

$response->assertStatus(200); // ✅ Can send message
```

### Test Case 3: Ticket Claim
```php
$yahya = User::factory()->create()->assignRole('ketua');
$ticket = Ticket::factory()->create([
    'target_role' => 'ketua',
    'status' => 'todo'
]);

// Expected
$response = $this->actingAs($yahya)
    ->post("/tickets/{$ticket->id}/claim");

$response->assertStatus(302); // ✅ Can claim
$ticket->refresh();
$this->assertEquals($yahya->id, $ticket->claimed_by); // ✅ Claimed
```

### Test Case 4: Cannot Update Project
```php
$yahya = User::factory()->create()->assignRole('ketua');
$project = Project::factory()->create();

// Expected
$response = $this->actingAs($yahya)
    ->put("/projects/{$project->id}", [
        'name' => 'Updated Name'
    ]);

$response->assertStatus(403); // ❌ Forbidden
```

---

## 🚀 Migration & Seeding

### Seed the Role
```bash
php artisan db:seed --class=RolePermissionSeeder
```

### Assign to User
```bash
# Via Tinker
php artisan tinker
>>> $user = User::where('username', 'yahya')->first();
>>> $user->assignRole('ketua');
```

### Verify
```bash
>>> $user->hasRole('ketua'); // true
>>> $user->can('projects.view'); // true
>>> $user->can('projects.update'); // false
```

---

## 📝 File Changes Summary

| File | Changes | Purpose |
|------|---------|---------|
| `database/seeders/RolePermissionSeeder.php` | Added `ketua` role & permissions | Role definition |
| `app/Policies/ProjectPolicy.php` | Added `ketua` to `view()` | Auto-view access |
| `app/Http/Controllers/ProjectController.php` | Conditional query for `ketua` | Auto-membership |
| `app/Http/Controllers/ProjectChatController.php` | Added `ketua` to `isMember()` | Chat access |
| `app/Models/Ticket.php` | Added to `getAvailableRoles()` | Ticket targeting |

---

## 🎓 Design Decisions

### Why NOT Use Pivot Table?
**Decision**: Ketua uses conditional queries, not `project_user` pivot

**Reasons**:
1. **Scalability** - Tidak perlu insert ribuan rows untuk setiap proyek
2. **Maintainability** - Tidak perlu sync saat project baru dibuat
3. **Performance** - Single role check vs N queries
4. **Clarity** - Explicit role-based logic, tidak hidden di pivot
5. **Position-based** - Role ketua adalah posisi organisasi, bukan member biasa

### Why Read-Only Projects?
**Decision**: Ketua can view but cannot update/delete projects

**Reasons**:
1. **Oversight Role** - Yahya sebagai Ketua fokus ke monitoring, bukan managing operasional
2. **Separation of Concerns** - PM/HR manage, Ketua oversees & memberi arahan strategis
3. **Audit Trail** - Changes tetap traceable ke PM/HR yang eksekusi
4. **Safety** - Prevent accidental modifications during oversight
5. **Delegation** - Empower PM/HR untuk execute, Ketua provide guidance

### Why Active in Chat?
**Decision**: Ketua can participate in all project chats

**Reasons**:
1. **Communication** - Yahya bisa memberi feedback langsung ke tim
2. **Collaboration** - Tim bisa tanya pendapat/arahan dari Ketua
3. **Engagement** - Tidak hanya passive viewer, tapi active participant
4. **Efficiency** - Real-time discussion, tidak perlu meeting formal
5. **Leadership** - Presence dalam komunikasi tim menunjukkan keterlibatan

---

## 🔗 Related Documentation

- `docs/PROJECT_PRIVACY_SYSTEM.md` - Project privacy overview
- `docs/DOUBLE_ROLE_IMPLEMENTATION.md` - Multi-role system
- `docs/PROJECT_CHAT_FEATURE.md` - Chat system
- `database/seeders/RolePermissionSeeder.php` - Role definitions

---

## ✅ Implementation Checklist

- [x] Tambah role `ketua` di seeder
- [x] Define permissions untuk `ketua`
- [x] Update `ProjectPolicy::view()` untuk auto-view
- [x] Update `ProjectController` methods untuk auto-membership
- [x] Update `ProjectChatController` untuk chat access
- [x] Update `Ticket` model available roles
- [x] Buat dokumentasi lengkap
- [x] Run seeder di database
- [ ] Testing manual dengan user ketua (Yahya)
- [ ] Assign role ke user Yahya: `$user->assignRole('ketua')`
- [ ] Verify akses di production

---

**Status**: ✅ **Implementation Complete - Ready for Seeding & Testing**
