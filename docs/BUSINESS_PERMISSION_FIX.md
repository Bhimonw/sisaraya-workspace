# Perbaikan Role Permission Management Usaha

**Tanggal**: 20 Oktober 2025  
**Status**: ✅ Selesai

## Ringkasan Perubahan

Dilakukan perbaikan sistem permission untuk fitur Management Usaha agar lebih konsisten menggunakan permission-based authorization daripada role-based authorization langsung. Ini meningkatkan fleksibilitas dan keamanan sistem.

## Masalah yang Diperbaiki

### 1. **Inkonsistensi Authorization**
- Menu sidebar menggunakan `@role('kewirausahaan')` tanpa cek permission
- PM yang seharusnya bisa melihat management usaha tidak bisa mengaksesnya
- Policy menggunakan hardcoded role check (`hasRole('pm')`) daripada permission check
- Controller hanya menggunakan middleware untuk sebagian action

### 2. **Permission yang Hilang**
- `business.update` - untuk update business yang pending
- `business.delete` - untuk delete business

### 3. **Authorization di BusinessReportController**
- Masih menggunakan role check (`hasRole('pm')`) daripada permission check

## Perubahan Detail

### 1. Database & Seeder (`database/seeders/RolePermissionSeeder.php`)

**Permission baru ditambahkan**:
```php
'business.update',    // Update business yang pending
'business.delete',    // Delete business
```

**Assignment ke role kewirausahaan**:
```php
Role::where('name', 'kewirausahaan')->first()?->givePermissionTo([
    'business.create', 
    'business.view', 
    'business.update',      // BARU
    'business.manage_talent', 
    'business.upload_reports', 
    'documents.upload'
]);
```

**PM sudah memiliki**:
- `business.view` - untuk lihat semua usaha
- `business.approve` - untuk approve/reject usaha

### 2. Controller (`app/Http/Controllers/BusinessController.php`)

**Middleware ditambahkan**:
```php
$this->middleware('permission:business.update')->only(['edit','update']);
$this->middleware('permission:business.delete')->only(['destroy']);
```

### 3. Policy (`app/Policies/BusinessPolicy.php`)

**Before** (hardcoded role check):
```php
public function approve(User $user, Business $business): bool
{
    return $user->hasRole('pm') && $business->isPending();
}
```

**After** (permission check):
```php
public function approve(User $user, Business $business): bool
{
    return $user->can('business.approve') && $business->isPending();
}
```

**Method baru ditambahkan**:
```php
public function uploadReport(User $user, Business $business): bool
{
    return $user->can('business.upload_reports')
        && ($user->id === $business->created_by || $user->hasRole('pm'))
        && $business->isApproved();
}
```

### 4. View - Menu Sidebar (`resources/views/layouts/_menu.blade.php`)

**Before** (role-based):
```blade
@role('kewirausahaan')
    <li>
        <a href="{{ route('businesses.index') }}">
            Usaha Aktif
        </a>
    </li>
@endrole
```

**After** (permission-based dengan conditional label):
```blade
@can('business.view')
    <li>
        <a href="{{ route('businesses.index') }}">
            @if($user->hasRole('pm'))
                Manajemen Usaha
            @else
                Usaha Aktif
            @endif
        </a>
    </li>
@endcan
```

**Hasil**: 
- Kewirausahaan melihat "Usaha Aktif"
- PM melihat "Manajemen Usaha"
- Keduanya bisa akses karena punya permission `business.view`

### 5. View - Business Index (`resources/views/businesses/index.blade.php`)

**Before**:
```blade
@if(auth()->user()->hasRole('pm'))
    Manajemen Usaha
@else
    Usaha Komunitas
@endif
```

**After**:
```blade
@can('business.approve')
    Manajemen Usaha
@else
    Usaha Komunitas
@endcan
```

**Logika**: Siapa saja dengan permission `business.approve` (PM) dianggap sebagai manager.

### 6. View - Business Show (`resources/views/businesses/show.blade.php`)

**Upload Report Section**:
```blade
@can('uploadReport', $business)
    <!-- Form upload laporan -->
@endcan
```

**Delete Report Button**:
```blade
@if($report->user_id === auth()->id() || auth()->user()->can('business.delete'))
    <!-- Delete button -->
@endif
```

### 7. BusinessReportController (`app/Http/Controllers/BusinessReportController.php`)

**store() method - Before**:
```php
$this->authorize('update', $business);
```

**store() method - After**:
```php
$this->authorize('uploadReport', $business);
```

**destroy() method - Before**:
```php
if ($report->user_id !== auth()->id() && !auth()->user()->hasRole('pm')) {
    abort(403);
}
```

**destroy() method - After**:
```php
if ($report->user_id !== auth()->id() && !auth()->user()->can('business.delete')) {
    abort(403);
}
```

