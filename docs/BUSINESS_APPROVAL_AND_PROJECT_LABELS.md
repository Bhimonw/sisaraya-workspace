# Business Approval Workflow & Project Labels

## 📋 Overview
Implementasi approval workflow untuk usaha baru (kewirausahaan → PM) dan sistem label untuk project (UMKM, DIVISI, Kegiatan).

---

## 🏢 Business Approval Workflow

### Database Schema
**Migration**: `2025_10_16_235504_add_approval_to_businesses_table.php`

Kolom baru di tabel `businesses`:
- `status` (enum): `pending`, `approved`, `rejected` - Default: `pending`
- `approved_by` (foreignId nullable): User ID PM yang approve/reject
- `approved_at` (timestamp nullable): Waktu approval/rejection
- `rejection_reason` (text nullable): Alasan jika ditolak

### Business Model Features

**Relationships:**
```php
$business->creator  // User yang membuat usaha
$business->approver // PM yang approve/reject
```

**Scopes:**
```php
Business::pending()   // Hanya yang menunggu approval
Business::approved()  // Hanya yang disetujui
Business::rejected()  // Hanya yang ditolak
```

**Helper Methods:**
```php
$business->isPending()  // bool
$business->isApproved() // bool
$business->isRejected() // bool
$business->getStatusColor()  // 'yellow', 'green', 'red'
$business->getStatusLabel()  // 'Menunggu Persetujuan', 'Disetujui', 'Ditolak'
```

### Business Controller Workflow

#### 1. Create Business (Kewirausahaan)
```php
POST /businesses
Permission: business.create
```

**Flow:**
1. Kewirausahaan mengisi form create business
2. Status otomatis di-set `pending`
3. Notifikasi dikirim ke SEMUA PM
4. Redirect ke index dengan pesan "Menunggu persetujuan PM"

#### 2. Approve Business (PM)
```php
POST /businesses/{id}/approve
Permission: business.approve
Policy: Only PM, only if status pending
```

**Flow:**
1. PM klik tombol "Setujui" di halaman detail business
2. Status diubah ke `approved`
3. `approved_by` dan `approved_at` diisi
4. `rejection_reason` di-clear (null)

#### 3. Reject Business (PM)
```php
POST /businesses/{id}/reject
Permission: business.approve
Policy: Only PM, only if status pending
```

**Flow:**
1. PM klik tombol "Tolak" → muncul modal
2. PM wajib mengisi alasan penolakan
3. Status diubah ke `rejected`
4. `approved_by`, `approved_at`, `rejection_reason` diisi

### Business Policy

**Authorization Rules:**
- `approve()`: Only PM + business must be pending
- `update()`: Only creator + business must be pending
- `delete()`: Creator (if pending) OR PM (anytime)

### Notification System

**BusinessNeedsApproval Notification:**
- Channel: `database` (in-app notification)
- Sent to: All users with role `pm`
- Payload:
  ```php
  [
      'business_id' => $business->id,
      'business_name' => $business->name,
      'creator_name' => $business->creator->name,
      'message' => "Usaha baru '{$business->name}' perlu persetujuan Anda",
      'action_url' => route('businesses.show', $business),
  ]
  ```

### UI Features

#### Index Page (`/businesses`)
- **Filter by status**: Semua, Menunggu Persetujuan, Disetujui, Ditolak
- **Status badge**: Color-coded (yellow/green/red)
- **Creator info**: "Dibuat oleh: [name]"
- **Approver info**: "Disetujui oleh: [name] pada [date]" (jika sudah di-approve)

#### Show Page (`/businesses/{id}`)
- **Status badge**: Large, prominent
- **Approval buttons** (PM only, if pending):
  - "Setujui" (green button)
  - "Tolak" (red button → modal)
- **Rejection modal**: Textarea untuk alasan (required)
- **Business details**: Description, creator, dates
- **Approval history**: Who approved/rejected + when
- **Rejection reason box**: Red background, visible jika ditolak

---

## 🏷️ Project Labels

### Database Schema
**Migration**: `2025_10_16_235508_add_label_to_projects_table.php`

Kolom baru di tabel `projects`:
- `label` (enum nullable): `UMKM`, `DIVISI`, `Kegiatan`

### Project Model Features

**Static Methods:**
```php
Project::getLabels()  // ['UMKM', 'DIVISI', 'Kegiatan']
Project::getLabelColor($label)  // 'purple', 'blue', 'green'
```

**Scope:**
```php
Project::byLabel('UMKM')->get()  // Filter by label
```

**Label Colors:**
- UMKM → `purple`
- DIVISI → `blue`
- Kegiatan → `green`

### Project Controller Updates

**Validation (store & update):**
```php
'label' => 'nullable|in:UMKM,DIVISI,Kegiatan'
```

**Index with filters:**
```php
GET /projects?status=active&label=UMKM
```

### UI Features

#### Create/Edit Form
- **Label dropdown**: After status field
- **Options**: -- Pilih Label --, UMKM, DIVISI, Kegiatan
- **Optional**: User boleh tidak memilih label

