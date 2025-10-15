# Double Role Implementation

## Overview
Sistem sekarang sudah fully support **multiple roles per user** menggunakan Spatie Laravel Permission. Contoh: **Bhimo** memiliki 2 role sekaligus: `PM` dan `Sekretaris`.

## Implementation Details

### 1. Database Structure
- Menggunakan Spatie Permission tables: `model_has_roles` (many-to-many relationship)
- User dapat memiliki **unlimited roles**
- Setiap role memiliki permissions yang berbeda

### 2. Seeder Configuration
File: `database/seeders/SisarayaMembersSeeder.php`

```php
[
    'name' => 'Bhimo',
    'username' => 'bhimo',
    'roles' => ['pm', 'sekretaris'], // ✅ Multiple roles
    'bio' => 'Project Manager & Sekretaris - ...'
]
```

Seeder akan loop dan assign semua roles:
```php
foreach ($memberData['roles'] as $roleName) {
    $user->assignRole($roleName);
}
```

### 3. Dashboard Display
File: `resources/views/dashboard.blade.php`

**Role Badges di Header:**
```blade
@foreach(auth()->user()->getRoleNames() as $role)
    <span class="px-3 py-1 text-xs font-semibold rounded-full 
        @if($role === 'pm') bg-purple-100 text-purple-700
        @elseif($role === 'sekretaris') bg-green-100 text-green-700
        ...">
        {{ strtoupper($role) }}
    </span>
@endforeach
```

**Welcome Message:**
```blade
@if(auth()->user()->getRoleNames()->count() > 1)
    Anda memiliki {{ auth()->user()->getRoleNames()->count() }} peran: 
    <span class="font-semibold">{{ auth()->user()->getRoleNames()->implode(', ') }}</span>
@endif
```

**Role-Specific Quick Actions:**
```blade
@role('pm')
    <!-- PM specific actions -->
@endrole

@role('sekretaris')
    <!-- Sekretaris specific actions -->
@endrole
```

Bhimo akan melihat **SEMUA quick actions** dari PM DAN Sekretaris.

### 4. Sidebar Menu
File: `resources/views/layouts/_menu.blade.php`

Menu items menggunakan `@role()` directive yang check secara **independen**:

```blade
@role('pm')
    <li>
        <a href="{{ route('projects.create') }}">
            🏗️ Manajemen Proyek
        </a>
    </li>
@endrole

@role('sekretaris')
    <li>
        <a href="{{ route('documents.index') }}">
            📂 Pengelolaan Arsip
        </a>
    </li>
@endrole
```

User dengan double role akan melihat **SEMUA menu items** yang match dengan roles mereka.

### 5. Permission Checks

#### Single Role Check
```blade
@role('pm')
    <!-- Content hanya untuk PM -->
@endrole
```

```php
if (auth()->user()->hasRole('pm')) {
    // Logic untuk PM
}
```

#### Multiple Role Check (OR condition)
```blade
@role('pm|sekretaris|hr')
    <!-- Content untuk PM OR Sekretaris OR HR -->
@endrole
```

```php
if (auth()->user()->hasAnyRole(['pm', 'sekretaris', 'hr'])) {
    // Logic untuk salah satu role
}
```

#### Multiple Role Check (AND condition)
```php
if (auth()->user()->hasAllRoles(['pm', 'sekretaris'])) {
    // Logic hanya untuk user yang punya KEDUA role
}
```

### 6. Dashboard Statistics

**Conditional Stats Cards:**
- Semua user: Tiket Saya, Events
- PM/Media/PR/Researcher: Proyek Aktif
- Bendahara: RAB Menunggu
- Sekretaris: Total Dokumen

Bhimo (PM + Sekretaris) akan melihat:
- ✅ Tiket Saya
- ✅ Proyek Aktif (karena PM)
- ✅ Total Dokumen (karena Sekretaris)
- ✅ Events

## Testing Multiple Roles

### Login sebagai Bhimo
```
Username: bhimo
Password: password
```

**Expected Behavior:**
1. Dashboard header menampilkan 2 badges: `PM` dan `SEKRETARIS`
2. Welcome message: "Anda memiliki 2 peran: pm, sekretaris"
3. Quick Actions menampilkan:
   - 🏗️ Buat Proyek Baru (PM)
   - 📂 Upload Dokumen (Sekretaris)
   - ➕ Buat Tiket Baru (Everyone)
   - 📅 Kalender Pribadi (Everyone)
4. Sidebar menu menampilkan:
   - Ruang Management dengan submenu PM & Sekretaris
   - 🏗️ Manajemen Proyek
   - 📂 Pengelolaan Arsip

### Adding More Roles
Untuk menambah role ke user yang sudah ada:

```php
$user = User::where('username', 'bhimo')->first();
$user->assignRole('hr'); // Sekarang Bhimo punya 3 roles
```

