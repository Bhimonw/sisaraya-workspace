# ⚡ Quick Fix Checklist - Error Handling & Security

**Priority:** CRITICAL untuk Production Deployment  
**Estimated Time:** 2-3 hari kerja

---

## 🔴 CRITICAL FIXES (Harus Segera)

### 1. File Upload Error Handling

**File:** `app/Http/Controllers/DocumentController.php`

```php
public function store(Request $request)
{
    try {
        $data = $request->validate([
            'file' => [
                'required',
                'file',
                'max:10240',
                'mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png', // ❌ Remove zip,rar
            ],
            'description' => 'nullable|string|max:5000',
            'is_confidential' => 'boolean',
            'project_id' => 'nullable|exists:projects,id',
        ]);

        if ($request->boolean('is_confidential') && !auth()->user()->hasAnyRole(['sekretaris', 'hr'])) {
            abort(403, 'Tidak memiliki akses untuk membuat dokumen rahasia');
        }

        $file = $request->file('file');
        
        // ✅ Add: Verify file is valid
        if (!$file->isValid()) {
            return back()->withErrors(['file' => 'File upload gagal. Silakan coba lagi.'])->withInput();
        }
        
        // ✅ Add: Sanitize filename
        $filename = \Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
        $extension = $file->getClientOriginalExtension();
        $newFilename = $filename . '_' . time() . '.' . $extension;
        
        $path = $file->storeAs('documents', $newFilename, 'public');
        
        $doc = Document::create([
            'user_id' => $request->user()->id,
            'project_id' => $data['project_id'] ?? null,
            'path' => $path,
            'name' => $file->getClientOriginalName(),
            'description' => $data['description'] ?? null,
            'is_confidential' => $request->boolean('is_confidential'),
        ]);

        $type = $doc->is_confidential ? 'confidential' : 'public';
        return redirect()->route('documents.index', ['type' => $type])
            ->with('success', 'Dokumen berhasil diupload');
            
    } catch (\Exception $e) {
        \Log::error('Document upload failed: ' . $e->getMessage(), [
            'user_id' => auth()->id(),
            'file' => $request->file('file')?->getClientOriginalName(),
        ]);
        
        return back()->withErrors(['file' => 'Terjadi kesalahan saat upload dokumen. Silakan coba lagi.'])->withInput();
    }
}
```

**Same fix needed in:** `app/Http/Controllers/RabController.php`

---

### 2. Transaction untuk User Creation

**File:** `app/Http/Controllers/Admin/UserController.php`

```php
public function store(Request $request)
{
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

    // Validasi: Guest tidak bisa digabung dengan role lainnya
    if (in_array('guest', $data['roles'] ?? [])) {
        if (count($data['roles']) > 1) {
            return back()->withErrors(['roles' => 'Role Guest tidak dapat digabung dengan role lainnya.'])->withInput();
        }
        
        if (empty($data['projects'])) {
            return back()->withErrors(['projects' => 'User dengan role Guest harus memilih minimal satu proyek.'])->withInput();
        }
    }

    // ✅ Add: Wrap in transaction with error handling
    try {
        DB::beginTransaction();
        
        $user = User::create([
            'name' => $data['name'],
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        if (!empty($data['roles'])) {
            $user->syncRoles($data['roles']);
        }

        if (in_array('guest', $data['roles'] ?? []) && !empty($data['projects'])) {
            $user->projects()->attach($data['projects']);
        }
        
        DB::commit();
        
        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil dibuat');
            
    } catch (\Exception $e) {
        DB::rollBack();
        
        \Log::error('User creation failed: ' . $e->getMessage(), [
            'admin_id' => auth()->id(),
            'username' => $data['username'],
        ]);
        
        return back()->withErrors(['error' => 'Gagal membuat user. Silakan coba lagi.'])->withInput();
    }
}
```

**Same pattern for:** `update()` method

---

### 3. Transaction untuk Bulk Ticket Creation

**File:** `app/Http/Controllers/TicketController.php`

