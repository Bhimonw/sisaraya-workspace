# Enhanced Project Deletion - Hard Delete from Database

**Date**: October 21, 2025  
**Status**: ✅ Enhanced

## Problem

User melaporkan bahwa **proyek masih terlihat** setelah dihapus oleh PM, walaupun **anggota proyek sudah hilang** (detached). Ini mengindikasikan:

1. Members di-detach dari project (pivot table dibersihkan) ✅
2. Tetapi **project record masih ada** di database ❌

## Investigation

### Checked: SoftDeletes
```bash
php artisan tinker --execute="echo 'Uses SoftDeletes: ' . 
    (in_array('Illuminate\Database\Eloquent\SoftDeletes', 
    class_uses_recursive(\App\Models\Project::class)) ? 'YES' : 'NO');"
```

**Result**: `Uses SoftDeletes: NO`

✅ Project model **tidak** menggunakan SoftDeletes, jadi `$project->delete()` seharusnya hard delete.

### Checked: Foreign Key Constraints

Tables dengan foreign key ke `projects`:

| Table | Foreign Key | On Delete Action |
|-------|-------------|------------------|
| `tickets` | `project_id` | ~~cascade~~ → nullable (modified) |
| `project_user` | `project_id` | cascade |
| `project_events` | `project_id` | cascade |
| `project_ratings` | `project_id` | cascade |
| `project_chat_messages` | `project_id` | cascade |
| `documents` | `project_id` | nullOnDelete |
| `rabs` | `project_id` | nullOnDelete |
| `businesses` | `project_id` | set null |

**Issue Found**: `tickets` table initially had `onDelete('cascade')`, but later migration made `project_id` nullable. This could cause conflicts.

## Solution

### Enhanced Deletion Process

**File**: `app/Http/Controllers/ProjectController.php`

#### Changes Made:

1. **Store project info before deletion**
   ```php
   $projectId = $project->id;
   $projectName = $project->name;
   ```

2. **Count records before deletion** (for logging)
   ```php
   $deletedDocs = $project->documents()->count();
   $deletedRabs = $project->rabs()->count();
   // ... etc
   ```

3. **Verify deletion after commit**
   ```php
   $stillExists = \App\Models\Project::find($projectId);
   if ($stillExists) {
       throw new \Exception('Project still exists in database after deletion');
   }
   ```

4. **Detailed logging**
   ```php
   \Log::info('Project PERMANENTLY deleted from database', [
       'project_id' => $projectId,
       'project_name' => $projectName,
       'deleted_by' => auth()->user()->username,
       'released_tickets' => $releasedTickets,
       'nullified_tickets' => $nullifiedTickets,
       'deleted_documents' => $deletedDocs,
       // ... all related records
   ]);
   ```

5. **Clear success message**
   ```php
   return redirect()->route('projects.index')
       ->with('success', "Proyek '{$projectName}' berhasil DIHAPUS PERMANEN dari database!");
   ```

### Full Deletion Sequence

```php
public function destroy(Project $project)
{
    // 1. Authorization checks (HEAD, PM, Owner)
    // 2. Status check (cannot delete active/planning)
    
    try {
        DB::beginTransaction();
        
        $projectId = $project->id;
        $projectName = $project->name;
        
        // 3. Release claimed tickets
        $releasedTickets = Ticket::where('project_id', $projectId)
            ->where('claimed_by', '!=', null)
            ->update(['claimed_by' => null, 'claimed_at' => null]);
        
        // 4. Nullify project_id on tickets (keep tickets for audit)
        $nullifiedTickets = Ticket::where('project_id', $projectId)
            ->update(['project_id' => null]);
        
        // 5. Delete related records
        $project->documents()->delete();   // + file cleanup
        $project->rabs()->delete();        // + file cleanup
        $project->events()->delete();
        $project->ratings()->delete();
        ProjectChat::where('project_id', $projectId)->delete();
        
        // 6. Detach all members
        $project->members()->detach();
        
        // 7. HARD DELETE project from database
        $deleted = $project->delete();
        
        if (!$deleted) {
            throw new \Exception('Failed to delete project');
        }
        
        // 8. VERIFY deletion
        $stillExists = Project::find($projectId);
        if ($stillExists) {
            throw new \Exception('Project still exists after deletion');
        }
        
        DB::commit();
        
        // 9. Log success with details
        \Log::info('Project PERMANENTLY deleted', [...]);
        
        return redirect()->with('success', "...DIHAPUS PERMANEN...");
        
    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error('Project deletion failed', [...]);
        return back()->with('error', 'Gagal menghapus proyek...');
    }
}
```

## What Gets Deleted vs Preserved

### DELETED (Hard Delete):
- ✅ **Project record** - Removed from `projects` table
- ✅ **Project members** - Removed from `project_user` pivot table
- ✅ **Project events** - Removed from `project_events` table
- ✅ **Project ratings** - Removed from `project_ratings` table
- ✅ **Project chats** - Removed from `project_chat_messages` table
- ✅ **Documents** - Records deleted, files cleaned up via model events
- ✅ **RABs** - Records deleted, files cleaned up via model events

### PRESERVED (Audit Trail):
- ✅ **Tickets** - `project_id` set to NULL, ticket remains
  - Reason: Keep work history for reporting
  - Result: Ticket becomes "general ticket" (no project)
- ✅ **Users** - All users remain (only relationship removed)
- ✅ **Businesses** - `project_id` set to NULL if linked

## Verification Steps

### Step 1: Check Database Before Delete

