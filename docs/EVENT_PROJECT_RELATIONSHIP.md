# Event-Project Relationship

## Overview

Event dan Project sekarang memiliki hubungan many-to-many. Ini memungkinkan:
- Satu Event bisa terkait dengan banyak Project (misal: Workshop bisa untuk Project A dan B)
- Satu Project bisa memiliki banyak Event (misal: Project X ada Kickoff Meeting, Sprint Review, dll)

## Database Schema

### Pivot Table: `event_project`
```sql
- id (primary key)
- event_id (foreign key to events)
- project_id (foreign key to projects)
- timestamps
- UNIQUE constraint on (event_id, project_id) - prevent duplicates
```

## Model Relationships

### Event Model (`app/Models/Event.php`)
```php
public function projects()
{
    return $this->belongsToMany(Project::class, 'event_project')->withTimestamps();
}
```

### Project Model (`app/Models/Project.php`)
```php
public function events()
{
    return $this->belongsToMany(Event::class, 'event_project')->withTimestamps();
}
```

## Controller Methods

### EventController
**Attach Project to Event:**
```php
POST /events/{event}/attach-project
Parameters: project_id (required)
```

**Detach Project from Event:**
```php
DELETE /events/{event}/detach-project/{project}
```

## UI Implementation

### Event Detail Page (`events/show.blade.php`)
Menampilkan section "🗂️ Proyek Terkait" dengan:
- Dropdown untuk memilih proyek
- Button "Tambah Proyek"
- List proyek yang sudah terkait dengan link ke project detail
- Button "Hapus" untuk melepas project dari event

**Features:**
- Dropdown hanya menampilkan proyek yang belum terkait
- Confirmation dialog saat hapus
- Link ke project detail untuk quick access
- Icon visual untuk setiap proyek

### Project Calendar (`projects/show.blade.php`)
Calendar proyek sekarang menampilkan:
1. **Tickets dengan due_date** (warna sesuai status)
2. **Events terkait langsung** dengan proyek (warna purple `#8b5cf6`)
   - Via relationship `project->events`
   - Prefix "📅" di judul
3. **Events lain** yang diikuti member proyek (warna green `#10b981`)
   - Via participants relationship
   - Prefix "📅" di judul

## Use Cases

### Use Case 1: PM Assign Event ke Project
**Scenario:** PM ingin menghubungkan Workshop dengan Project Website Redesign

**Steps:**
1. Buka **Event > Workshop Detail**
2. Scroll ke section "🗂️ Proyek Terkait"
3. Pilih "Website Redesign" dari dropdown
4. Klik "Tambah Proyek"
5. Workshop sekarang muncul di calendar Project Website Redesign dengan warna purple

**Result:**
- Event muncul di calendar proyek
- Semua member proyek aware ada workshop terkait proyek mereka
- Calendar dashboard juga menampilkan event ini

### Use Case 2: Anggota Lihat Event Proyek
**Scenario:** Anggota yang assign ke Project X ingin lihat event terkait

**Steps:**
1. Buka **Proyek > Project X**
2. Scroll ke section "📅 Calendar Project"
3. Lihat calendar dengan:
   - Tickets proyek (deadline)
   - Events terkait langsung (purple)
   - Events lain yang member ikuti (green)

**Result:**
- Anggota tahu kapan ada meeting/workshop untuk proyek mereka
- Bisa planning kerja berdasarkan event schedule
- Tidak miss deadline dan event penting

### Use Case 3: Guest Invite ke Event & Project
**Scenario:** Guest diundang ke Workshop dan juga di-assign ke Project terkait

**Steps:**
1. HR/PM tambah Guest ke **Event > Workshop** dengan role "guest"
2. PM assign Guest ke **Project > Website Redesign**
3. PM hubungkan Workshop dengan Project Website Redesign
4. Guest login → lihat **Kalender Dashboard**

**Result:**
- Guest lihat Workshop di calendar (karena dia participant)
- Guest lihat Project Website Redesign timeline (karena dia member)
- Guest aware tentang jadwal project dan event yang dia ikuti

### Use Case 4: Lepas Event dari Project
**Scenario:** Event sudah selesai atau tidak relevan lagi dengan project

**Steps:**
1. Buka **Event > Workshop Detail**
2. Scroll ke "🗂️ Proyek Terkait"
3. Find project yang mau dilepas
4. Klik "Hapus"
5. Confirm dialog

**Result:**
- Event tidak muncul lagi di calendar project
- Relationship event-project terputus
- Event masih exist (tidak dihapus), hanya relationshipnya yang dilepas

## API Endpoints Impact

### `/api/calendar/project/{project}/events`
Sekarang returns:
1. **Project tickets** dengan due_date
2. **Events terkait langsung** via `project->events` (purple)
3. **Events lain** yang member ikuti (green)

### `/api/calendar/user/projects`
Untuk dashboard calendar, returns:
- Project timeline
- Project tickets
- Events yang terkait dengan project user

## Color Coding

| Item | Warna | Keterangan |
|------|-------|------------|
| Event terkait langsung | `#8b5cf6` (Purple) | Via event_project pivot table |
| Event lain (member participate) | `#10b981` (Green) | Via event_user pivot table |
| Ticket TODO | `#6b7280` (Gray) | Status todo |
| Ticket DOING | `#3b82f6` (Blue) | Status doing |
| Ticket DONE | `#22c55e` (Green) | Status done |

## Benefits

1. **Better Organization**
   - Events jelas terkait dengan project mana
   - Calendar project lebih informatif

2. **Improved Coordination**
   - Member proyek tahu event yang relevan
   - PM bisa schedule event per project

3. **Guest Management**
   - Guest yang di-assign ke project automatically tahu event terkait
   - Better onboarding untuk guest

4. **Timeline Visibility**
   - Project timeline tidak hanya tickets, tapi juga events
   - Holistic view dari aktivitas project

## Future Enhancements

1. **Auto-invite Project Members to Event**
   - Saat event di-attach ke project, auto invite semua member project

2. **Event Template per Project Type**
   - Project jenis "Website" auto suggest events: Kickoff, Design Review, UAT, Launch

3. **Event Dependency**
   - Event A harus selesai sebelum Event B bisa mulai
   - Gantt chart view

4. **Notification**
   - Notify project members saat event baru di-attach
   - Reminder H-1 sebelum event terkait project

5. **Event Impact on Project Progress**
   - Track apakah event done berdampak ke project milestone
   - Event completion checklist

## Testing Checklist

- [ ] Create event dan attach ke project
- [ ] Verify event muncul di project calendar dengan warna purple
- [ ] Create multiple events dan attach ke same project
- [ ] Detach event dari project, verify tidak muncul lagi di calendar
- [ ] Guest yang member project bisa lihat event terkait di calendar dashboard
- [ ] Member project bisa lihat both direct events (purple) dan other events (green)
- [ ] Unique constraint mencegah duplicate attach (attach 2x ke same project)
- [ ] Calendar dashboard menampilkan event dengan proper color coding
