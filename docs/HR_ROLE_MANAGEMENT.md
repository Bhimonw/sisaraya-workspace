# HR Permission & Role Management

## 📋 Overview

Sistem permission untuk HR telah diubah agar HR **hanya dapat mengelola role user**, tidak dapat mengedit detail akun atau menghapus user.

## 🔐 Permission HR

### ✅ Yang BISA Dilakukan HR:
- ✅ Melihat daftar semua user
- ✅ Melihat detail user (nama, username, email, roles)
- ✅ **Mengelola role user** (assign/remove roles)
- ✅ Memberikan multiple roles ke satu user

### ❌ Yang TIDAK BISA Dilakukan HR:
- ❌ Edit data user (nama, username, email, password)
- ❌ Hapus user/akun
- ❌ Buat user baru (ini harus dilakukan oleh admin/sistem)

## 🎯 Alasan Pembatasan

1. **Separation of Concerns** - HR fokus pada pengelolaan role dan permissions
2. **Data Integrity** - Mencegah perubahan data sensitif user
3. **Audit Trail** - Role management terpisah dari user data management
4. **Security** - Mengurangi risiko keamanan dengan membatasi akses

## 📁 File yang Dimodifikasi

### 1. Routes (`routes/web.php`)
```php
Route::prefix('admin')->name('admin.')->group(function () {
    // Manage user roles (HR only manages roles, not user data)
    Route::get('users/{user}/manage-roles', [AdminUserController::class, 'manageRoles'])
        ->name('users.manage-roles');
    Route::put('users/{user}/update-roles', [AdminUserController::class, 'updateRoles'])
        ->name('users.update-roles');
    
    Route::resource('users', AdminUserController::class);
});
```

### 2. Controller (`app/Http/Controllers/Admin/UserController.php`)

**Methods Baru:**
- `manageRoles(User $user)` - Tampilkan form manage roles
- `updateRoles(Request $request, User $user)` - Update user roles

### 3. Views

**New File:** `resources/views/admin/users/manage-roles.blade.php`
- Form untuk assign/remove roles
- Checkbox untuk setiap role
- Deskripsi untuk setiap role
- Info box dengan penjelasan

**Modified Files:**
- `resources/views/components/users/user-table.blade.php`
  - Ganti tombol "Edit" dan "Hapus" dengan "Kelola Role"
- `resources/views/components/users/user-card.blade.php`
  - Ganti tombol "Edit" dan "Hapus" dengan "Kelola Role"

## 🎨 UI/UX Changes

### Before:
```
[Edit] [Hapus]
```

### After:
```
[Kelola Role]
```

## 📝 Cara Menggunakan

### Sebagai HR:

1. **Buka Halaman Manajemen Anggota**
   - Navigate ke: `Ruang Management > Manajemen Anggota`

2. **Pilih User yang Ingin Dikelola Role-nya**
   - Klik tombol **"Kelola Role"** pada user yang dipilih

3. **Assign/Remove Roles**
   - Centang role yang ingin diberikan
   - Hapus centang untuk remove role
   - User dapat memiliki multiple roles sekaligus

4. **Simpan Perubahan**
   - Klik **"Simpan Perubahan Role"**
   - Role akan langsung aktif

## 🔧 Technical Details

### Role Sync
Menggunakan Spatie Permission `syncRoles()` method:
```php
$user->syncRoles($roles); // Sync roles (remove old, add new)
```

### Validation
```php
$data = $request->validate([
    'roles' => 'nullable|array',
    'roles.*' => 'exists:roles,name',
]);
```

### Available Roles
- `pm` - Project Manager
- `hr` - Human Resources
- `bendahara` - Finance/Treasurer
- `sekretaris` - Secretary
- `kewirausahaan` - Entrepreneurship
- `member` - Regular Member
- (dan role lainnya sesuai seeder)

## 🎯 Benefits

1. **Clear Responsibility** - HR fokus pada role management
2. **Better Security** - Reduced attack surface
3. **Audit Friendly** - Role changes easy to track
4. **User Safety** - Prevents accidental user data deletion
5. **Scalability** - Easy to add more granular permissions

## 🔮 Future Enhancements

Possible improvements:
- [ ] Role approval workflow (request → approve)
- [ ] Role change history/audit log
- [ ] Bulk role assignment
- [ ] Role templates/presets
- [ ] Permission preview (what can user do with these roles)

## 📊 Permission Matrix

| Action | HR | Admin | PM |
|--------|----|----|-----|
| View Users | ✅ | ✅ | ❌ |
| Manage Roles | ✅ | ✅ | ❌ |
| Edit User Data | ❌ | ✅ | ❌ |
| Delete User | ❌ | ✅ | ❌ |
| Create User | ❌ | ✅ | ❌ |

---

**Created:** October 17, 2025  
**Author:** Copilot Agent  
**Status:** ✅ Implemented & Documented
