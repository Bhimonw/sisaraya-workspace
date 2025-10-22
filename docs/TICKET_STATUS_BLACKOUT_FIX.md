# Ticket Status Blackout Fix

**Tanggal:** 22 Oktober 2025  
**Status:** ✅ Selesai

## Problem

Error SQL saat membuat ticket dengan status "blackout":
```
SQLSTATE[01000]: Warning: 1265 Data truncated for column 'status' at row 1
```

Status "blackout" tidak ada dalam enum definition di database, tapi sudah digunakan di:
- Model `Ticket.php` (getStatusLabel, getStatusColor)
- Views (overview.blade.php, index.blade.php, show.blade.php)

## Solution

### 1. Update Base Migration

File: `database/migrations/2025_10_12_000002_create_tickets_table.php`

```php
$table->enum('status', ['todo','doing','done','blackout'])->default('todo');
```

### 2. Create Alter Migration for Production

File: `database/migrations/2025_10_22_000001_add_blackout_status_to_tickets.php`

Migration ini menggunakan `DB::statement()` untuk alter enum di production database tanpa drop column:

```php
DB::statement("ALTER TABLE tickets MODIFY COLUMN status ENUM('todo', 'doing', 'done', 'blackout') DEFAULT 'todo'");
```

### 3. Update Form Views

File: `resources/views/tickets/create_from_rab.blade.php`

Menambahkan option blackout di dropdown status:
```blade
<option value="blackout">Blackout</option>
```

## Ticket Status Definitions

| Status | Label | Color | Deskripsi |
|--------|-------|-------|-----------|
| `todo` | To Do | Gray | Tiket belum dikerjakan |
| `doing` | Doing | Blue | Tiket sedang dikerjakan |
| `done` | Done | Green | Tiket sudah selesai |
| `blackout` | Blackout | Black | Tiket ditunda/freeze |

## Migration Command

```powershell
php artisan migrate
```

Output:
```
2025_10_22_000001_add_blackout_status_to_tickets ..... DONE
```

## Rollback

Jika perlu rollback:

```powershell
php artisan migrate:rollback --step=1
```

Migration akan otomatis:
1. Update semua ticket dengan status 'blackout' menjadi 'todo'
2. Alter enum kembali ke `['todo', 'doing', 'done']`

## Testing Checklist

- [x] Migration berhasil dijalankan
- [x] Enum status include 'blackout'
- [x] Form create ticket memiliki option blackout
- [x] UI overview/index sudah support blackout
- [x] Model Ticket sudah ada helper methods untuk blackout
- [ ] Test create ticket dengan status blackout (manual testing)
- [ ] Test drag & drop ticket ke column blackout di kanban

## Related Files

- `app/Models/Ticket.php` - Status helpers
- `resources/views/tickets/overview.blade.php` - Kanban blackout column
- `resources/views/tickets/index.blade.php` - List view dengan blackout badge
- `resources/views/tickets/show.blade.php` - Detail view dengan blackout status
- `resources/views/tickets/create_from_rab.blade.php` - Form dengan blackout option

## Notes

Status "blackout" digunakan untuk tiket yang sementara ditunda atau di-freeze karena:
- Menunggu keputusan/approval
- Resource tidak tersedia
- Dependency belum selesai
- External blocker

Berbeda dengan "todo" yang menunggu dikerjakan, "blackout" menandakan tiket tidak bisa dikerjakan dalam kondisi saat ini.