#### Project Cards (Index)
- **Label badge**: Displayed next to status badge
- **Color-coded**: Purple (UMKM), Blue (DIVISI), Green (Kegiatan)
- **Filter buttons**: Horizontal filter above cards

#### Filter System
```
[Semua] [UMKM] [DIVISI] [Kegiatan]
```
- Combined with status filter
- URL: `/projects?status=active&label=UMKM`

---

## 🔐 Permissions

### New Permission
- `business.approve` - PM can approve/reject businesses

### Permission Assignment (RolePermissionSeeder)
```php
'pm' => [...existing..., 'business.approve']
```

---

## 🧪 Testing Checklist

### Business Approval Workflow
- [ ] Kewirausahaan dapat create business → status auto `pending`
- [ ] Notifikasi terkirim ke semua PM
- [ ] PM melihat tombol approve/reject hanya untuk pending business
- [ ] PM dapat approve → status jadi `approved`, `approved_by` terisi
- [ ] PM dapat reject dengan alasan → status jadi `rejected`, `rejection_reason` terisi
- [ ] Filter by status berfungsi (pending/approved/rejected)
- [ ] Creator tidak bisa edit business yang sudah approved/rejected
- [ ] Non-PM tidak melihat tombol approve/reject

### Project Labels
- [ ] Create project dengan label → tersimpan
- [ ] Create project tanpa label → label null (allowed)
- [ ] Edit project → label bisa diubah
- [ ] Label badge muncul di project card
- [ ] Filter by label berfungsi
- [ ] Combined filter (status + label) berfungsi
- [ ] Label colors sesuai (purple/blue/green)

---

## 📂 Files Modified

### Migrations
- `database/migrations/2025_10_16_235504_add_approval_to_businesses_table.php`
- `database/migrations/2025_10_16_235508_add_label_to_projects_table.php`

### Models
- `app/Models/Business.php` - Added approver relation, scopes, status helpers
- `app/Models/Project.php` - Added label methods, scope

### Controllers
- `app/Http/Controllers/BusinessController.php` - Approval logic, filters
- `app/Http/Controllers/ProjectController.php` - Label validation, filters

### Policies
- `app/Policies/BusinessPolicy.php` - Approve authorization

### Notifications
- `app/Notifications/BusinessNeedsApproval.php` - Database notification to PMs

### Routes
- `routes/web.php` - Added approve/reject routes

### Views
- `resources/views/businesses/index.blade.php` - Status filters, badges
- `resources/views/businesses/show.blade.php` - Approval UI, modal
- `resources/views/projects/create.blade.php` - Label field
- `resources/views/projects/edit.blade.php` - Label field
- `resources/views/projects/index.blade.php` - Label filters, badges

### Seeders
- `database/seeders/RolePermissionSeeder.php` - Added `business.approve` permission

---

## 🚀 Deployment Steps

1. **Run migrations:**
   ```bash
   php artisan migrate
   ```

2. **Seed permissions:**
   ```bash
   php artisan db:seed --class=RolePermissionSeeder
   ```

3. **Clear caches:**
   ```bash
   php artisan optimize:clear
   php artisan permission:cache-reset
   ```

---

## 💡 Usage Examples

### Create Business (Kewirausahaan)
1. Login sebagai kewirausahaan
2. Navigate to `/businesses`
3. Click "Buat Usaha Baru"
4. Fill form → Submit
5. Notifikasi muncul: "Usaha berhasil dibuat. Menunggu persetujuan PM."
6. PM mendapat notifikasi

### Approve Business (PM)
1. Login sebagai PM
2. Navigate to `/businesses`
3. Click "Menunggu Persetujuan" filter
4. Click pada business card
5. Click "Setujui" button → Status berubah hijau "Disetujui"

### Create Project with Label
1. Login sebagai PM
2. Navigate to `/projects` → "Buat Proyek Baru"
3. Fill name, description, etc.
4. Select label: "UMKM" atau "DIVISI" atau "Kegiatan"
5. Submit → Label badge muncul di project card

### Filter Projects by Label
1. Navigate to `/projects`
2. Click filter label: [UMKM] [DIVISI] [Kegiatan]
3. Combined with status: Select "Aktif" + "UMKM"
4. View filtered results

---

## 🔄 Future Enhancements

### Business Approval
- [ ] Email notification to PM
- [ ] Notification to creator when approved/rejected
- [ ] Business edit history (audit trail)
- [ ] Bulk approve/reject for PM
- [ ] Auto-reject after X days pending

### Project Labels
- [ ] Custom label colors via settings
- [ ] Label analytics (count by label)
- [ ] Label-based permissions
- [ ] Multi-label support (tags)
- [ ] Label icons

---

## 📝 Notes
- Business approval is **one-time decision** (cannot change after approved/rejected)
- Project labels are **optional** and can be changed anytime
- PM role has full control over business approval
- Creator cannot delete business after approval (only PM can)