### Removing Roles
```php
$user->removeRole('sekretaris'); // Hapus 1 role
$user->syncRoles(['pm']); // Replace semua roles dengan array baru
```

## Color Coding for Roles

Dashboard menggunakan Tailwind color-coding untuk setiap role:

| Role | Background | Text |
|------|-----------|------|
| PM | `bg-purple-100` | `text-purple-700` |
| HR | `bg-blue-100` | `text-blue-700` |
| Sekretaris | `bg-green-100` | `text-green-700` |
| Bendahara | `bg-yellow-100` | `text-yellow-700` |
| Media | `bg-pink-100` | `text-pink-700` |
| PR | `bg-indigo-100` | `text-indigo-700` |
| Kewirausahaan | `bg-orange-100` | `text-orange-700` |
| Researcher | `bg-teal-100` | `text-teal-700` |
| Talent Manager | `bg-cyan-100` | `text-cyan-700` |
| Talent | `bg-lime-100` | `text-lime-700` |
| Guest | `bg-gray-100` | `text-gray-700` |

## Current Sisaraya Members with Roles

| Username | Name | Roles | Role Count |
|----------|------|-------|------------|
| bhimo | Bhimo | PM, Sekretaris | 2 ⭐ |
| bagas | Bagas | HR | 1 |
| dijah | Dijah | Bendahara | 1 |
| yahya | Yahya | PR | 1 |
| fadhil | Fadhil | PR | 1 |
| robby | Robby | Media | 1 |
| fauzan | Fauzan | Media | 1 |
| aulia | Aulia | Media | 1 |
| faris | Faris | Media | 1 |
| ardhi | Ardhi | Media | 1 |
| erge | Erge | Media | 1 |
| gades | Gades | Media | 1 |
| kafilah | Kafilah | Kewirausahaan | 1 |
| agung | Agung | Researcher | 1 |

## Future Enhancements

### Priority 1: Role Switching UI
Untuk user dengan multiple roles, tambahkan dropdown untuk "switch context":
```blade
<select id="active-role" class="text-sm border rounded">
    @foreach(auth()->user()->getRoleNames() as $role)
        <option value="{{ $role }}">View as {{ ucfirst($role) }}</option>
    @endforeach
</select>
```

Filter dashboard berdasarkan active role untuk fokus pada tasks tertentu.

### Priority 2: Role-specific Notifications
Notifikasi dapat di-target ke specific role:
```php
$users = User::role('bendahara')->get();
Notification::send($users, new RabNeedsApproval($rab));
```

### Priority 3: Activity Log per Role
Track actions dengan context role:
```php
activity()
    ->performedOn($project)
    ->causedBy(auth()->user())
    ->withProperties(['role' => 'pm'])
    ->log('Created new project');
```

## Troubleshooting

### Issue: User tidak melihat menu meskipun punya role
**Solution:** Clear cache
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

### Issue: Role badge tidak muncul di dashboard
**Solution:** Pastikan user sudah login dan check:
```php
dd(auth()->user()->getRoleNames());
```

### Issue: `@role()` directive tidak work
**Solution:** Pastikan Spatie Permission sudah di-publish:
```bash
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
php artisan migrate
```

## API Reference

### Spatie Permission Methods

```php
// Check single role
$user->hasRole('pm'); // bool

// Check any role (OR)
$user->hasAnyRole(['pm', 'sekretaris']); // bool

// Check all roles (AND)
$user->hasAllRoles(['pm', 'sekretaris']); // bool

// Get role names
$user->getRoleNames(); // Collection: ['pm', 'sekretaris']

// Get roles (objects)
$user->roles; // Collection of Role models

// Assign role
$user->assignRole('pm');
$user->assignRole(['pm', 'sekretaris']);

// Remove role
$user->removeRole('pm');

// Sync roles (replace all)
$user->syncRoles(['pm', 'hr']);
```

### Blade Directives

```blade
@role('pm')
    <!-- Content -->
@endrole

@role('pm|sekretaris')
    <!-- OR condition -->
@endrole

@hasrole('pm')
    <!-- Same as @role -->
@endhasrole

@hasanyrole('pm|sekretaris')
    <!-- OR condition -->
@endhasanyrole

@hasallroles('pm|sekretaris')
    <!-- AND condition -->
@endhasallroles
```

---

## Changelog

### v1.1.0 - 2025-10-13
- ✅ Implemented double role support (Bhimo: PM + Sekretaris)
- ✅ Updated dashboard with role badges and multi-role detection
- ✅ Added role-specific quick actions
- ✅ Enhanced sidebar menu for multiple roles
- ✅ Created comprehensive documentation
- ✅ Seeded 14 Sisaraya members with correct roles

**Test Status:** ✅ Ready for testing
**Login:** username: `bhimo`, password: `password`
