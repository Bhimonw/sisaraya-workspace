# Member Data Management System

## Overview
Sistem pendataan anggota lengkap yang memungkinkan setiap member untuk mengisi data pribadi, keahlian, modal kontribusi, dan link eksternal. Data dikirim ke sekretaris untuk dikelola secara terpusat.

## Features

### 1. Profile Enhancement
- **Photo Upload**: Upload foto profil (max 2MB, format: JPG/PNG/GIF)
- **Contact Information**: Nomor telepon dan WhatsApp
- **Bio**: Deskripsi singkat tentang diri sendiri
- Auto-delete old photo saat upload foto baru

### 2. Member Data Collection

#### Skills (Keahlian)
- Nama keahlian/skill
- Tingkat keahlian: Pemula, Menengah, Mahir, Expert
- Deskripsi detail
- Support multiple skills per user

#### Modal (Kontribusi)
- Jenis: Uang atau Alat
- Nama item/kontribusi
- Jumlah uang (untuk jenis "uang")
- Deskripsi
- Flag "dapat dipinjam" untuk resource sharing
- Support multiple modal entries

#### Links & External Contacts
- Nama link (e.g., Portfolio, LinkedIn, Instagram)
- Bidang/kategori
- URL
- Contact info (username, email, phone)
- Support multiple links

### 3. Sekretaris Management Dashboard
- View all members dengan data statistics
- Search by name or username
- Detail view untuk setiap member
- Export to CSV untuk reporting
- Notifikasi saat member update data

## Database Schema

### Users Table Additions
```php
- photo_path: string nullable
- phone: string nullable  
- whatsapp: string nullable
- bio: string (already exists)
```

### member_skills
```php
- id
- user_id (FK -> users)
- nama_skill: string
- tingkat_keahlian: enum(pemula,menengah,mahir,expert)
- deskripsi: text nullable
- timestamps
```

### member_modals
```php
- id
- user_id (FK -> users)
- jenis: enum(uang,alat)
- nama_item: string
- jumlah_uang: decimal nullable
- deskripsi: text nullable
- dapat_dipinjam: boolean default false
- timestamps
```

### member_links
```php
- id
- user_id (FK -> users)
- nama: string
- bidang: string nullable
- url: string nullable
- contact: string nullable
- timestamps
```

## Models

### User Model Relationships
```php
public function skills()
{
    return $this->hasMany(MemberSkill::class);
}

public function modals()
{
    return $this->hasMany(MemberModal::class);
}

public function links()
{
    return $this->hasMany(MemberLink::class);
}
```

### MemberSkill, MemberModal, MemberLink
All have `belongsTo(User::class)` relationship and proper fillable attributes.

## Controllers

### MemberDataController
**Routes**: `member-data.*`
**Access**: All authenticated users

Methods:
- `index()`: Display own data (skills, modals, links)
- `create()`: Show form to add data
- `store()`: Save data and notify sekretaris
- `update($type, $id)`: Update specific entry (skill/modal/link)
- `destroy($type, $id)`: Delete specific entry

### Admin\MemberDataAdminController
**Routes**: `admin.member-data.*`
**Access**: `sekretaris` role only

Methods:
- `index()`: List all members with data counts and search
- `show(User $user)`: Detailed view of member's data
- `export()`: Export all data to CSV

### ProfileController
Enhanced `update()` method to handle photo upload:
- Validate photo (image, max 2MB)
- Store in `storage/app/public/photos`
- Delete old photo automatically
- Update `photo_path` in database

## Routes

### Member Routes (All Users)
```php
GET  /member-data              → member-data.index
GET  /member-data/create       → member-data.create
POST /member-data              → member-data.store
PATCH /member-data/{type}/{id} → member-data.update
DELETE /member-data/{type}/{id} → member-data.destroy
```

### Admin Routes (Sekretaris Only)
```php
GET /admin/member-data           → admin.member-data.index
GET /admin/member-data/{user}    → admin.member-data.show
GET /admin/member-data-export    → admin.member-data.export
```

## Views

### Member Views
1. **member-data/index.blade.php**
   - Display user's own skills, modals, and links
   - Delete buttons for each entry
   - Link to create form

2. **member-data/form.blade.php**
   - Dynamic form using Alpine.js
   - Add multiple skills/modals/links in one submission
   - Validation and helpful hints
   - Info box about data being sent to sekretaris

### Admin Views (Sekretaris)
1. **admin/member-data/index.blade.php**
   - List all users with photo, roles, contact info
   - Data count badges (skills, modals, links)
   - Search functionality
   - Export CSV button
   - Pagination

2. **admin/member-data/show.blade.php**
   - Detailed member profile
   - All skills displayed with expertise levels
   - All modals with amounts and borrowability
   - All links with clickable URLs
   - Contact information

### Profile View
- **profile/partials/update-profile-information-form.blade.php**
  - Enhanced with photo upload field
  - Shows current photo or initial letter
  - Bio textarea
  - Phone and WhatsApp fields
  - Multi-role display (fixed from showing only first role)

## Notifications

### MemberDataNotification
**Type**: Database notification (queued)
**Sent to**: All users with `sekretaris` role
**Triggered by**: 
- Member creates new data
- Member updates existing data

Notification payload:
```php
[
    'title' => 'Data Member Diperbarui',
    'message' => "{username} - {action}",
    'user_id' => $user->id,
    'user_name' => $user->name,
    'action' => $action,
    'url' => route('admin.member-data.show', $user->id),
]
```

## Menu Integration

### Member Menu
New menu item added in sidebar (available to all authenticated users):
- **Icon**: Document with lines
- **Label**: "Data Kepegawaian"
- **Route**: `member-data.index`
- **Position**: Between "Voting" and "Akun & Pengaturan"