```php
public function store(Request $request, Project $project = null)
{
    // ... validation code ...

    $ticketsCreated = 0;

    // ✅ Add: Wrap multi-ticket creation in transaction
    try {
        DB::beginTransaction();
        
        if (!empty($targetUserIds)) {
            foreach ($targetUserIds as $userId) {
                $ticket = Ticket::create([
                    'title' => $data['title'],
                    'description' => $data['description'] ?? null,
                    'status' => 'todo',
                    'context' => 'umum',
                    'priority' => $data['priority'] ?? 'medium',
                    'weight' => $data['weight'] ?? 5,
                    'target_role' => null,
                    'target_user_id' => $userId,
                    'due_date' => $data['due_date'] ?? null,
                    'creator_id' => $request->user()->id,
                    'project_id' => null,
                ]);

                $ticket->load(['project', 'projectEvent.project']);

                // Notification in separate try-catch (don't fail transaction if notification fails)
                try {
                    $targetUser = User::find($userId);
                    if ($targetUser) {
                        $targetUser->notify(new TicketAssignedNotification($ticket));
                    }
                } catch (\Exception $e) {
                    \Log::warning('Failed to send ticket notification', [
                        'ticket_id' => $ticket->id,
                        'user_id' => $userId,
                        'error' => $e->getMessage(),
                    ]);
                    // Continue anyway - notification failure shouldn't stop ticket creation
                }

                $ticketsCreated++;
            }
        } else {
            // Single ticket creation
            $ticket = Ticket::create([
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'status' => 'todo',
                'context' => 'umum',
                'priority' => $data['priority'] ?? 'medium',
                'weight' => $data['weight'] ?? 5,
                'target_role' => $targetRole,
                'target_user_id' => null,
                'due_date' => $data['due_date'] ?? null,
                'creator_id' => $request->user()->id,
                'project_id' => null,
            ]);
            
            $ticketsCreated = 1;
        }
        
        DB::commit();
        
        return redirect()->route('tickets.index')
            ->with('success', $ticketsCreated . ' tiket berhasil dibuat');
            
    } catch (\Exception $e) {
        DB::rollBack();
        
        \Log::error('Ticket creation failed', [
            'creator_id' => auth()->id(),
            'title' => $data['title'] ?? null,
            'error' => $e->getMessage(),
        ]);
        
        return back()->withErrors(['error' => 'Gagal membuat tiket. Silakan coba lagi.'])->withInput();
    }
}
```

---

### 4. Transaction Error Handling untuk Business Approval

**File:** `app/Http/Controllers/BusinessController.php`

```php
public function approve(Business $business)
{
    $this->authorize('approve', $business);
    
    // ✅ Add: Error handling to existing transaction
    try {
        DB::beginTransaction();
        
        $project = Project::create([
            'name' => $business->name,
            'description' => $business->description,
            'owner_id' => auth()->id(),
            'status' => 'active',
            'label' => 'UMKM',
            'is_public' => true,
        ]);
        
        $project->members()->attach($business->created_by, [
            'role' => 'admin',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        $business->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'rejection_reason' => null,
            'project_id' => $project->id,
        ]);
        
        DB::commit();
        
        return redirect()->route('businesses.show', $business)
            ->with('success', 'Usaha berhasil disetujui dan proyek telah dibuat!');
            
    } catch (\Exception $e) {
        DB::rollBack();
        
        \Log::error('Business approval failed', [
            'business_id' => $business->id,
            'approver_id' => auth()->id(),
            'error' => $e->getMessage(),
        ]);
        
        return back()->with('error', 'Gagal menyetujui usaha. Silakan coba lagi.');
    }
}
```

---

### 5. Rate Limiting untuk Sensitive Operations

**File:** `routes/web.php`

Add throttle middleware:

```php
// Existing route groups...

// ✅ Add: Rate limiting for file uploads
Route::middleware(['auth', 'throttle:10,1'])->group(function () {
    Route::post('documents', [DocumentController::class, 'store'])->name('documents.store');
    Route::post('rabs', [RabController::class, 'store'])->name('rabs.store');
    Route::put('rabs/{rab}', [RabController::class, 'update'])->name('rabs.update');
});

// ✅ Add: Rate limiting for ticket/vote creation
Route::middleware(['auth', 'throttle:20,1'])->group(function () {
    Route::post('tickets', [TicketController::class, 'store'])->name('tickets.store');
    Route::post('votes', [VoteController::class, 'store'])->name('votes.store');
});
```

---

### 6. Query Parameter Validation

