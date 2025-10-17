# HR User Creation Feature

## Overview
Fitur ini memungkinkan HR untuk membuat user baru dalam sistem Sisaraya. Hanya user dengan role **HR** yang memiliki akses untuk menambah user baru.

## Access Control
- **Role Required**: `hr`
- **Permissions**: Hanya HR yang bisa mengakses halaman create dan submit form create user
- **Middleware**: `role:hr` diterapkan pada route group

## Routes
```php
Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('role:hr')->group(function () {
        Route::get('users', [AdminUserController::class, 'index'])->name('users.index');
        Route::get('users/create', [AdminUserController::class, 'create'])->name('users.create');
        Route::post('users', [AdminUserController::class, 'store'])->name('users.store');
    });
});
```

## User Flow

### 1. Navigasi ke Form Create
- HR masuk ke halaman **Manajemen Anggota** (`/admin/users`)
- Klik tombol **"Tambah User Baru"** di header

### 2. Mengisi Form Create User
Form memiliki 4 section utama:

#### A. Informasi Dasar (Required)
- **Nama Lengkap** (required): Nama lengkap user
- **Username** (required): Username unik untuk login, dengan prefix `@`
- **Email** (optional): Email user (boleh kosong)
- **Password** (required): Minimal 8 karakter
- **Konfirmasi Password** (required): Harus sama dengan password

#### B. Roles (Optional)
- Pilih satu atau lebih role untuk user:
  - **admin**: Akses penuh ke semua fitur
  - **pm**: Project Manager
  - **hr**: Human Resources
  - **finance**: Keuangan & RAB
  - **guest**: Akses terbatas ke proyek tertentu
  - Dan role lainnya...

**⚠️ PENTING - Role Guest:**
- Role **Guest** tidak dapat digabung dengan role lainnya (mutually exclusive)
- Guest adalah role khusus dengan akses terbatas hanya ke proyek tertentu
- Jika Guest dipilih, semua role lain akan otomatis di-uncheck
- Jika role lain dipilih saat Guest aktif, Guest akan otomatis di-uncheck
- Checkbox role lain akan disabled ketika Guest dipilih (dan sebaliknya)

#### C. Proyek (Conditional)
- Section ini **hanya muncul** jika role **Guest** dipilih
- Wajib memilih minimal 1 proyek aktif untuk role Guest
- Menampilkan daftar proyek dengan status "Aktif"

#### D. Action Buttons
- **Batal**: Kembali ke halaman index tanpa menyimpan
- **Buat User**: Submit form dan buat user baru

### 3. Validasi
Controller melakukan validasi:
```php
$data = $request->validate([
    'name' => 'required|string|max:255',
    'username' => 'required|string|max:255|unique:users,username',
    'email' => 'nullable|email|max:255|unique:users,email',
    'password' => 'required|string|min:8|confirmed',
    'roles' => 'nullable|array',
    'roles.*' => 'exists:roles,name',
    'projects' => 'nullable|array',
    'projects.*' => 'exists:projects,id',
]);
```

**Validasi Khusus**:
1. **Guest Role - Mutually Exclusive**:
   - Jika role `guest` dipilih bersama role lain, error akan ditampilkan
   - Error message: "Role Guest tidak dapat digabung dengan role lainnya."
   
2. **Guest Role - Project Required**:
   - Jika role `guest` dipilih, **harus** memilih minimal 1 proyek
   - Error message: "User dengan role Guest harus memilih minimal satu proyek."

### 4. Proses Pembuatan User
1. User dibuat dengan data yang diinput
2. Password di-hash menggunakan `Hash::make()`
3. Roles di-sync ke user menggunakan `syncRoles()`
4. Jika role Guest: proyek di-attach menggunakan `attach()`
5. Redirect ke halaman index dengan success message

## Features

### Dynamic Projects Section
- Menggunakan Alpine.js untuk show/hide section proyek
- Section proyek otomatis muncul ketika checkbox "Guest" dicentang
- Section otomatis hilang ketika checkbox "Guest" di-uncheck

