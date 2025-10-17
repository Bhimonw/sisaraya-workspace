# Business Reports Feature Documentation

## 📊 Overview
Fitur upload dan manajemen laporan usaha untuk kewirausahaan dan PM. Setiap usaha dapat memiliki multiple laporan dengan berbagai jenis.

---

## ✨ Features

### 1. **Upload Laporan**
- **Who can upload:** Kewirausahaan (creator) & PM
- **File types:** PDF, Word (.doc, .docx), Excel (.xls, .xlsx), Images (.jpg, .jpeg, .png)
- **Max file size:** 10 MB
- **Report types:**
  - 📈 Laporan Penjualan
  - 💰 Laporan Keuangan
  - ⚙️ Laporan Operasional
  - 📋 Laporan Lainnya

### 2. **List Laporan**
- Tampil di halaman detail usaha
- Sorted by latest (terbaru dulu)
- Show: Judul, jenis, tanggal, uploader, file size
- Color-coded badges per jenis laporan

### 3. **Download Laporan**
- **Who can download:** Semua yang bisa lihat detail usaha
- Direct download dengan original filename

### 4. **Delete Laporan**
- **Who can delete:** Uploader sendiri atau PM
- Confirmation prompt sebelum delete
- File otomatis terhapus dari storage

---

## 🗂️ Database Structure

### Table: `business_reports`
```sql
CREATE TABLE business_reports (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    business_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT NULL,
    file_path VARCHAR(255) NOT NULL,
    file_name VARCHAR(255) NOT NULL,
    file_type VARCHAR(255) NULL,
    file_size INT NULL,
    report_type ENUM('penjualan','keuangan','operasional','lainnya') DEFAULT 'lainnya',
    report_date DATE NOT NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    FOREIGN KEY (business_id) REFERENCES businesses(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

---

## 🏗️ Architecture

### Models

#### BusinessReport.php
```php
class BusinessReport extends Model
{
    protected $fillable = [
        'business_id', 'user_id', 'title', 'description',
        'file_path', 'file_name', 'file_type', 'file_size',
        'report_type', 'report_date'
    ];

    // Relationships
    public function business() // belongsTo Business
    public function uploader() // belongsTo User

    // Helpers
    public function getFormattedFileSizeAttribute() // "2.5 MB"
    public function getReportTypeColorAttribute() // "green", "blue", etc.
    public function getReportTypeLabelAttribute() // "Laporan Penjualan"
}
```

#### Business.php (updated)
```php
class Business extends Model
{
    // ... existing code ...
    
    public function reports() // hasMany BusinessReport
}
```

### Controller: BusinessReportController

**Methods:**
1. `store(Request, Business)` - Upload new report
2. `download(Business, BusinessReport)` - Download report file
3. `destroy(Business, BusinessReport)` - Delete report

**Authorization:**
- Upload: Only creator or PM
- Download: Anyone who can view business
- Delete: Only uploader or PM

### Routes
```php
// Business Reports
Route::post('businesses/{business}/reports', [BusinessReportController::class, 'store'])
    ->name('businesses.reports.store');
    
Route::get('businesses/{business}/reports/{report}/download', [BusinessReportController::class, 'download'])
    ->name('businesses.reports.download');
    
Route::delete('businesses/{business}/reports/{report}', [BusinessReportController::class, 'destroy'])
    ->name('businesses.reports.destroy');
```

---

## 🎨 UI Components

### Business Show Page Layout

```
┌─────────────────────────────────────────────────────────────┐
│                   Business Detail (2/3 width)               │
│  ┌───────────────────────────────────────────────────────┐ │
│  │ Business Info Card                                    │ │
│  │ - Name & Status Badge                                 │ │
│  │ - Description                                         │ │
│  │ - Creator & Date                                      │ │
│  │ - Approver Info (if approved/rejected)               │ │
│  │ - Project Link (if approved)                         │ │
│  │ - Rejection Reason (if rejected)                     │ │
│  └───────────────────────────────────────────────────────┘ │
│                                                             │
│  ┌───────────────────────────────────────────────────────┐ │
│  │ Reports List                                          │ │
│  │ ┌─────────────────────────────────────────────────┐  │ │
│  │ │ Report Card                                     │  │ │
│  │ │ - Title & Type Badge                            │  │ │
│  │ │ - Description                                   │  │ │
│  │ │ - Date, Uploader, File Size                     │  │ │
│  │ │ - Download & Delete Buttons                     │  │ │
│  │ └─────────────────────────────────────────────────┘  │ │
│  └───────────────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────────────┘

