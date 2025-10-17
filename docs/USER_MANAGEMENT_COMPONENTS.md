# 📦 Komponen User Management - Dokumentasi

## Overview

Dokumentasi untuk komponen-komponen reusable yang digunakan pada halaman Manajemen Anggota HR. Komponen-komponen ini dirancang untuk modularitas, konsistensi UI, dan kemudahan maintenance.

---

## 🗂️ Daftar Komponen

### 1. **User Card** (`components/users/user-card.blade.php`)

Komponen untuk menampilkan informasi user dalam format card dengan desain modern.

**Props:**
- `user` (required) - Model User yang akan ditampilkan
- `canDelete` (optional, default: `true`) - Menampilkan tombol hapus atau tidak
- `showActions` (optional, default: `true`) - Menampilkan action buttons (edit/delete)

**Fitur:**
- Avatar dengan initial 2 huruf pertama nama
- Badge ID user di pojok kanan atas
- Status online indicator (hijau)
- Informasi: nama, username, email
- Badge roles dengan warna konsisten
- Tombol Edit dan Hapus dengan konfirmasi
- Hover effects dan transitions

**Contoh Penggunaan:**
```blade
<x-users.user-card :user="$user" />

<x-users.user-card :user="$user" :canDelete="false" />

<x-users.user-card :user="$user" :showActions="false" />
```

---

### 2. **User Table** (`components/users/user-table.blade.php`)

Komponen tabel untuk menampilkan daftar user dalam format tabel responsif.

**Props:**
- `users` (required) - Collection dari User models
- `canDelete` (optional, default: `true`) - Menampilkan tombol hapus atau tidak

**Fitur:**
- Header dengan gradient background
- Avatar mini untuk setiap user
- Kolom: ID, Anggota (nama + avatar), Username, Email, Roles, Actions
- Hover effect pada baris (gradient violet-blue)
- Empty state dengan icon dan pesan
- Responsive dan scrollable horizontal

**Contoh Penggunaan:**
```blade
<x-users.user-table :users="$users" />

<x-users.user-table :users="$users" :canDelete="false" />
```

---

### 3. **User Grid** (`components/users/user-grid.blade.php`)

Komponen grid untuk menampilkan user cards dalam layout grid responsif.

**Props:**
- `users` (required) - Collection dari User models
- `canDelete` (optional, default: `true`) - Menampilkan tombol hapus atau tidak

**Fitur:**
- Layout grid responsif: 1 kolom (mobile), 2 kolom (tablet), 3 kolom (desktop)
- Menggunakan komponen `user-card` untuk setiap user
- Empty state dengan ilustrasi dan pesan lengkap
- Gap spacing yang konsisten

**Contoh Penggunaan:**
```blade
<x-users.user-grid :users="$users" />

<x-users.user-grid :users="$users" :canDelete="false" />
```

---

### 4. **Role Badge** (`components/users/role-badge.blade.php`)

Komponen badge untuk menampilkan role dengan warna yang konsisten.

**Props:**
- `role` (required) - Nama role (string)

**Fitur:**
- Warna konsisten untuk setiap role:
  - `pm` - Violet
  - `hr` - Blue
  - `sekretaris` - Amber
  - `bendahara` - Green
  - `kewirausahaan` - Emerald
  - `media` - Pink
  - `pr` - Indigo
  - `researcher` - Teal
  - `talent_manager` - Cyan
  - `talent` - Lime
  - `member` - Gray
- Text uppercase untuk konsistensi
- Border dan background dengan opacity

**Contoh Penggunaan:**
```blade
<x-users.role-badge role="pm" />

<x-users.role-badge role="hr" />

@foreach($user->roles as $role)
    <x-users.role-badge :role="$role->name" />
@endforeach
```

---

### 5. **Page Header** (`components/users/page-header.blade.php`)

Komponen header halaman dengan title, description, dan action button.

**Props:**
- `title` (optional, default: `'Page Title'`) - Judul halaman
- `description` (optional, default: `null`) - Deskripsi halaman
- `actionUrl` (optional, default: `null`) - URL untuk action button
- `actionText` (optional, default: `'Tambah Baru'`) - Text untuk action button
- `actionIcon` (optional, default: `true`) - Menampilkan icon plus atau tidak
- `showAction` (optional, default: `true`) - Menampilkan action button atau tidak

**Fitur:**
- Layout flexbox responsif
- Gradient button dengan hover effects
- Optional description
- Icon plus SVG (dapat dinonaktifkan)

**Contoh Penggunaan:**
```blade
<x-users.page-header 
    title="Manajemen Anggota"
    description="Kelola akun pengguna Sisaraya"
    :actionUrl="route('admin.users.create')"
    actionText="Tambah User Baru"
/>

<x-users.page-header 
    title="Daftar Member"
    :showAction="false"
/>
```

---

### 6. **View Toggle** (`components/users/view-toggle.blade.php`)

Komponen toggle untuk switch antara Grid dan Table view dengan Alpine.js.

**Props:**
- `currentView` (optional, default: `'grid'`) - View mode default (`'grid'` atau `'table'`)

**Fitur:**
- Alpine.js untuk state management
- Toggle button dengan icon grid dan table
- Active state dengan background violet
- Responsive: hide text pada mobile
- Smooth transition

**Contoh Penggunaan:**
```blade
<x-users.view-toggle currentView="grid" />

<x-users.view-toggle currentView="table" />

<!-- Dengan slot untuk konten tambahan -->
<x-users.view-toggle currentView="grid">
    <button>Filter</button>
</x-users.view-toggle>
```

