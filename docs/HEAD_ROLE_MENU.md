# HEAD Role - Monitoring Menu (View-Only Access)

**Date**: October 21, 2025  
**Role**: `head` (Head of SISARAYA)

## Overview

Role `head` adalah role khusus untuk posisi kepemimpinan tertinggi SISARAYA (Head of Organization). Role ini dirancang untuk memberikan akses **monitoring dan oversight** terhadap seluruh aktivitas organisasi tanpa kemampuan untuk melakukan perubahan langsung.

## Role Philosophy

**"Pengawas Tertinggi dengan Akses View-Only"**

- ✅ Dapat **melihat** semua proyek, tiket, usaha, dan dokumen
- ✅ Dapat **claim tiket** untuk ikut berkontribusi
- ✅ Dapat **aktif di chat** proyek
- ❌ **Tidak dapat** create/update/delete proyek
- ❌ **Tidak dapat** create/update/delete tiket untuk orang lain
- ❌ **Tidak dapat** approve/reject business proposals

## Menu Structure

Role `head` memiliki menu khusus di bagian **"Ruang Management"** dengan visual yang berbeda (amber/orange theme) untuk membedakan dari role management lainnya:

### 1. Monitoring Proyek
- **Route**: `projects.index`, `projects.show`
- **Akses**: View only
- **Fitur**:
  - Melihat semua proyek (active & completed)
  - Melihat detail proyek
  - Melihat member proyek
  - Melihat chat proyek
  - Melihat kalender proyek
- **Tidak Bisa**:
  - Create proyek baru
  - Edit/delete proyek
  - Manage member proyek
  - Blackout proyek

### 2. Monitoring Tiket
- **Route**: `tickets.index`, `tickets.show`
- **Akses**: View + Claim
- **Fitur**:
  - Melihat semua tiket
  - Claim tiket yang ditujukan untuk role tertentu
  - Update status tiket yang di-claim
  - Melihat detail tiket
- **Tidak Bisa**:
  - Create tiket umum (hanya PM yang bisa)
  - Assign tiket ke user lain
  - Delete tiket

### 3. Monitoring Usaha
- **Route**: `businesses.index`, `businesses.show`
- **Akses**: View only
- **Fitur**:
  - Melihat semua business proposals
  - Melihat detail usaha
  - Melihat laporan usaha
  - Melihat status approval
- **Tidak Bisa**:
  - Create business proposal
  - Approve/reject business
  - Convert business to project
  - Upload laporan usaha

### 4. Monitoring Dokumen
- **Route**: `documents.index`
- **Akses**: View only (public documents)
- **Fitur**:
  - Melihat dokumen umum
  - Download dokumen
  - Filter dokumen by project/type
- **Tidak Bisa**:
  - Upload dokumen baru
  - Delete dokumen
  - Akses dokumen rahasia (hanya HR & Sekretaris)

## Menu Comparison: HEAD vs PM

| Feature | HEAD | PM |
|---------|------|-----|
| **View Projects** | ✅ All projects | ✅ All projects |
| **Create Project** | ❌ | ✅ |
| **Edit/Delete Project** | ❌ | ✅ (owner only) |
| **View Tickets** | ✅ All tickets | ✅ All tickets |
| **Claim Tickets** | ✅ | ✅ |
| **Create General Tickets** | ❌ | ✅ |
| **View Businesses** | ✅ All businesses | ✅ All businesses |
| **Approve Business** | ❌ | ✅ |
| **View Documents** | ✅ Public only | ✅ All documents |
| **Upload Documents** | ❌ | ✅ |

## Permissions

Role `head` memiliki permission berikut (dari `RolePermissionSeeder.php`):

```php
Role::where('name', 'head')->first()?->givePermissionTo([
    'projects.view',      // View all projects
    'tickets.view_all',   // View all tickets
    'tickets.update_status', // Update claimed tickets
    'documents.view_all'  // View public documents
]);
```

## Menu Implementation

Menu `head` ada di `resources/views/layouts/_menu.blade.php`:

```blade
@role('head')
    <li>
        <a href="{{ route('projects.index') }}" class="... {{ $active ? 'bg-amber-100 text-amber-900' : '...' }}">
            <svg>...</svg>
            <span>
                Monitoring Proyek
                <span class="block text-[10px] text-gray-500">View Only</span>
            </span>
        </a>
    </li>
    <!-- ... other monitoring menus ... -->
@endrole
```

