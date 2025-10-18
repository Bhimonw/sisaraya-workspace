# Project Blackout Status Feature

## Overview
Fitur Blackout adalah status khusus untuk proyek yang berada dalam kondisi kritis atau darurat yang memerlukan perhatian segera dari semua anggota tim.

## Purpose
- **Emergency Response**: Menandai proyek yang dalam kondisi darurat
- **Priority Alert**: Memberikan visual alert yang jelas untuk proyek kritis
- **Focused Attention**: Memastikan tim fokus pada proyek yang memerlukan perhatian segera

## Status Blackout

### Karakteristik
- **Color Code**: Red (Merah) - `bg-red-100 text-red-800 border-red-300`
- **Icon**: Filled black circle (⚫)
- **Priority**: Highest - ditampilkan terpisah di bagian atas workspace
- **Visual**: Gradient merah, border merah, dengan animasi pulse

### Kapan Menggunakan Blackout?
1. **Deadline Kritis**: Proyek dengan deadline yang sangat dekat dan belum selesai
2. **Masalah Besar**: Proyek mengalami masalah signifikan yang harus segera diselesaikan
3. **Emergency**: Situasi darurat yang memerlukan respons cepat
4. **Critical Blocker**: Proyek terblokir oleh issue kritis yang menghambat progress

## Access Control

### Who Can Create Blackout Projects?
**Only PM (Project Manager)** dapat membuat proyek dengan status blackout.

### Permission Check
```php
// Routes middleware
Route::middleware(['auth', 'role:pm'])->group(function () {
    Route::post('projects', [ProjectController::class, 'store']);
});
```

## Implementation

### 1. Database Schema
Status blackout ditambahkan ke enum status proyek:

```php
// Project Model
protected $fillable = ['name','description','owner_id','status',...];

// Valid status values
'status' => 'required|in:planning,active,on_hold,completed,blackout'
```

### 2. Model Updates

**File: `app/Models/Project.php`**

```php
/**
 * Get status label
 */
public static function getStatusLabel(string $status): string
{
    return match($status) {
        'planning' => 'Perencanaan',
        'active' => 'Aktif',
        'on_hold' => 'Tertunda',
        'completed' => 'Selesai',
        'blackout' => 'Blackout',
        default => 'Perencanaan',
    };
}

/**
 * Get status color
 */
public static function getStatusColor(string $status): string
{
    return match($status) {
        'planning' => 'gray',
        'active' => 'blue',
        'on_hold' => 'yellow',
        'completed' => 'green',
        'blackout' => 'red',
        default => 'gray',
    };
}

/**
 * Scope: Get blackout projects
 */
public function scopeBlackout($query)
{
    return $query->where('status', 'blackout');
}
```

### 3. Controller Updates

**File: `app/Http/Controllers/ProjectController.php`**

#### Workspace Method
```php
public function workspace()
{
    $user = Auth::user();
    
    // Get blackout projects (CRITICAL - shown first)
    $blackoutProjects = Project::withCount('tickets')
        ->with(['owner', 'members'])
        ->where(function($q) use ($user) {
            $q->where('owner_id', $user->id)
              ->orWhereHas('members', function($q2) use ($user) {
                  $q2->where('user_id', $user->id);
              });
        })
        ->where('status', 'blackout')
        ->latest()
        ->get();
    
    // Get active projects
    $projects = Project::...->where('status', 'active')->get();
    
    return view('projects.workspace', compact('projects', 'blackoutProjects'));
}
```

#### Store & Update Validation
```php
$data = $request->validate([
    'status' => 'required|in:planning,active,on_hold,completed,blackout',
    // ... other fields
]);
```

### 4. UI Components

#### Status Badge Component
**File: `resources/views/components/project-status-badge.blade.php`**

```blade
<x-project-status-badge :status="$project->status" />
```

Renders:
- Blackout: Red badge dengan filled circle icon
- Active: Blue badge dengan lightning icon
- Etc.

