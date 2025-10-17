# 🧪 Quick Testing Guide - Business Reports Feature

## Pre-requisites
✅ Server running: `php artisan serve`
✅ Database migrated: `php artisan migrate`
✅ Storage linked: `php artisan storage:link`
✅ Seeders run: `php artisan db:seed`

---

## Test 1: PM Access Menu "Manajemen Usaha"

### Steps:
1. **Login sebagai PM (Bhimo)**
   ```
   URL: http://127.0.0.1:8000/login
   Username: bhimo
   Password: password
   ```

2. **Check Sidebar**
   - Expand "Ruang Management"
   - ✅ Should see "Manajemen Usaha" menu

3. **Click "Manajemen Usaha"**
   - Should redirect to `/businesses`
   - ✅ Can see list of businesses
   - ✅ Can see filter tabs

---

## Test 2: Kewirausahaan Upload Laporan

### Steps:
1. **Login sebagai Kewirausahaan (Kafilah)**
   ```
   Username: kafilah
   Password: password
   ```

2. **Navigate to Businesses**
   - Click "Usaha Aktif" di sidebar
   - Select a business (or create one if none exists)

3. **Upload Laporan**
   - Scroll to sidebar "Upload Laporan"
   - Fill form:
     - **Judul:** "Laporan Penjualan September 2025"
     - **Jenis:** Laporan Penjualan
     - **Tanggal:** 2025-09-30
     - **File:** Upload sample PDF
   - Click "Upload Laporan"

4. **Verify Upload**
   - ✅ Success message appears
   - ✅ Report appears in list
   - ✅ Green badge shows "Laporan Penjualan"
   - ✅ File size displayed correctly
   - ✅ Your name as uploader

---

## Test 3: PM Upload Laporan

### Steps:
1. **Login sebagai PM (Bhimo)**

2. **Navigate to Business Detail**
   - Click "Manajemen Usaha"
   - Click on any business

3. **Upload Different Report Type**
   - Fill form:
     - **Judul:** "Laporan Keuangan Q3 2025"
     - **Jenis:** Laporan Keuangan
     - **Tanggal:** 2025-09-30
     - **Deskripsi:** "Laporan keuangan kuartal 3"
     - **File:** Upload Excel file
   - Click "Upload Laporan"

4. **Verify Upload**
   - ✅ Blue badge shows "Laporan Keuangan"
   - ✅ Description displayed
   - ✅ PM name as uploader

---

## Test 4: Download Laporan

### Steps:
1. **On Business Detail Page**
   - Find uploaded report in list

2. **Click Download Button**
   - ✅ File downloads with original name
   - ✅ File opens correctly

3. **Try from Different User**
   - Logout → Login as different user
   - Navigate to same business
   - ✅ Can still download (if has view permission)

---

## Test 5: Delete Laporan

### Test 5a: Delete Own Report
1. **As Kewirausahaan (Uploader)**
   - Go to business with your uploaded report
   - Click delete button (🗑️)
   - ✅ Confirmation prompt appears
   - Confirm delete
   - ✅ Success message
   - ✅ Report removed from list

### Test 5b: PM Delete Any Report
1. **As PM**
   - Go to any business
   - Try to delete report uploaded by kewirausahaan
   - ✅ Delete button visible
   - ✅ Can delete successfully

### Test 5c: Unauthorized Delete
1. **As Regular User (not uploader, not PM)**
   - Go to business detail
   - ✅ Delete button NOT visible for others' reports

---

## Test 6: File Upload Validation

### Test Invalid Files:
1. **Try Upload File > 10MB**
   - ✅ Error: File too large

2. **Try Upload Unsupported Format (.zip, .exe)**
   - ✅ Error: Invalid file type

3. **Try Upload Without Required Fields**
   - Leave title empty
   - ✅ Validation error

---

## Test 7: Authorization