┌─────────────────────────┐
│ Sidebar (1/3 width)     │
│ ┌─────────────────────┐ │
│ │ Upload Report Form  │ │
│ │ - Title             │ │
│ │ - Report Type       │ │
│ │ - Date              │ │
│ │ - Description       │ │
│ │ - File Upload       │ │
│ │ [Upload Button]     │ │
│ └─────────────────────┘ │
└─────────────────────────┘
```

### Report Type Colors
```php
'penjualan' => 'green',    // Sales reports
'keuangan' => 'blue',       // Financial reports  
'operasional' => 'yellow',  // Operational reports
'lainnya' => 'gray',        // Others
```

---

## 🔐 Authorization

### Policy: BusinessPolicy (updated)

```php
public function update(User $user, Business $business): bool
{
    // Creator can update if still pending
    return $user->id === $business->created_by && $business->isPending();
}
```

**Authorization Matrix:**

| Action | Kewirausahaan (Creator) | PM | Others |
|--------|-------------------------|----|----- ---|
| Upload Report | ✅ (any status) | ✅ (any status) | ❌ |
| View Reports | ✅ | ✅ | ✅ (if can view business) |
| Download Report | ✅ | ✅ | ✅ (if can view business) |
| Delete Own Report | ✅ | ✅ | ❌ |
| Delete Others' Report | ❌ | ✅ | ❌ |

---

## 📝 Usage Examples

### Upload Report (Kewirausahaan)
1. Login sebagai kafilah
2. Buka detail usaha
3. Isi form di sidebar:
   - Judul: "Laporan Penjualan September 2025"
   - Jenis: Laporan Penjualan
   - Tanggal: 2025-09-30
   - File: Upload PDF/Excel
4. Klik "Upload Laporan"
5. File tersimpan di `storage/app/public/business-reports/`
6. Record tersimpan di database

### View & Download (PM)
1. Login sebagai bhimo (PM)
2. Navigate ke Manajemen Usaha
3. Klik usaha yang ingin dilihat
4. Scroll ke section "Laporan Usaha"
5. Klik "Download" untuk download file
6. File downloaded dengan nama asli

### Delete Report (PM)
1. Di halaman detail usaha
2. Klik tombol delete (🗑️) pada laporan
3. Confirm deletion
4. File & record terhapus
5. Success message ditampilkan

---

## 🧪 Testing Checklist

### Test Scenarios

- [ ] **Upload Report - Kewirausahaan**
  - [ ] Upload PDF laporan penjualan
  - [ ] Upload Excel laporan keuangan
  - [ ] Upload Word laporan operasional
  - [ ] Upload image laporan
  - [ ] Try upload file > 10MB (should fail)
  - [ ] Try upload unsupported format (should fail)

- [ ] **Upload Report - PM**
  - [ ] PM can upload report to any business
  - [ ] PM can upload to approved business
  - [ ] PM can upload to rejected business

- [ ] **View Reports**
  - [ ] Reports sorted by latest
  - [ ] Correct badges for each type
  - [ ] File size formatted correctly
  - [ ] Uploader name displayed
  - [ ] Empty state when no reports

- [ ] **Download Reports**
  - [ ] Download PDF works
  - [ ] Download Excel works
  - [ ] Download Word works
  - [ ] Download image works
  - [ ] Original filename preserved
  - [ ] 404 if file doesn't exist

- [ ] **Delete Reports**
  - [ ] Kewirausahaan can delete own reports
  - [ ] Kewirausahaan cannot delete others' reports
  - [ ] PM can delete any report
  - [ ] File removed from storage
  - [ ] Record removed from database
  - [ ] Confirmation prompt works

- [ ] **Authorization**
  - [ ] Non-creator, non-PM cannot upload
  - [ ] Delete button only shows for authorized users
  - [ ] 403 error for unauthorized actions

---

## 🚀 Deployment Notes

### Storage Setup
Pastikan symbolic link sudah dibuat:
```bash
php artisan storage:link
```

### File Permissions
Folder `storage/app/public/business-reports/` harus writable:
```bash
chmod -R 775 storage/app/public/business-reports
```

### .env Configuration
```env
FILESYSTEM_DISK=public
```

---

## 🐛 Known Issues & Limitations

1. **File Size Limit:** 10MB max per file
2. **Concurrent Uploads:** No progress bar for large files
3. **No Preview:** Tidak ada preview untuk PDF/images
4. **No Versioning:** Tidak ada version control untuk laporan yang sama

---

## 🔮 Future Enhancements

- [ ] Add file preview (PDF, images)
- [ ] Add progress bar for uploads
- [ ] Add report categories/tags
- [ ] Add search/filter reports
- [ ] Add report approval workflow
- [ ] Add email notification on upload
- [ ] Add report templates
- [ ] Add bulk upload
- [ ] Add export reports to ZIP
- [ ] Add report analytics/stats

---

## 📞 Support

**Files Modified:**
- `app/Models/BusinessReport.php` - New model
- `app/Models/Business.php` - Added reports() relationship
- `app/Http/Controllers/BusinessReportController.php` - New controller
- `database/migrations/2025_10_17_005808_create_business_reports_table.php` - Migration
- `resources/views/businesses/show.blade.php` - Updated UI
- `resources/views/layouts/_menu.blade.php` - Added PM menu
- `routes/web.php` - Added report routes

**Related Documentation:**
- `docs/BUSINESS_APPROVAL_AND_PROJECT_LABELS.md`
- `docs/BUSINESS_TO_PROJECT_WORKFLOW.md`
- `docs/TROUBLESHOOTING_PM_ACCESS.md`

---

**Last Updated:** October 17, 2025  
**Status:** ✅ IMPLEMENTED  
**Version:** 1.0.0