#### Form Select (Create/Edit Project)
```blade
<select name="status" required class="...">
    <option value="planning">Perencanaan</option>
    <option value="active">Aktif</option>
    <option value="on_hold">Ditunda</option>
    <option value="completed">Selesai</option>
    <option value="blackout" class="text-red-600 font-semibold">⚫ Blackout</option>
</select>
<p class="mt-1 text-xs text-gray-500">
    <span class="text-red-600 font-semibold">Blackout</span>: 
    Proyek dalam kondisi darurat atau kritis yang memerlukan perhatian khusus
</p>
```

### 5. Workspace Display

#### Blackout Projects Section
```blade
@if($blackoutProjects->isNotEmpty())
    <div class="mb-8">
        <!-- Alert Header -->
        <div class="bg-gradient-to-r from-red-600 to-rose-600 rounded-xl shadow-lg p-6 mb-4">
            <div class="flex items-center gap-3 text-white">
                <svg>...</svg>
                <div>
                    <h2 class="text-2xl font-bold">⚠️ Proyek Blackout</h2>
                    <p class="text-red-100">Proyek dalam kondisi kritis...</p>
                </div>
            </div>
        </div>

        <!-- Blackout Projects Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($blackoutProjects as $project)
                <div class="bg-white rounded-xl shadow-lg border-2 border-red-500 
                            hover:shadow-xl transition-all duration-300 overflow-hidden 
                            group animate-pulse-slow">
                    <!-- Red header -->
                    <div class="bg-gradient-to-r from-red-600 to-rose-600 p-4">
                        <span>BLACKOUT</span>
                        <h3>{{ $project->name }}</h3>
                    </div>
                    <!-- Content... -->
                </div>
            @endforeach
        </div>
    </div>
@endif
```

### 6. Project Index Filter Tabs

```blade
<a href="{{ route('projects.index', ['status' => 'blackout']) }}" 
   class="flex-1 min-w-[120px] text-center py-4 px-4 border-b-2 font-medium text-sm
          @if($status === 'blackout') 
              border-red-600 text-red-600 bg-red-50 
          @else 
              border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50 
          @endif">
    <div class="flex items-center justify-center gap-2">
        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24">
            <circle cx="12" cy="12" r="10"/>
        </svg>
        <span class="font-bold">Blackout</span>
    </div>
</a>
```

## Visual Design

### Color Palette
```
Primary:     #DC2626 (red-600)
Light:       #FEE2E2 (red-100)
Text:        #991B1B (red-800)
Border:      #FCA5A5 (red-300)
Gradient:    from-red-600 to-rose-600
```

### Styling Features
1. **Border**: 2px solid red border
2. **Shadow**: Elevated shadow (shadow-lg)
3. **Animation**: Subtle pulse animation
4. **Gradient Header**: Red to rose gradient
5. **Icon**: Filled black circle (solid red in context)

### Animations
```css
/* Custom animation for blackout cards */
.animate-pulse-slow {
    animation: pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}
```

## User Flow

### Creating Blackout Project
1. PM navigates to Create Project form
2. Fills in project details
3. Selects **"Blackout"** status from dropdown
4. Sees warning: "Proyek dalam kondisi darurat atau kritis..."
5. Adds members (optional)
6. Clicks "Buat Proyek"
7. Project created with blackout status

### Viewing Blackout Projects
1. User navigates to **Workspace** (`/workspace`)
2. Blackout section appears **first** if any blackout projects exist
3. Red alert banner with warning icon
4. Grid of blackout projects with:
   - Red gradient header
   - BLACKOUT badge
   - Red action button
   - Pulse animation
5. Active projects section follows below

### Filtering Blackout Projects
1. Navigate to **Manajemen Proyek** (`/projects`)
2. Click **"Blackout"** tab in status filter
3. See all blackout projects across organization
4. Projects displayed with red theme

## Business Logic

### Status Transition
Projects can be moved to/from blackout status:

```
Planning → Blackout (emergency discovered)
Active → Blackout (crisis situation)
On Hold → Blackout (critical blocker resolved, now urgent)
Blackout → Active (emergency resolved)
Blackout → Completed (crisis managed successfully)
```

### Notifications (Future Enhancement)
When project status changed to blackout:
- [ ] Notify all project members
- [ ] Send email to PM and admins
- [ ] Create urgent notification in system
- [ ] Optional: SMS/Push notification for critical cases

