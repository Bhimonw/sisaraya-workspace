# Status Field untuk Buat Tiket Umum

**Tanggal**: 20 Oktober 2025  
**Tipe**: Feature Enhancement  
**Status**: ✅ Selesai

---

## 🎯 Problem

User melaporkan bahwa saat membuat tiket umum, tidak ada opsi untuk memilih status **Blackout**. Tiket hanya bisa dibuat dengan status TODO secara default, dan tidak ada cara untuk langsung membuat tiket dengan status Blackout.

---

## ✅ Solution

Menambahkan field **Status Awal** pada form "Buat Tiket Umum" dengan 2 opsi:
1. **⏰ To Do** - Belum dikerjakan (default)
2. **⚫ Blackout** - Ditunda/dibatalkan

---

## 📝 Implementation

### File Changed
`resources/views/components/tickets/form-fields.blade.php`

### Changes Made

#### Before
Form hanya memiliki 3 fields dalam 1 row:
- Priority
- Bobot (dengan slider)
- Deadline

Status tiket secara implisit selalu `todo`.

#### After
Form sekarang memiliki 2 rows:

**Row 1: Status & Priority**
- Status Awal (NEW!)
- Priority

**Row 2: Bobot & Deadline**
- Bobot (dengan slider)
- Deadline

---

## 🎨 UI/UX Design

### Status Field Specifications

```blade
<select name="status" id="status" class="...">
    <option value="todo" selected>⏰ To Do - Belum dikerjakan</option>
    <option value="blackout">⚫ Blackout - Ditunda/dibatalkan</option>
</select>
<p class="text-xs text-gray-500 mt-1">
    Pilih "Blackout" untuk tiket yang sementara ditunda
</p>
```

**Features**:
- Icon emoji untuk visual clarity (⏰ dan ⚫)
- Descriptive text untuk setiap option
- Help text di bawah dropdown menjelaskan kegunaan Blackout
- Default value: `todo`

---

## 📐 Layout Changes

### Before (3 columns)
```
┌──────────────────────────────────────────────────┐
│ [Priority]  [Bobot]  [Deadline]                  │
└──────────────────────────────────────────────────┘
```

### After (2 rows × 2 columns)
```
┌──────────────────────────────────────────────────┐
│ [Status Awal]              [Priority]            │
│ ⏰ To Do / ⚫ Blackout      🟢🟡🟠🔴             │
├──────────────────────────────────────────────────┤
│ [Bobot]                    [Deadline]            │
│ 🪶⚖️🏋️ Slider 1-10         📅 Date picker       │
└──────────────────────────────────────────────────┘
```

**Benefits**:
- Better visual balance (2×2 grid)
- Logical grouping: Status+Priority, Weight+Deadline
- More breathing room for each field
- Mobile responsive with `md:grid-cols-2`

---

## 🔧 Technical Details

### HTML Structure

**Grid Container 1** (Status & Priority):
```blade
<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
    {{-- Status field --}}
    {{-- Priority field --}}
</div>
```

**Grid Container 2** (Bobot & Deadline):
```blade
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    {{-- Bobot field with Alpine.js --}}
    {{-- Deadline field --}}
</div>
```

### Form Data
```php
// POST data when submitted
[
    'status' => 'todo' | 'blackout',  // NEW!
    'priority' => 'low' | 'medium' | 'high' | 'urgent',
    'weight' => 1-10,
    'due_date' => 'YYYY-MM-DD',
    // ... other fields
]
```

---

## 📊 Use Cases

### Use Case 1: Tiket Normal (TODO)
**Scenario**: PM membuat tiket untuk review proposal  
**Action**: Pilih "⏰ To Do" (default)  
**Result**: Tiket dibuat dengan status `todo`, muncul di kolom "To Do"

### Use Case 2: Tiket Ditunda (Blackout)
**Scenario**: PM membuat tiket untuk event yang ditunda karena force majeure  
**Action**: Pilih "⚫ Blackout"  
**Result**: Tiket dibuat dengan status `blackout`, muncul di statistik Blackout

### Use Case 3: Pre-planning
**Scenario**: PM membuat tiket untuk ide yang belum siap dikerjakan  
**Action**: Pilih "⚫ Blackout"  
**Result**: Tiket tersimpan tapi tidak muncul di available tickets

---

## ✅ Benefits

1. **Flexibility** - User bisa langsung membuat tiket blackout tanpa harus edit nanti
2. **Clarity** - Explicit status selection vs implicit default
3. **Workflow** - Support use case pre-planning dan force majeure
4. **Consistency** - Semua status badges sekarang bisa dibuat dari awal
5. **Documentation** - Help text menjelaskan kegunaan Blackout

---

## 🧪 Testing

### Manual Testing Checklist

- [x] Status field muncul di form "Buat Tiket Umum"
- [x] Default value adalah "To Do"
- [x] Option "Blackout" bisa dipilih
- [x] Help text muncul di bawah dropdown
- [x] Form tetap responsive di mobile (stacks vertically)
- [x] Form submission dengan status="todo" berhasil
- [x] Form submission dengan status="blackout" berhasil
- [x] Tiket TODO muncul di kolom "To Do"
- [x] Tiket Blackout muncul di statistik Blackout
- [x] Layout 2×2 grid terlihat balanced

### Database Verification

```sql
-- Check created tickets
SELECT id, title, status FROM tickets 
WHERE created_at > '2025-10-20' 
ORDER BY created_at DESC;

-- Result should show mix of 'todo' and 'blackout' statuses
```

---

## 🎨 Visual Reference

**Before** (screenshot user):
```
[Priority ▼] [Bobot slider] [Deadline 📅]
```

**After**:
```
Row 1:
[Status Awal ▼]              [Priority ▼]
⏰ To Do - Belum...          🟡 Sedang

Row 2:
[Bobot Level: ⚖️ 5 Sedang]   [Deadline 📅]
Slider 1────●────10          dd/mm/yyyy
```

---

## 📝 Files Changed

### Modified
1. `resources/views/components/tickets/form-fields.blade.php`
   - Added Status field with TODO/Blackout options
   - Restructured layout from 3-column to 2×2 grid
   - Added help text for Blackout explanation
   - Updated grid classes for better responsive behavior

---

## 🔗 Related Features

- `docs/TICKET_STATUS_BADGE_ENHANCEMENT.md` - Status badge gradients
- `docs/TIKETKU_BADGE_CONSISTENCY.md` - Badge consistency across pages
- `docs/TICKET_FORM_REFACTORING.md` - Component extraction

---

## 🚀 Future Enhancements

### Potential Improvements
1. Add "Doing" and "Done" status options (jika diperlukan)
2. Conditional field visibility based on status (e.g., hide deadline untuk Blackout)
3. Bulk status change untuk multiple tickets
4. Status change reason/notes field untuk Blackout

---

**Priority**: High (User-requested feature)  
**Impact**: Medium (Improves workflow flexibility)  
**Status**: Production Ready ✅