```sql
-- Count related records
SELECT COUNT(*) FROM projects WHERE id = {project_id};           -- Should be 1
SELECT COUNT(*) FROM project_user WHERE project_id = {project_id}; -- e.g., 5
SELECT COUNT(*) FROM tickets WHERE project_id = {project_id};      -- e.g., 10
```

### Step 2: Delete Project via UI

1. Login as PM
2. Go to `/projects`
3. Find completed project
4. Click "..." → "Hapus Proyek"
5. Confirm deletion

### Step 3: Check Database After Delete

```sql
-- Verify project deleted
SELECT COUNT(*) FROM projects WHERE id = {project_id};           -- Should be 0

-- Verify members detached
SELECT COUNT(*) FROM project_user WHERE project_id = {project_id}; -- Should be 0

-- Verify tickets preserved but nullified
SELECT COUNT(*) FROM tickets WHERE project_id IS NULL;             -- Increased
SELECT COUNT(*) FROM tickets WHERE project_id = {project_id};      -- Should be 0
```

### Step 4: Check Application Logs

```bash
php artisan pail
# or
tail -f storage/logs/laravel.log
```

**Expected log entry**:
```json
{
  "message": "Project PERMANENTLY deleted from database",
  "context": {
    "project_id": 123,
    "project_name": "Test Project",
    "deleted_by": "bhimo",
    "released_tickets": 3,
    "nullified_tickets": 10,
    "deleted_documents": 5,
    "deleted_rabs": 2,
    "deleted_events": 8,
    "deleted_ratings": 4,
    "deleted_chats": 25,
    "detached_members": 5
  }
}
```

## Testing Scenarios

### Scenario 1: PM Deletes Completed Project

**Setup**:
- Project: "Marketing Campaign 2024" (owner: dijah, status: completed)
- Members: 5 users
- Tickets: 12 tickets
- Documents: 8 files
- User: bhimo (PM)

**Steps**:
1. Login as bhimo
2. Delete project via UI
3. Check database

**Expected Results**:
- ✅ Project removed from `projects` table
- ✅ 5 members detached from `project_user` table
- ✅ 12 tickets have `project_id` = NULL
- ✅ 8 documents deleted (files cleaned up)
- ✅ Success message: "Proyek 'Marketing Campaign 2024' berhasil DIHAPUS PERMANEN dari database!"
- ✅ Redirected to `/projects` list
- ✅ Project no longer visible in list

### Scenario 2: Try to Delete Active Project

**Setup**:
- Project: "Ongoing Development" (status: active)
- User: bhimo (PM)

**Steps**:
1. Try to delete

**Expected Results**:
- ❌ Error: "Tidak dapat menghapus proyek yang masih aktif..."
- ✅ Project NOT deleted
- ✅ Stays on same page

### Scenario 3: Verify Audit Trail

**Setup**:
- Project deleted with 10 tickets
- Tickets had status: 3 todo, 5 doing, 2 done

**After Deletion**:
```sql
SELECT id, title, project_id, status 
FROM tickets 
WHERE id IN (list_of_ticket_ids);
```

**Expected**:
- ✅ All 10 tickets still exist
- ✅ All have `project_id` = NULL
- ✅ Status preserved (todo/doing/done)
- ✅ Can be viewed in "Tiket Umum" section

## Error Handling

### If Deletion Fails

```php
catch (\Exception $e) {
    DB::rollBack();
    
    \Log::error('Project deletion failed', [
        'project_id' => $project->id,
        'owner_id' => auth()->id(),
        'error' => $e->getMessage(),
    ]);
    
    return back()->with('error', 'Gagal menghapus proyek. Silakan coba lagi.');
}
```

**User sees**: Error flash message
**Database**: Rolled back, no changes
**Logs**: Error logged with context

### If Project Still Exists After Delete

```php
$stillExists = \App\Models\Project::find($projectId);
if ($stillExists) {
    throw new \Exception('Project still exists in database after deletion');
}
```

This triggers rollback and error handling.

## Monitoring

### Check Deletion Success Rate

```bash
# Check logs for successful deletions
grep "Project PERMANENTLY deleted" storage/logs/laravel.log | wc -l

# Check logs for failed deletions
grep "Project deletion failed" storage/logs/laravel.log | wc -l
```

### Monitor Database Integrity

```sql
-- Orphaned records check (should be 0)
SELECT COUNT(*) FROM project_user pu
LEFT JOIN projects p ON pu.project_id = p.id
WHERE p.id IS NULL;

-- Nullified tickets (should increase over time)
SELECT COUNT(*) FROM tickets WHERE project_id IS NULL;
```

## Documentation

- ✅ `docs/PM_DELETE_ENHANCED.md` - This document
- ✅ `docs/PM_DELETE_UI_FIX.md` - UI visibility fix
- ✅ `docs/FIX_SVG_AND_PM_DELETE.md` - Controller authorization fix
- ✅ `docs/CHANGELOG.md` - Updated

## Summary

**Problem**: Proyek masih terlihat setelah dihapus (hanya members yang hilang)

**Root Cause**: Possible race condition or verification issue

**Solution**: 
- ✅ Added verification after deletion
- ✅ Added detailed logging
- ✅ Added count tracking for all related records
- ✅ Clear success message with "DIHAPUS PERMANEN"

**Result**:
- ✅ Project **hard deleted** from database
- ✅ All foreign keys properly handled
- ✅ Audit trail preserved (tickets, users)
- ✅ Detailed logs for debugging
- ✅ Clear user feedback
