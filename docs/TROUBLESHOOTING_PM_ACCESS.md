# 🔧 Troubleshooting: PM Login & Business Access

**Issue:** PM (bhimo) tidak bisa masuk atau mengakses halaman businesses

**Root Cause:** PM role tidak memiliki permission `business.view`

**Status:** ✅ **FIXED**

---

## 📊 Problem Analysis

### What Happened
1. BusinessController memiliki middleware:
   ```php
   $this->middleware('permission:business.view')->only(['index','show']);
   ```

2. PM role hanya punya permission `business.approve` tapi **tidak punya** `business.view`

3. Ketika PM coba akses `/businesses`, Laravel middleware memblokir akses karena missing permission

### Verification Results

**User Status:**
- ✅ User `bhimo` exists in database
- ✅ Has role `pm`
- ✅ Has role `sekretaris`
- ✅ Password hash valid (password: `password`)

**Permission Status (BEFORE FIX):**
- ❌ PM missing `business.view`
- ✅ PM has `business.approve`
- ✅ PM has other project permissions

**Permission Status (AFTER FIX):**
- ✅ PM has `business.view`
- ✅ PM has `business.approve`
- ✅ Total 9 permissions for PM role

---

## 🛠️ Solution Applied

### Step 1: Update RolePermissionSeeder

**File:** `database/seeders/RolePermissionSeeder.php`

**Change:**
```php
// BEFORE
Role::where('name', 'pm')->first()?->givePermissionTo([
    'projects.create', 
    'projects.update', 
    'projects.view', 
    'projects.manage_members', 
    'tickets.create', 
    'tickets.update_status', 
    'documents.upload', 
    'business.approve'
]);

// AFTER
Role::where('name', 'pm')->first()?->givePermissionTo([
    'projects.create', 
    'projects.update', 
    'projects.view', 
    'projects.manage_members', 
    'tickets.create', 
    'tickets.update_status', 
    'documents.upload', 
    'business.approve', 
    'business.view'  // ✅ ADDED
]);
```

### Step 2: Re-seed Permissions

```bash
php artisan db:seed --class=RolePermissionSeeder
```

### Step 3: Verify

```bash
# Check PM permissions
php artisan tinker --execute="echo App\Models\User::where('username', 'bhimo')->first()->hasPermissionTo('business.view') ? '✅ PM has business.view' : '❌ PM missing business.view'"

# Output: ✅ PM has business.view
```

---

## 🧪 Testing Instructions

### Method 1: Quick Test (Recommended)

1. **Open test page:**
   ```
   http://127.0.0.1:8000/test-login.html
   ```

2. **Click credentials to copy:**
   - Username: `bhimo`
   - Password: `password`

3. **Click "Go to Login Page"**

4. **Login and test access**

### Method 2: Manual Test

1. **Login as PM:**
   ```
   URL: http://127.0.0.1:8000/login
   Username: bhimo
   Password: password
   ```

2. **Access businesses page:**
   ```
   URL: http://127.0.0.1:8000/businesses
   ```

3. **Expected result:**
   - ✅ Can see businesses list
   - ✅ Can see filter tabs (Semua, Menunggu Persetujuan, Disetujui, Ditolak)
   - ✅ Can click on business to see details
   - ✅ Can see "Setujui" and "Tolak" buttons for pending businesses

### Method 3: Create Test Business

1. **Login as kewirausahaan:**
   ```
   Username: kafilah
   Password: password
   ```

2. **Create new business:**
   - Navigate to `/businesses`
   - Click "Buat Usaha Baru"
   - Fill in name and description
   - Submit

3. **Logout and login as PM (bhimo)**

4. **Check notification:**
   - Should see notification about new business
   - Click notification → redirects to business detail

5. **Approve business:**
   - Click "Setujui" button
   - Should see success message
   - Project should be auto-created
   - Green box "Proyek Terkait" should appear

---

## 🔍 Debug Commands

### Check User & Roles
```bash
php artisan tinker --execute="echo App\Models\User::where('username', 'bhimo')->with('roles')->first()"
```

### Check Permissions
```bash
php artisan tinker --execute="echo implode(', ', Spatie\Permission\Models\Role::where('name', 'pm')->first()->permissions->pluck('name')->toArray())"
```

### Check Password
```bash
php artisan tinker --execute="echo Hash::check('password', App\Models\User::where('username', 'bhimo')->first()->password) ? 'Valid' : 'Invalid'"
```

### List All Users
```bash
php artisan tinker --execute="echo App\Models\User::count() . ' users'"
```

---

## 🚨 Common Issues & Solutions

### Issue 1: "403 Forbidden" on /businesses
**Cause:** Missing `business.view` permission  
**Solution:** Run `php artisan db:seed --class=RolePermissionSeeder`