**Design Notes**:
- Menggunakan `bg-amber-100 text-amber-900` untuk highlight active state
- Label "View Only" pada setiap menu item
- Icon mata (eye) untuk menu Monitoring Proyek

## Use Case

### Scenario: Yahya sebagai Head of SISARAYA

Yahya adalah kepala organisasi SISARAYA. Ia ingin:

1. **Monitoring Progress**
   - Melihat status semua proyek yang sedang berjalan
   - Melihat berapa tiket yang belum selesai
   - Melihat proposal usaha yang sedang diajukan

2. **Stay Updated**
   - Ikut chat di proyek-proyek penting
   - Claim tiket untuk ikut berkontribusi langsung
   - Melihat dokumen dan laporan terbaru

3. **Tidak Interfere**
   - Tidak membuat proyek sendiri (koordinasi dengan PM)
   - Tidak approve/reject proposal (koordinasi dengan PM)
   - Tidak manage member (koordinasi dengan HR)

## Controller Logic

Untuk memastikan head hanya bisa view, tambahkan gate check di controller:

```php
// ProjectController@create
public function create()
{
    // Head tidak boleh create
    abort_if(auth()->user()->hasRole('head') && !auth()->user()->hasRole('pm'), 403, 
        'Head role tidak dapat membuat proyek. Hubungi PM untuk membuat proyek baru.');
    
    // ... rest of code
}

// ProjectController@update
public function update(Request $request, Project $project)
{
    // Head tidak boleh update
    abort_if(auth()->user()->hasRole('head') && !auth()->user()->hasRole('pm'), 403,
        'Head role tidak dapat mengedit proyek.');
    
    // ... rest of code
}
```

## Testing

### Manual Test Checklist

Login sebagai user dengan role `head`:

**1. Menu Visibility**
- [ ] Menu "Ruang Management" muncul
- [ ] Submenu "Monitoring Proyek" muncul dengan label "View Only"
- [ ] Submenu "Monitoring Tiket" muncul dengan label "View Only"
- [ ] Submenu "Monitoring Usaha" muncul dengan label "View Only"
- [ ] Submenu "Monitoring Dokumen" muncul dengan label "View Only"

**2. Projects Page**
- [ ] Bisa akses `/projects`
- [ ] Bisa lihat list semua proyek
- [ ] Bisa klik detail proyek
- [ ] **Tidak ada** tombol "Buat Proyek Baru"
- [ ] **Tidak ada** tombol "Edit" di detail proyek

**3. Tickets Page**
- [ ] Bisa akses `/tickets`
- [ ] Bisa lihat list semua tiket
- [ ] Bisa klik detail tiket
- [ ] Bisa claim tiket yang open
- [ ] **Tidak ada** menu "Buat Tiket Umum" (hanya PM)

**4. Businesses Page**
- [ ] Bisa akses `/businesses`
- [ ] Bisa lihat list semua business
- [ ] Bisa klik detail business
- [ ] **Tidak ada** tombol "Buat Proposal"
- [ ] **Tidak ada** tombol "Approve/Reject"

**5. Documents Page**
- [ ] Bisa akses `/documents`
- [ ] Bisa lihat dokumen umum
- [ ] Bisa download dokumen
- [ ] **Tidak ada** tombol "Upload Dokumen"
- [ ] **Tidak ada** akses dokumen rahasia

## Notes

- Role `head` bisa dikombinasikan dengan role lain (multi-role system)
- Jika user memiliki `head` + `pm`, maka akan mendapat akses penuh dari PM
- Menu head menggunakan warna amber untuk consistency dengan theme "monitoring/oversight"
- Label "View Only" membantu user memahami limitasi akses mereka

## Future Enhancements

Potential features untuk role `head`:

1. **Dashboard Khusus Head**
   - Overview semua proyek aktif
   - KPI dan metrics organisasi
   - Timeline milestone penting

2. **Reporting Access**
   - Generate comprehensive reports
   - Export data untuk board meetings
   - Visualisasi progress organisasi

3. **Notification Priority**
   - Notifikasi untuk milestone penting
   - Alert untuk proyek yang terlambat
   - Weekly summary email

## Related Files

- `database/seeders/RolesSeeder.php` - Role definition
- `database/seeders/RolePermissionSeeder.php` - Permissions
- `resources/views/layouts/_menu.blade.php` - Menu implementation
- `routes/web.php` - Routes (sama dengan role lain)
- `app/Http/Controllers/ProjectController.php` - Add authorization checks

## Changelog

- **2025-10-21**: Initial implementation of HEAD role menu with view-only access
