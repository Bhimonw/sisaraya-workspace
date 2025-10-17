# User Management Components

Komponen-komponen reusable untuk halaman Manajemen Anggota HR.

## 📦 Komponen yang Tersedia

### Core Components
- **`user-card.blade.php`** - Menampilkan user dalam format card dengan avatar, info, dan actions
- **`user-table.blade.php`** - Tabel lengkap dengan header dan data user
- **`user-grid.blade.php`** - Grid layout responsif untuk user cards
- **`role-badge.blade.php`** - Badge dengan warna konsisten untuk setiap role
- **`page-header.blade.php`** - Header halaman dengan title dan action button
- **`view-toggle.blade.php`** - Toggle untuk switch Grid/Table view

## 🚀 Quick Start

```blade
{{-- Grid View --}}
<x-users.user-grid :users="$users" />

{{-- Table View --}}
<x-users.user-table :users="$users" />

{{-- Single Card --}}
<x-users.user-card :user="$user" />

{{-- Role Badge --}}
<x-users.role-badge role="pm" />

{{-- Page Header --}}
<x-users.page-header 
    title="Manajemen Anggota"
    :actionUrl="route('admin.users.create')"
/>

{{-- View Toggle --}}
<x-users.view-toggle currentView="grid" />
```

## 📖 Dokumentasi Lengkap

Lihat: [`docs/USER_MANAGEMENT_COMPONENTS.md`](../../../docs/USER_MANAGEMENT_COMPONENTS.md)

## 🎨 Design Principles

- **Consistent** - Warna dan styling yang seragam
- **Flexible** - Props untuk customization
- **Accessible** - Semantic HTML dan ARIA labels
- **Responsive** - Mobile-first approach
- **Modern** - Tailwind CSS dengan gradients dan transitions

## 🔗 Related

- Main implementation: `admin/users/index.blade.php`
- Controller: `app/Http/Controllers/Admin/UserController.php`
- Models: `app/Models/User.php`