### Sekretaris Menu (Admin Section)
New submenu under "Manajemen & Laporan" section:
- **Icon**: Users group
- **Label**: "Data Anggota"
- **Route**: `admin.member-data.index`
- **Access**: `sekretaris` role only
- **Position**: After "Pengelolaan Arsip"

## Validation Rules

### Profile Update
```php
'photo' => ['image', 'max:2048'], // 2MB max
'phone' => ['nullable', 'string', 'max:20'],
'whatsapp' => ['nullable', 'string', 'max:20'],
'bio' => ['nullable', 'string', 'max:500'],
```

### Member Data Store
```php
'skills.*.nama_skill' => ['required', 'string', 'max:255'],
'skills.*.tingkat_keahlian' => ['required', 'in:pemula,menengah,mahir,expert'],
'skills.*.deskripsi' => ['nullable', 'string'],

'modals.*.jenis' => ['required', 'in:uang,alat'],
'modals.*.nama_item' => ['required', 'string', 'max:255'],
'modals.*.jumlah_uang' => ['nullable', 'numeric', 'min:0'],
'modals.*.deskripsi' => ['nullable', 'string'],
'modals.*.dapat_dipinjam' => ['boolean'],

'links.*.nama' => ['required', 'string', 'max:255'],
'links.*.bidang' => ['nullable', 'string', 'max:255'],
'links.*.url' => ['nullable', 'url', 'max:500'],
'links.*.contact' => ['nullable', 'string', 'max:255'],
```

## Usage Examples

### User Workflow
1. Login to system
2. Click "Data Kepegawaian" in sidebar
3. Click "+ Tambah Data"
4. Fill in skills, modal contributions, and links
5. Click "Simpan & Kirim ke Sekretaris"
6. Sekretaris receives notification
7. View/manage data anytime from "Data Kepegawaian" page

### Sekretaris Workflow
1. Receive notification when member adds/updates data
2. Go to "Manajemen & Laporan" → "Data Anggota"
3. Search for specific member or browse all
4. Click "Lihat Detail" to see full member profile
5. Export all data to CSV for reporting/analysis
6. Use data for project assignments, resource planning, etc.

### Profile Photo Upload
1. Go to "Akun & Pengaturan"
2. Click "Choose File" under "Foto Profil"
3. Select image (JPG/PNG/GIF, max 2MB)
4. Fill other profile fields as needed
5. Click "Save"
6. Old photo automatically deleted
7. New photo displayed in profile and throughout system

## Security Considerations

### Authorization
- **Member Data**: Users can only view/edit their OWN data
- **Admin Dashboard**: Restricted to `sekretaris` role via middleware
- **Photo Upload**: Validated file type and size
- **Cascade Delete**: When user deleted, all their data (skills/modals/links) automatically removed

### Data Privacy
- Member data only visible to:
  1. The member themselves (own data)
  2. Users with `sekretaris` role (all data)
- No public access to member data
- CSV export requires `sekretaris` role

## File Storage

Photos stored in: `storage/app/public/photos/`
Public access via: `/storage/photos/{filename}`

**Setup**: Run `php artisan storage:link` to create symlink

## Testing

### Manual Testing Checklist
- [ ] Upload photo from profile page
- [ ] Photo displays correctly throughout app
- [ ] Add multiple skills with different expertise levels
- [ ] Add modal contributions (both uang and alat)
- [ ] Add external links with URLs
- [ ] Sekretaris receives notification
- [ ] Sekretaris can view all member data
- [ ] CSV export includes all fields
- [ ] Search functionality works
- [ ] Delete individual entries
- [ ] Old photo deleted when uploading new one

### Edge Cases
- No photo (shows initial letter instead)
- Empty/null fields handled gracefully
- Very long text in deskripsi (textarea scrolls)
- Special characters in URLs
- Large numbers in jumlah_uang (formatted properly)

## Future Enhancements

Potential improvements for future iterations:
1. **Photo crop/resize**: Frontend image editor
2. **Bulk import**: CSV import for initial data load
3. **Data versioning**: Track changes over time
4. **Advanced search**: Filter by skills, modal type, etc.
5. **Data validation**: Email format, phone number format
6. **Resource booking**: If modal "dapat dipinjam", add booking system
7. **Skill endorsements**: Other members can endorse skills
8. **API endpoints**: For mobile app or external integrations
9. **Statistics dashboard**: Charts for skill distribution, modal summary

## Migration Notes

If deploying to existing system:
1. Run migrations: `php artisan migrate`
2. Create storage symlink: `php artisan storage:link`
3. Ensure `storage/app/public/photos` has write permissions
4. Notify users about new feature via announcement
5. Train sekretaris on new dashboard features

## Troubleshooting

**Photo not displaying?**
- Check storage link: `php artisan storage:link`
- Verify file exists in `storage/app/public/photos/`
- Check file permissions (should be readable by web server)

**Notification not received?**
- Queue must be running: `php artisan queue:work`
- Or use sync driver: `QUEUE_CONNECTION=sync` in `.env`
- Check notifications table for entries

**CSV export empty?**
- Verify sekretaris role has correct permissions
- Check if users have actually entered data
- Review browser console for download errors

## Related Documentation
- `docs/DOUBLE_ROLE_IMPLEMENTATION.md` - Multi-role system
- `docs/HR_USER_CREATION.md` - User management by HR
- `docs/PUSH_NOTIFICATION_GUIDE.md` - Notification system

---

**Implementation Date**: October 21, 2025  
**Status**: ✅ Fully Implemented  
**Test Coverage**: Manual testing required  
**Breaking Changes**: None
