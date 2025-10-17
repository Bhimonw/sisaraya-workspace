# Business to Project Auto-Creation Workflow

## 🎯 Overview
Ketika PM menyetujui usaha yang diajukan oleh kewirausahaan, sistem **otomatis membuat project baru** dengan PM sebagai owner dan kewirausahaan sebagai admin member.

---

## 📊 Complete Workflow Diagram

```
Kewirausahaan                    PM                         System
     |                           |                             |
     |-- Buat Usaha Baru ------->|                             |
     |   (status: pending)       |                             |
     |                           |<---- Terima Notifikasi -----|
     |                           |                             |
     |                           |-- Review Usaha ------------>|
     |                           |                             |
     |                           |-- Klik "Setujui" ---------->|
     |                           |                             |
     |                           |                    [CREATE PROJECT]
     |                           |                    - Owner: PM
     |                           |                    - Member: Kewirausahaan (admin)
     |                           |                    - Label: UMKM
     |                           |                    - Status: active
     |                           |                             |
     |<-- Notifikasi: Approved --|<---- Success Response ------|
     |                           |                             |
     |                           |-- Akses Project ----------->|
     |                           |   (Full Access)             |
     |                           |                             |
     |-- Akses Project ----------|-------------------------->  |
     |   (Admin Access)          |                             |
```

---

## 🔧 Technical Implementation

### Database Changes

#### businesses table
```sql
ALTER TABLE businesses ADD COLUMN project_id BIGINT UNSIGNED NULL;
ALTER TABLE businesses ADD FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE SET NULL;
```

#### Migration: `2025_10_17_003149_add_project_id_to_businesses_table.php`

### Model Updates

#### Business.php
```php
protected $fillable = [..., 'project_id'];

public function project()
{
    return $this->belongsTo(\App\Models\Project::class, 'project_id');
}
```

### Controller Logic

#### BusinessController@approve
```php
public function approve(Business $business)
{
    DB::transaction(function () use ($business) {
        // 1. Create project
        $project = Project::create([
            'name' => $business->name,
            'description' => $business->description,
            'owner_id' => auth()->id(), // PM
            'status' => 'active',
            'label' => 'UMKM',
            'is_public' => true,
        ]);
        
        // 2. Add kewirausahaan as admin member
        $project->members()->attach($business->created_by, [
            'role' => 'admin',
        ]);
        
        // 3. Update business
        $business->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'project_id' => $project->id,
        ]);
    });
}
```

---

## 👥 Role & Access Matrix

| Role | Business | Project | Access Level |
|------|----------|---------|--------------|
| **Kewirausahaan** | Creator | Admin Member | - Create business<br>- View own businesses<br>- **Admin access to project**<br>- Can manage members<br>- Can create tickets<br>- Can view all data |
| **PM** | Approver | Owner | - Approve/reject businesses<br>- **Full project control**<br>- Can edit project settings<br>- Can add/remove members<br>- Can delete project<br>- Can close project |

---

## 🎨 UI Features

### Business Show Page - Approved State

```blade
@if($business->isApproved() && $business->project)
    <div class="bg-green-50 border border-green-200 rounded p-4">
        <h4>Proyek Terkait</h4>
        <p>Usaha ini telah disetujui dan proyek telah dibuat.</p>
        <a href="{{ route('projects.show', $business->project) }}">
            Buka Proyek →
        </a>
    </div>
@endif
```

### Business Index - Project Link Badge

```blade
@if($business->project_id)
    <a href="{{ route('projects.show', $business->project_id) }}"
       class="badge badge-blue">
        📁 Proyek
    </a>
@endif
```

---

## 🧪 Testing Scenarios

### Test 1: PM Approve Creates Project
```php
/** @test */
public function pm_approve_creates_project_automatically()
{
    $pm = User::factory()->create()->assignRole('pm');
    $kewirausahaan = User::factory()->create();
    
    $business = Business::factory()->create([
        'status' => 'pending',
        'created_by' => $kewirausahaan->id,
    ]);
    
    $this->actingAs($pm)->post(route('businesses.approve', $business));
    
    $business->refresh();
    
    // Assert project created
    $this->assertNotNull($business->project_id);
    
    // Assert PM is owner
    $this->assertEquals($pm->id, $business->project->owner_id);
    
    // Assert kewirausahaan is admin member
    $member = $business->project->members->find($kewirausahaan->id);
    $this->assertEquals('admin', $member->pivot->role);
}
```