**File:** `app/Http/Controllers/ProjectController.php`

```php
public function index(Request $request)
{
    // ✅ Add: Validate query parameters
    $validated = $request->validate([
        'status' => 'nullable|in:all,planning,active,on_hold,completed,blackout',
        'label' => 'nullable|in:UMKM,DIVISI,Kegiatan',
    ]);
    
    $status = $validated['status'] ?? 'all';
    $label = $validated['label'] ?? null;
    
    // ... rest of code ...
}
```

**Same fix needed in:**
- `RabController::index()` - validate `status` parameter
- `DocumentController::index()` - validate `type` parameter
- `VoteController::index()` - validate filter parameters

---

## 🟡 IMPORTANT FIXES (Dalam Minggu Ini)

### 7. Wrap All Notifications in Try-Catch

Search and replace pattern:

```php
// ❌ BEFORE:
$user->notify(new SomeNotification($data));

// ✅ AFTER:
try {
    $user->notify(new SomeNotification($data));
} catch (\Exception $e) {
    \Log::warning('Notification failed', [
        'user_id' => $user->id,
        'notification' => 'SomeNotification',
        'error' => $e->getMessage(),
    ]);
    // Continue - notification failure shouldn't break the operation
}
```

**Files to update:**
- `TicketController.php` (multiple locations)
- `BusinessController.php`
- `RabController.php`
- `RoleChangeRequestController.php`

---

### 8. Standardize Error Messages

Replace all English error messages with Indonesian:

```php
// ❌ BEFORE:
abort(403, 'Only PM can create general tickets');

// ✅ AFTER:
abort(403, 'Hanya PM yang dapat membuat tiket umum');
```

**Quick find & replace:**
- "Only PM" → "Hanya PM"
- "can create" → "dapat membuat"
- "can view" → "dapat melihat"
- "can manage" → "dapat mengelola"

---

## 🔍 TESTING CHECKLIST

After implementing fixes, test:

### File Upload Tests
```powershell
# Test 1: Upload file > 10MB
# Test 2: Upload .exe file (should be rejected)
# Test 3: Upload with malicious filename (../../../etc/passwd)
# Test 4: Upload 20 files rapidly (test rate limiting)
```

### Transaction Tests
```powershell
# Test 1: Create user with invalid role (should rollback)
# Test 2: Create multiple tickets, fail midway (should rollback all)
# Test 3: Approve business with invalid project data (should rollback)
```

### Rate Limiting Tests
```powershell
# Test 1: Upload 11 files in 1 minute (should get 429 error)
# Test 2: Create 21 tickets in 1 minute (should get 429 error)
```

### Query Parameter Tests
```powershell
# Test 1: /projects?status=invalid_status (should show error)
# Test 2: /documents?type=<script>alert('xss')</script> (should reject)
```

---

## 📋 IMPLEMENTATION STEPS

1. **Day 1 Morning:** Fix #1-2 (File uploads + User creation)
2. **Day 1 Afternoon:** Fix #3-4 (Tickets + Business approval)
3. **Day 2 Morning:** Fix #5-6 (Rate limiting + Query validation)
4. **Day 2 Afternoon:** Fix #7 (Wrap notifications)
5. **Day 3:** Testing + Fix #8 (Error messages)

---

## 🚀 DEPLOYMENT CHECKLIST

Before pushing to production:

```powershell
# 1. Run tests
php artisan test

# 2. Check for syntax errors
composer validate

# 3. Clear and rebuild cache
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# 4. Run migrations (if any)
php artisan migrate --force

# 5. Rebuild frontend assets
npm run build

# 6. Test key workflows manually:
# - Upload dokumen
# - Buat user baru (sebagai HR)
# - Buat tiket bulk
# - Approve business
```

---

## 📝 NOTES

- **Jangan skip Critical fixes** - ini bisa menyebabkan data loss di production
- **Test di local dulu** sebelum deploy ke production
- **Backup database** sebelum deploy
- **Monitor error logs** setelah deploy: `tail -f storage/logs/laravel.log`

---

**Estimasi Total Waktu:** 2-3 hari kerja (16-24 jam)  
**Risk Level Setelah Fix:** LOW ✅

---

*Created: 21 Oktober 2025*  
*Last Updated: 21 Oktober 2025*