### Guest Role Mutual Exclusion
- Alpine.js logic untuk mencegah Guest dicampur dengan role lain
- Computed properties: `isGuestSelected`, `hasOtherRoles`
- Method `toggleRole()` untuk handle checkbox changes
- Ketika Guest dipilih: semua role lain di-disable dan di-uncheck
- Ketika role lain dipilih dan Guest aktif: Guest di-uncheck
- Visual feedback: disabled checkboxes menggunakan opacity dan cursor-not-allowed
- Warning box dengan background amber untuk mengingatkan user

### Username Format
- Prefix `@` ditampilkan di input field untuk konsistensi
- Username harus unik di database
- Petunjuk: "Username harus unik dan tidak bisa diubah"

### Password Security
- Minimal 8 karakter
- Konfirmasi password wajib
- Password di-hash sebelum disimpan ke database

### Guest Role Special Handling
- Guest **hanya** bisa akses proyek yang di-assign
- Jika Guest dipilih, wajib pilih proyek
- Jika role Guest dihapus, semua project associations dihapus

## UI Components

### Page Header
Menggunakan component `x-users.page-header`:
```blade
<x-users.page-header 
    title="Manajemen Anggota"
    description="Kelola akun pengguna Sisaraya"
    :actionUrl="route('admin.users.create')"
    actionText="Tambah User Baru"
/>
```

### Form Layout
- Responsive design dengan Tailwind CSS
- Section dividers dengan border dan heading
- Icons untuk setiap section
- Gradient buttons untuk primary actions

## Error Handling

### Validation Errors
- Inline error messages di bawah setiap field
- Red border pada field yang error
- Error messages dalam Bahasa Indonesia

### Success Messages
- Flash message dengan gradient background
- Icon check untuk visual feedback
- Auto-dismiss atau bisa di-close manual

## Controller Methods

### `create()`
```php
public function create()
{
    $roles = Role::orderBy('name')->get();
    $projects = Project::where('status', 'active')->orderBy('name')->get();
    return view('admin.users.create', compact('roles', 'projects'));
}
```

### `store(Request $request)`
```php
public function store(Request $request)
{
    // Validate input
    // Check guest role validation
    // Create user
    // Sync roles
    // Attach projects if guest
    // Redirect with success
}
```

## Security Notes

1. **Authorization**: Hanya HR yang bisa akses endpoint create dan store
2. **Validation**: Semua input di-validate sebelum disimpan
3. **Password**: Di-hash menggunakan bcrypt melalui `Hash::make()`
4. **Unique Constraints**: Username dan email harus unik
5. **CSRF Protection**: Form menggunakan `@csrf` token

## Related Files

### Routes
- `routes/web.php` - Route definitions

### Controllers
- `app/Http/Controllers/Admin/UserController.php` - Controller logic

### Views
- `resources/views/admin/users/index.blade.php` - List users with "Tambah User" button
- `resources/views/admin/users/create.blade.php` - Create user form
- `resources/views/components/users/page-header.blade.php` - Header component

### Models
- `app/Models/User.php` - User model
- `spatie/laravel-permission` - Role & permission management

## Testing Checklist

- [ ] HR dapat mengakses halaman create user
- [ ] Non-HR tidak bisa akses halaman create (redirect/403)
- [ ] Form validation berfungsi untuk semua field
- [ ] Username harus unik
- [ ] Email harus unik (jika diisi)
- [ ] Password minimal 8 karakter
- [ ] Password confirmation harus match
- [ ] Guest role dengan role lain menampilkan error
- [ ] Guest role tanpa proyek menampilkan error
- [ ] Guest role dengan proyek berhasil dibuat
- [ ] User tanpa role Guest berhasil dibuat
- [ ] Multiple roles bisa dipilih (kecuali Guest)
- [ ] Guest checkbox disable semua role lain
- [ ] Role lain disable Guest checkbox
- [ ] Projects section muncul/hilang saat Guest checked/unchecked
- [ ] Success message muncul setelah user dibuat
- [ ] User baru muncul di halaman index
- [ ] Warning box tentang Guest mutual exclusion tampil di form

## Future Improvements

1. **Email Notification**: Kirim email welcome dengan password ke user baru
2. **Auto Password Generate**: Option untuk generate random password otomatis
3. **Bulk User Import**: Import multiple users dari CSV/Excel
4. **User Avatar Upload**: Upload foto profil saat create user
5. **Department/Division**: Tambah field department/division untuk user
6. **Email Verification**: Require email verification untuk keamanan
