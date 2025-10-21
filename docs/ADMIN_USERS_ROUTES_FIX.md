# Admin Users Routes Fix

**Date**: October 21, 2025  
**Issue**: RouteNotFoundException - Route [admin.users.edit] not defined

## Problem

Saat mengakses halaman `/admin/users`, terjadi error karena route `admin.users.edit` tidak terdefinisi di `routes/web.php`. 

Error terjadi di:
- File: `resources/views/components/users/user-table.blade.php:88`
- Route yang dipanggil: `route('admin.users.edit', $user)`

## Root Cause

1. **Missing routes**: Routes untuk `edit`, `update`, dan `destroy` tidak terdefinisi di `routes/web.php`
2. **Duplicate route name**: Route `role-requests.store` didefinisikan 2 kali:
   - Baris 43: `Route::post('/role-change-requests', ...)->name('role-requests.store')`
   - Baris 196: Di dalam group dengan prefix `role-requests.`

## Solution

### 1. Menambahkan Missing Routes

Menambahkan route `edit`, `update`, dan `destroy` ke dalam group user management di `routes/web.php`:

```php
// User Management (HR can view users and create new users)
Route::middleware('role:hr')->group(function () {
    Route::get('users', [AdminUserController::class, 'index'])->name('users.index');
    Route::get('users/create', [AdminUserController::class, 'create'])->name('users.create');
    Route::post('users', [AdminUserController::class, 'store'])->name('users.store');
    Route::get('users/{user}/edit', [AdminUserController::class, 'edit'])->name('users.edit');
    Route::put('users/{user}', [AdminUserController::class, 'update'])->name('users.update');
    Route::delete('users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');
});
```

### 2. Menghapus Duplikasi Route

Menghapus definisi route `role-requests.store` dan `role-requests.cancel` yang berada di luar group (baris 43-44) dan memindahkan `cancel` ke dalam group yang sesuai:

**Before:**
```php
Route::middleware(['auth'])->group(function () {
    // Role Change Requests (User can submit/cancel from profile)
    Route::post('/role-change-requests', [...])->name('role-requests.store'); // DUPLICATE
    Route::delete('/role-change-requests/{roleChangeRequest}', [...])->name('role-requests.cancel');
    
    // ... other routes ...
    
    Route::prefix('role-requests')->name('role-requests.')->group(function () {
        Route::post('/', [...])->name('store'); // DUPLICATE dengan di atas
        // ...
    });
});
```

**After:**
```php
Route::middleware(['auth'])->group(function () {
    // ... other routes ...
    
    Route::prefix('role-requests')->name('role-requests.')->group(function () {
        Route::get('create', [RoleChangeRequestController::class, 'create'])->name('create');
        Route::post('/', [RoleChangeRequestController::class, 'store'])->name('store');
        Route::get('my-requests', [RoleChangeRequestController::class, 'myRequests'])->name('my-requests');
        Route::delete('{roleChangeRequest}', [RoleChangeRequestController::class, 'cancel'])->name('cancel');
    });
});
```

## Verification

Setelah perbaikan, verifikasi dengan command:

```powershell
# Lihat semua route admin.users
php artisan route:list --name=admin.users

# Output:
# GET|HEAD  admin/users ............................ admin.users.index
# POST      admin/users ............................ admin.users.store
# GET|HEAD  admin/users/create ................... admin.users.create
# PUT       admin/users/{user} ................... admin.users.update
# DELETE    admin/users/{user} ................. admin.users.destroy
# GET|HEAD  admin/users/{user}/edit .................. admin.users.edit

# Verifikasi tidak ada duplikasi role-requests
php artisan route:list --name=role-requests

# Clear route cache
php artisan route:clear
```

## Related Files

- `routes/web.php` - Route definitions
- `app/Http/Controllers/Admin/UserController.php` - Controller dengan method edit, update, destroy
- `resources/views/admin/users/edit.blade.php` - View untuk edit user
- `resources/views/components/users/user-table.blade.php` - Component yang memanggil route edit

## Controller Methods

Controller `UserController` sudah memiliki semua method yang diperlukan:
- ✅ `index()` - List users
- ✅ `create()` - Form create user
- ✅ `store()` - Save new user
- ✅ `edit()` - Form edit user
- ✅ `update()` - Update user
- ✅ `destroy()` - Delete user

## Testing

1. Login sebagai user dengan role `hr` (contoh: `bagas`)
2. Akses `/admin/users`
3. Klik tombol "Edit" pada salah satu user
4. Form edit user harus terbuka tanpa error
5. Test juga tombol "Delete" untuk memastikan route destroy berfungsi

## Notes

- Route `admin.users.edit`, `admin.users.update`, dan `admin.users.destroy` hanya bisa diakses oleh user dengan role `hr`
- HR tidak bisa menghapus akun mereka sendiri (ada validasi di controller)
- Saat menghapus user, semua relasi (projects, roles, skills, modals, links) akan dihapus juga