### Test Unauthorized Upload:
1. **Login as user WITHOUT kewirausahaan or PM role**
   - Try to access business detail
   - ✅ Upload form NOT visible

2. **Try Direct POST**
   - Use browser dev tools or Postman
   - POST to `/businesses/{id}/reports`
   - ✅ 403 Forbidden error

---

## Test 8: UI & Responsive

### Desktop View:
1. **Business Detail Page**
   - ✅ 2-column layout (2/3 main, 1/3 sidebar)
   - ✅ Upload form sticky on scroll
   - ✅ Reports list shows all details

### Mobile View:
1. **Resize browser to mobile width**
   - ✅ Single column layout
   - ✅ Upload form below reports
   - ✅ Cards stack properly

---

## Test 9: Edge Cases

### Empty Reports:
1. **Business with no reports**
   - ✅ Shows empty state with icon
   - ✅ Message: "Belum ada laporan"

### Multiple Reports:
1. **Upload 5+ reports**
   - ✅ All display correctly
   - ✅ Sorted by latest
   - ✅ No performance issues

### Long Filenames:
1. **Upload file with very long name**
   - ✅ Truncated properly in UI
   - ✅ Full name on download

---

## Test 10: Integration with Business Workflow

### Upload After Approval:
1. **Create business as kewirausahaan**
2. **PM approves business → project created**
3. **Upload laporan to approved business**
   - ✅ Can upload
   - ✅ Project link still visible
   - ✅ Both sections work together

### Upload on Rejected Business:
1. **Rejected business**
2. **Try upload laporan**
   - ✅ Can still upload (for documentation)
   - ✅ Rejection reason visible

---

## Expected Results Summary

| Test | Expected Result | Status |
|------|----------------|--------|
| PM sees menu | ✅ Menu visible in sidebar | ⬜ |
| Kewirausahaan upload | ✅ Report uploaded successfully | ⬜ |
| PM upload | ✅ Report uploaded successfully | ⬜ |
| Download report | ✅ File downloads correctly | ⬜ |
| Delete own report | ✅ Can delete | ⬜ |
| PM delete any | ✅ Can delete any report | ⬜ |
| Upload > 10MB | ❌ Validation error | ⬜ |
| Upload invalid type | ❌ Validation error | ⬜ |
| Unauthorized upload | ❌ Form not visible | ⬜ |
| Empty state | ✅ Shows message | ⬜ |

---

## Common Issues & Solutions

### Issue 1: "Storage link not found"
**Solution:**
```bash
php artisan storage:link
```

### Issue 2: "Permission denied" on upload
**Solution:**
```bash
chmod -R 775 storage/app/public
```

### Issue 3: "File not found" on download
**Solution:**
- Check if file exists in `storage/app/public/business-reports/`
- Check database `file_path` column

### Issue 4: Upload form not showing
**Solution:**
- Check if user is creator or PM
- Check `@canany(['update'], $business)` directive
- Verify BusinessPolicy

---

## Quick Commands

```bash
# Restart server
php artisan serve

# Check storage link
ls -la public/storage

# Check uploaded files
ls storage/app/public/business-reports/

# Clear cache
php artisan cache:clear
php artisan view:clear

# Check routes
php artisan route:list | grep businesses

# Check permissions
php artisan tinker --execute="echo App\Models\User::where('username', 'bhimo')->first()->hasPermissionTo('business.view') ? 'OK' : 'NO'"
```

---

## Success Criteria

All tests pass when:
- [x] PM can access "Manajemen Usaha" menu
- [x] Kewirausahaan can upload reports
- [x] PM can upload reports to any business
- [x] Anyone can download reports
- [x] Uploader can delete own reports
- [x] PM can delete any reports
- [x] File validation works correctly
- [x] UI is responsive and user-friendly
- [x] Authorization properly enforced

---

**Testing Status:** Ready for manual testing
**Estimated Time:** 15-20 minutes for complete test suite
**Last Updated:** October 17, 2025