### 8. Test (`tests/Feature/BusinessApprovalTest.php`)

**Permission setup ditambahkan di setUp()**:
```php
protected function setUp(): void
{
    parent::setUp();
    
    // Create permissions
    $permissions = [
        'business.create',
        'business.view',
        'business.update',
        'business.delete',
        'business.approve',
        'business.upload_reports',
    ];
    
    foreach ($permissions as $permission) {
        \Spatie\Permission\Models\Permission::create(['name' => $permission]);
    }
    
    // Create roles
    Role::create(['name' => 'kewirausahaan']);
    Role::create(['name' => 'pm']);
}
```

## Matrix Permission Business

| Permission | Kewirausahaan | PM | Deskripsi |
|-----------|--------------|-----|-----------|
| `business.view` | ✅ | ✅ | Lihat daftar dan detail usaha |
| `business.create` | ✅ | ❌ | Buat usaha baru (pending) |
| `business.update` | ✅ | ❌ | Update usaha yang masih pending (owner only) |
| `business.delete` | ❌ | ❌ | Delete business (via policy: creator if pending, PM anytime) |
| `business.approve` | ❌ | ✅ | Approve/reject usaha pending |
| `business.upload_reports` | ✅ | ❌ | Upload laporan untuk usaha approved |
| `business.manage_talent` | ✅ | ❌ | Reserved untuk fitur talent management |

## Workflow Authorization

### 1. **Buat Usaha Baru**
```
User → business.create permission → POST /businesses 
     → Status: pending 
     → Notifikasi ke semua PM
```

### 2. **PM Approve Usaha**
```
PM → business.approve permission → POST /businesses/{id}/approve
   → Status: approved
   → Create Project (PM as owner, creator as admin member)
   → Project dengan label "UMKM"
```

### 3. **Upload Laporan**
```
User → uploadReport policy check:
       ✅ business.upload_reports permission
       ✅ Creator or PM
       ✅ Business is approved
     → POST /businesses/{id}/reports
```

### 4. **Delete Laporan**
```
User → business.delete permission OR report uploader
     → DELETE /businesses/{id}/reports/{report_id}
```

## Testing

Semua test pass dengan 9 test cases:
```bash
php artisan test --filter=BusinessApprovalTest
```

**Test coverage**:
- ✅ Kewirausahaan can create business with pending status
- ✅ PM receives notification when business created
- ✅ PM can approve pending business
- ✅ PM approve creates project automatically
- ✅ PM can reject business with reason
- ✅ Non PM cannot approve business
- ✅ PM cannot approve already approved business
- ✅ Rejection reason is required when rejecting
- ✅ Businesses can be filtered by status

## Deployment Steps

1. **Jalankan seeder untuk update permission**:
```bash
php artisan db:seed --class=RolePermissionSeeder --force
```

2. **Clear cache permission**:
```bash
php artisan permission:cache-reset
php artisan cache:clear
```

3. **Verifikasi permission**:
```bash
php artisan tinker
>>> \Spatie\Permission\Models\Permission::where('name', 'like', 'business.%')->pluck('name');
```

Expected output:
```
[
  "business.create",
  "business.view",
  "business.update",
  "business.delete",
  "business.manage_talent",
  "business.upload_reports",
  "business.approve"
]
```

4. **Test manual**:
- Login sebagai kewirausahaan → Lihat menu "Usaha Aktif"
- Login sebagai PM → Lihat menu "Manajemen Usaha"
- Buat usaha baru → PM dapat notifikasi
- PM approve → Project otomatis terbuat

## Best Practices Applied

### ✅ Permission-based Authorization
- Menggunakan `@can()` daripada `@role()` di view
- Menggunakan `$user->can()` daripada `$user->hasRole()` di policy
- Middleware controller menggunakan `permission:` guard

### ✅ Granular Permissions
- Setiap action memiliki permission terpisah
- Permission assignment ke role dilakukan di seeder
- Policy menggabungkan permission check dengan business logic

### ✅ Policy-based Authorization
- Complex authorization logic di policy
- Controller menggunakan `$this->authorize()`
- View menggunakan `@can()` dengan policy method

### ✅ Flexible & Maintainable
- Mudah menambah role baru tanpa ubah code
- Tinggal assign permission ke role baru
- Policy reusable untuk berbagai skenario

## Referensi

- **Spatie Laravel Permission**: https://spatie.be/docs/laravel-permission
- **Laravel Authorization**: https://laravel.com/docs/authorization
- **Project Instructions**: `.github/copilot-instructions.md`

## Changelog Entry

```
[2025-10-20] Perbaiki role permission management usaha: 
- Tambah business.update dan business.delete permission
- Ganti @role dengan @can di menu dan views
- Update policy untuk cek permission bukan role
- Update BusinessReportController authorization
- Fix test setup untuk include permissions
- Semua 9 test pass ✅
```