## Testing Checklist

### Functional Tests
- [ ] PM can create project with blackout status
- [ ] PM can edit project status to blackout
- [ ] Non-PM cannot create blackout project (blocked by route middleware)
- [ ] Blackout projects appear in workspace section
- [ ] Blackout filter tab works in project index
- [ ] Badge component displays correctly for blackout status
- [ ] Validation accepts 'blackout' as valid status

### Visual Tests
- [ ] Red theme applied consistently
- [ ] Pulse animation works smoothly
- [ ] Gradient header renders correctly
- [ ] Border and shadow visible
- [ ] Icon displays as filled circle
- [ ] Responsive layout (mobile, tablet, desktop)

### Integration Tests
- [ ] Blackout projects counted separately from active
- [ ] Workspace query performance with multiple blackout projects
- [ ] Filter tabs maintain state across navigation
- [ ] Search/filter works with blackout status

## Database Migration

**File: `database/migrations/2025_10_18_051349_add_blackout_status_to_projects_table.php`**

```php
public function up(): void
{
    // SQLite doesn't enforce enum at DB level
    // MySQL/PostgreSQL users would need to alter column type
    // Status validation handled at application level
}
```

**Note**: For SQLite (default), enum is enforced by Laravel validation, not database constraints.

## Related Files

### Models
- `app/Models/Project.php` - Status methods and scopes

### Controllers
- `app/Http/Controllers/ProjectController.php` - Workspace, index, store, update

### Views
- `resources/views/projects/create.blade.php` - Create form with blackout option
- `resources/views/projects/edit.blade.php` - Edit form with blackout option
- `resources/views/projects/index.blade.php` - Filter tabs with blackout
- `resources/views/projects/workspace.blade.php` - Blackout section display
- `resources/views/components/project-status-badge.blade.php` - Status badge component

### Migrations
- `database/migrations/2025_10_18_051349_add_blackout_status_to_projects_table.php`

## Future Enhancements

1. **Auto Escalation**: Automatically set project to blackout if deadline < 24 hours and progress < 50%
2. **Blackout Dashboard**: Dedicated page for all organization blackout projects
3. **Escalation Levels**: Blackout Level 1 (warning), Level 2 (critical), Level 3 (emergency)
4. **Time Tracking**: Track how long project has been in blackout status
5. **Resolution Report**: Require summary when moving project out of blackout
6. **Notification System**: Push/email/SMS alerts for blackout projects
7. **Analytics**: Blackout history, frequency, resolution time metrics
8. **Auto-Resolution**: Remove blackout status after X days if no activity

## Best Practices

### When to Use Blackout
✅ **DO use blackout for**:
- Deadline in < 48 hours with significant incomplete work
- Critical bug/issue blocking all progress
- Emergency client request
- Major resource/team unavailability crisis

❌ **DON'T use blackout for**:
- Normal delays or slow progress
- Minor issues or bugs
- Regular priority projects
- Personal preference or emphasis

### Managing Blackout Projects
1. **Communicate**: Notify all stakeholders immediately
2. **Prioritize**: Deprioritize other work if necessary
3. **Daily Updates**: Require daily status updates
4. **Quick Wins**: Focus on quick resolutions
5. **Document**: Keep detailed notes of actions taken
6. **Review**: Post-blackout review to prevent recurrence

## Troubleshooting

### Blackout Section Not Showing
- Check if there are actually blackout projects for current user
- Verify workspace controller passes `$blackoutProjects` to view
- Check `@if($blackoutProjects->isNotEmpty())` condition

### Status Not Saving
- Verify validation includes 'blackout' in allowed values
- Check form has correct status option value
- Ensure database accepts the status value

### Badge Not Displaying Correctly
- Clear view cache: `php artisan view:clear`
- Check component path: `resources/views/components/project-status-badge.blade.php`
- Verify Tailwind classes are compiled

## Support

For issues or questions about blackout status feature:
1. Check this documentation first
2. Review related code files listed above
3. Test with a sample blackout project
4. Check browser console for JavaScript errors
5. Verify user has PM role for creating blackout projects