---

## 🎨 Implementasi Lengkap di Index Page

**File:** `resources/views/admin/users/index.blade.php`

```blade
@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto" x-data="{ viewMode: 'grid' }">
    
    {{-- Page Header --}}
    <x-users.page-header 
        title="Manajemen Anggota"
        description="Kelola akun pengguna Sisaraya"
        :actionUrl="route('admin.users.create')"
        actionText="Tambah User Baru"
    />

    {{-- Success/Error Messages --}}
    @if(session('success'))
        <div class="mb-6 bg-gradient-to-r from-green-50...">
            <!-- Success message -->
        </div>
    @endif

    {{-- Stats & View Toggle --}}
    <div class="mb-6 flex justify-between items-center">
        <div class="flex items-center gap-2">
            <!-- Stats -->
            Total: <span class="font-bold">{{ $users->count() }}</span> anggota
        </div>
        <x-users.view-toggle currentView="grid" />
    </div>

    {{-- Users Grid View --}}
    <div x-show="viewMode === 'grid'" x-transition>
        <x-users.user-grid :users="$users" />
    </div>

    {{-- Users Table View --}}
    <div x-show="viewMode === 'table'" x-transition x-cloak>
        <x-users.user-table :users="$users" />
    </div>

</div>
@endsection
```

---

## 🎯 Keuntungan Modularisasi

### 1. **Reusability**
- Komponen dapat digunakan kembali di berbagai halaman
- Tidak perlu copy-paste code
- Update di satu tempat, berlaku di semua tempat

### 2. **Consistency**
- UI/UX yang konsisten di seluruh aplikasi
- Warna role badge yang seragam
- Pattern yang sama untuk semua user displays

### 3. **Maintainability**
- Mudah di-update dan di-debug
- Code lebih terstruktur dan clean
- Separation of concerns yang jelas

### 4. **Flexibility**
- Props yang fleksibel untuk berbagai use case
- Dapat di-customize tanpa mengubah komponen
- Support conditional rendering

### 5. **Better Developer Experience**
- Code lebih mudah dibaca
- Dokumentasi yang jelas
- Naming convention yang konsisten

---

## 🔄 Cara Extend Komponen

### Menambahkan Role Baru

Edit `components/users/role-badge.blade.php`:

```blade
$roleColors = [
    'pm' => 'bg-violet-100 text-violet-700 border-violet-200',
    'hr' => 'bg-blue-100 text-blue-700 border-blue-200',
    // Tambahkan role baru di sini
    'new_role' => 'bg-purple-100 text-purple-700 border-purple-200',
];
```

### Menambahkan Kolom Baru di Table

Edit `components/users/user-table.blade.php`:

```blade
<!-- Tambahkan header -->
<th class="px-6 py-4...">Status</th>

<!-- Tambahkan cell -->
<td class="px-6 py-4...">
    {{ $user->status }}
</td>
```

### Custom Action Button di Card

Edit `components/users/user-card.blade.php` atau buat variant baru:

```blade
@if($showActions)
<div class="flex flex-col gap-2 items-end">
    <!-- Existing actions -->
    
    <!-- New custom action -->
    <a href="{{ route('admin.users.permissions', $user) }}" 
       class="inline-flex items-center gap-1 px-3 py-1.5 bg-purple-500 text-white...">
        Permissions
    </a>
</div>
@endif
```

---

## 📝 Best Practices

1. **Selalu gunakan props dengan type hints yang jelas**
2. **Berikan default values untuk optional props**
3. **Gunakan naming convention yang konsisten**
4. **Tambahkan comments untuk logic yang kompleks**
5. **Test semua variants dari komponen**
6. **Maintain dokumentasi saat update komponen**

---

## 🧪 Testing Checklist

- [ ] User card menampilkan informasi dengan benar
- [ ] Role badges menampilkan warna yang sesuai
- [ ] Tombol Edit membuka halaman edit yang benar
- [ ] Tombol Hapus menampilkan konfirmasi
- [ ] Toggle view bekerja smooth antara grid dan table
- [ ] Empty state ditampilkan saat tidak ada data
- [ ] Responsive di mobile, tablet, dan desktop
- [ ] User tidak bisa hapus dirinya sendiri
- [ ] Success/error messages muncul dengan benar

---

## 🎨 Design System

**Color Palette (Roles):**
- Violet: PM, Management
- Blue: HR, Human Resources
- Green: Finance, Bendahara
- Amber: Secretary, Sekretaris
- Emerald: Entrepreneurship, Kewirausahaan
- Pink: Media
- Indigo: Public Relations
- Teal: Researcher
- Cyan: Talent Manager
- Lime: Talent
- Gray: Member, Default

**Typography:**
- Headers: Bold, 3xl untuk page title
- Subtext: Regular, sm untuk descriptions
- Body: Medium, base untuk content

**Spacing:**
- Gap between cards: 6 (1.5rem)
- Padding in cards: 6 (1.5rem)
- Margin bottom sections: 6 (1.5rem)

---

## 📚 Related Files

- `resources/views/admin/users/index.blade.php` - Main implementation
- `resources/views/admin/users/edit.blade.php` - Edit page (can be refactored too)
- `resources/views/admin/users/create.blade.php` - Create page (can be refactored too)
- `app/Http/Controllers/Admin/UserController.php` - Controller
- `docs/CHANGELOG.md` - Change log

---

**Dibuat:** 17 Oktober 2025  
**Author:** Copilot Agent  
**Status:** ✅ Production Ready