### Test 2: Project Has Correct Properties
```php
/** @test */
public function created_project_has_business_properties()
{
    $business = Business::factory()->create([
        'name' => 'Warung Kopi',
        'description' => 'Kopi enak murah meriah',
        'status' => 'pending',
    ]);
    
    $pm = User::factory()->create()->assignRole('pm');
    $this->actingAs($pm)->post(route('businesses.approve', $business));
    
    $project = $business->fresh()->project;
    
    $this->assertEquals('Warung Kopi', $project->name);
    $this->assertEquals('Kopi enak murah meriah', $project->description);
    $this->assertEquals('active', $project->status);
    $this->assertEquals('UMKM', $project->label);
    $this->assertTrue($project->is_public);
}
```

---

## 📝 User Stories

### Story 1: Kewirausahaan Mengajukan Usaha
```
As a: Kewirausahaan
I want to: Submit usaha proposal
So that: PM can review and approve it

Acceptance Criteria:
✅ Form create business dengan name & description
✅ Status otomatis "pending" setelah submit
✅ Notifikasi terkirim ke semua PM
✅ Redirect ke business index dengan success message
```

### Story 2: PM Mereview dan Approve
```
As a: PM
I want to: Review and approve business proposals
So that: A project is automatically created for the business

Acceptance Criteria:
✅ Receive notification when new business submitted
✅ View business details with approve/reject buttons
✅ Click approve → project auto-created
✅ I become project owner (full access)
✅ Kewirausahaan becomes project admin
✅ Success message dengan link ke project
```

### Story 3: Kewirausahaan Akses Project
```
As a: Kewirausahaan (business creator)
I want to: Access the project created from my approved business
So that: I can manage project activities

Acceptance Criteria:
✅ See "Proyek Terkait" box di business detail
✅ Click link → redirect to project page
✅ Have admin access in project
✅ Can add members, create tickets, view all data
✅ Cannot delete project (only PM can)
```

---

## 🚀 Deployment Checklist

- [x] Migration: `add_project_id_to_businesses_table`
- [x] Business model: `project()` relationship
- [x] BusinessController: approve logic updated
- [x] Views: Show project link when approved
- [x] Tests: Auto-creation workflow
- [x] Documentation: Updated
- [ ] Database migration in production
- [ ] Clear caches after deployment
- [ ] Test with real PM and kewirausahaan accounts

---

## ⚠️ Important Notes

1. **Transaction Safety**: Project creation and business approval happen in a DB transaction. If one fails, both rollback.

2. **Default Label**: All auto-created projects get "UMKM" label. PM can change this later in project settings.

3. **Project Ownership**: PM who approves becomes owner. This cannot be changed automatically.

4. **Member Role**: Kewirausahaan gets "admin" role, which gives them:
   - View all project data
   - Manage project members
   - Create and assign tickets
   - Cannot delete project
   - Cannot change project status to completed

5. **Rejection**: If PM rejects, NO project is created. Business stays in rejected state.

6. **Re-approval**: Once approved, business cannot be re-approved (policy prevents it).

---

## 🔄 Future Enhancements

- [ ] Allow PM to customize project properties before creation
- [ ] Option to add more initial members during approval
- [ ] Email notification to kewirausahaan when project created
- [ ] Template selection for different business types
- [ ] Automatic milestone creation based on business type
- [ ] Integration with financial planning (RAB)

---

## 📞 Support & Questions

For questions about this workflow:
1. Check `docs/BUSINESS_APPROVAL_AND_PROJECT_LABELS.md`
2. Review test cases in `tests/Feature/BusinessApprovalTest.php`
3. See controller logic in `app/Http/Controllers/BusinessController.php`