### Issue 2: "419 CSRF Token Mismatch"
**Cause:** Application key changed or browser cache  
**Solution:**
```bash
php artisan key:generate
# Then hard refresh browser (Ctrl+F5)
```

### Issue 3: "Login credentials incorrect"
**Cause:** Password hash mismatch  
**Solution:**
```bash
# Reset password for bhimo
php artisan tinker --execute="App\Models\User::where('username', 'bhimo')->first()->update(['password' => Hash::make('password')])"
```

### Issue 4: "Redirected to welcome page"
**Cause:** Not authenticated or middleware issue  
**Solution:**
- Check if logged in: `/dashboard` should work
- Clear browser cookies
- Check `routes/web.php` middleware

### Issue 5: "Approve button not showing"
**Cause:** Business status not pending or missing policy  
**Solution:**
- Check business status: must be "pending"
- Check BusinessPolicy authorize method
- Verify PM has `business.approve` permission

---

## 📝 Permissions Matrix

| Permission | PM | Kewirausahaan | HR | Description |
|------------|:--:|:-------------:|:--:|-------------|
| `business.view` | ✅ | ✅ | ❌ | View businesses list & detail |
| `business.create` | ❌ | ✅ | ❌ | Create new business |
| `business.approve` | ✅ | ❌ | ❌ | Approve/reject businesses |
| `business.manage_talent` | ❌ | ✅ | ❌ | Manage business talent |
| `business.upload_reports` | ❌ | ✅ | ❌ | Upload business reports |

---

## ✅ Verification Checklist

Before testing, ensure:

- [ ] Server is running (`php artisan serve`)
- [ ] Database migrated (`php artisan migrate`)
- [ ] Permissions seeded (`php artisan db:seed --class=RolePermissionSeeder`)
- [ ] Users seeded (`php artisan db:seed --class=SisarayaMembersSeeder`)
- [ ] Browser cache cleared
- [ ] Application key generated (`php artisan key:generate`)

After fix:

- [ ] PM can login successfully
- [ ] PM can access `/businesses`
- [ ] PM can see all businesses
- [ ] PM can see approve/reject buttons on pending businesses
- [ ] PM can approve business
- [ ] Project auto-created when approved
- [ ] Kewirausahaan becomes admin member of project
- [ ] PM is owner of project

---

## 🎯 Expected Workflow

### Complete Flow: Kewirausahaan → PM → Project

```
┌─────────────────┐
│ 1. Kewirausahaan │
│    creates       │
│    business      │
└────────┬────────┘
         │ status: pending
         ▼
┌─────────────────┐
│ 2. System sends │
│    notification │
│    to all PMs   │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│ 3. PM receives  │
│    notification │
│    & reviews    │
└────────┬────────┘
         │
    ┌────┴────┐
    │         │
  APPROVE   REJECT
    │         │
    ▼         ▼
┌────────┐  ┌────────┐
│ 4a. PM │  │ 4b. PM │
│ clicks │  │ clicks │
│ approve│  │ reject │
└───┬────┘  └───┬────┘
    │           │
    ▼           ▼
┌──────────┐  ┌──────────┐
│ 5a. AUTO │  │ 5b. Add  │
│  CREATE  │  │ rejection│
│ PROJECT  │  │  reason  │
│          │  │          │
│ - PM as  │  │ Business │
│   owner  │  │ status:  │
│ - Kewira │  │ rejected │
│   as     │  └──────────┘
│   admin  │
│ - Label  │
│   UMKM   │
│ - Status │
│   active │
└──────────┘
```

---

## 📚 Related Documentation

- Main docs: `docs/BUSINESS_APPROVAL_AND_PROJECT_LABELS.md`
- Workflow: `docs/BUSINESS_TO_PROJECT_WORKFLOW.md`
- Tests: `tests/Feature/BusinessApprovalTest.php`
- Policy: `app/Policies/BusinessPolicy.php`
- Controller: `app/Http/Controllers/BusinessController.php`

---

## 🎉 Success Indicators

You'll know it's working when:

1. ✅ Login as bhimo succeeds
2. ✅ Can access http://127.0.0.1:8000/businesses
3. ✅ See list of businesses with status badges
4. ✅ Can filter by status (4 tabs working)
5. ✅ Pending businesses show approve/reject buttons
6. ✅ Clicking approve creates project automatically
7. ✅ Green "Proyek Terkait" box appears after approval
8. ✅ Can click "Buka Proyek" to view the auto-created project
9. ✅ Kewirausahaan appears as admin member in project
10. ✅ PM has full owner access to project

---

**Last Updated:** October 17, 2025  
**Status:** ✅ RESOLVED  
**Fix Applied:** Added `business.view` permission to PM role
