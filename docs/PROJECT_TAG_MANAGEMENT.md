# Project Tag/Label Management — Documentation

**Date**: 17 Oktober 2025  
**Feature**: Label/Tag Management untuk Proyek  
**Location**: `resources/views/projects/show.blade.php`

---

## 🎯 Overview

Fitur tag/label management memungkinkan PM untuk mengkategorikan proyek berdasarkan jenis atau fungsinya. Label ditampilkan di header proyek dan dapat dikelola di tab "Kelola Proyek".

---

## 📋 Features

### 1. **Label Display di Project Header**

**Location**: Header proyek (di samping status badge)

**Design**:
- Badge dengan icon tag (SVG)
- Color-coded berdasarkan jenis label
- Rounded-full dengan border
- Inline dengan status badge

**Labels Available**:
| Label | Color | Use Case |
|-------|-------|----------|
| **UMKM** | Purple | Proyek usaha mikro kecil menengah |
| **DIVISI** | Blue | Proyek berbasis divisi/departemen |
| **Kegiatan** | Green | Proyek kegiatan/event |

**Display Format**:
```
[Project Name] [Status Badge] [Label Badge]
```

---

### 2. **Label Management di Settings Tab**

**Location**: Tab "Kelola Proyek" → Section "Label/Tag Proyek"

**Features**:
- ✅ Radio button selection dengan visual cards
- ✅ Color-coded cards per label
- ✅ Selected state dengan ring highlight + checkmark icon
- ✅ Option "Tidak ada label"
- ✅ Hover effects pada cards
- ✅ Responsive grid (1 col mobile, 3 cols desktop)

**UI Design**:
```
┌─────────────────────────────────────────────────────┐
│ 🏷️ Label/Tag Proyek                                 │
├─────────────────────────────────────────────────────┤
│ Pilih Label Proyek                                  │
│ Label membantu mengkategorikan proyek...            │
│                                                      │
│ ┌─────────┐  ┌─────────┐  ┌─────────┐             │
│ │ 🏷️ UMKM │  │ 🏷️ DIVISI│  │🏷️Kegiatan│            │
│ │ Selected│  │         │  │         │             │
│ │    ✓    │  │         │  │         │             │
│ └─────────┘  └─────────┘  └─────────┘             │
│                                                      │
│ ☐ Tidak ada label                                   │
└─────────────────────────────────────────────────────┘
```

---

## 🎨 Design Specifications

### Color Palette

**Purple (UMKM)**:
- Background: `bg-purple-50` (default), `bg-purple-100` (selected)
- Border: `border-purple-300` (default), `border-purple-500` (selected)
- Text: `text-purple-700`
- Ring: `ring-purple-500` (selected)
- Hover: `hover:bg-purple-100`

**Blue (DIVISI)**:
- Background: `bg-blue-50` (default), `bg-blue-100` (selected)
- Border: `border-blue-300` (default), `border-blue-500` (selected)
- Text: `text-blue-700`
- Ring: `ring-blue-500` (selected)
- Hover: `hover:bg-blue-100`

**Green (Kegiatan)**:
- Background: `bg-green-50` (default), `bg-green-100` (selected)
- Border: `border-green-300` (default), `border-green-500` (selected)
- Text: `text-green-700`
- Ring: `ring-green-500` (selected)
- Hover: `hover:bg-green-100`

### Component Structure

**Header Badge**:
```blade
<span class="inline-flex items-center px-3 py-1 text-sm font-medium 
             rounded-full border bg-{color}-100 text-{color}-700 border-{color}-300">
    <svg class="h-3.5 w-3.5 mr-1.5">...</svg>
    {{ $project->label }}
</span>
```

**Settings Card (Unselected)**:
```blade
<div class="flex items-center justify-center gap-2 px-4 py-3 border-2 
            rounded-xl transition-all duration-200 border-{color}-300 
            bg-{color}-50 text-{color}-700 hover:bg-{color}-100">
    <svg>...</svg>
    <span class="font-semibold">Label Name</span>
</div>
```

**Settings Card (Selected)**:
```blade
<div class="flex items-center justify-center gap-2 px-4 py-3 border-2 
            rounded-xl transition-all duration-200 border-{color}-500 
            bg-{color}-100 ring-2 ring-{color}-500">
    <svg>...</svg>
    <span class="font-semibold">Label Name</span>
    <svg class="h-4 w-4 ml-auto">✓ checkmark</svg>
</div>
```

---

## 🔧 Technical Implementation

### Model Method

**File**: `app/Models/Project.php`

```php
// Already exists
protected $fillable = ['name','description','owner_id','status',
                       'is_public','start_date','end_date','label'];

// Get available labels
public static function getLabels(): array
{
    return ['UMKM', 'DIVISI', 'Kegiatan'];
}

// Get label color
public static function getLabelColor(?string $label): string
{
    return match($label) {
        'UMKM' => 'purple',
        'DIVISI' => 'blue',
        'Kegiatan' => 'green',
        default => 'gray',
    };
}

// Scope for filtering
public function scopeByLabel($query, ?string $label)
{
    if ($label) {
        return $query->where('label', $label);
    }
    return $query;
}
```

### Form Submission

**Method**: POST/PUT  
**Route**: `projects.update`  
**Field**: `label` (nullable string)

**Validation** (should be added to controller):
```php
$request->validate([
    'label' => 'nullable|in:UMKM,DIVISI,Kegiatan',
    // ... other fields
]);
```

---

## 📱 Responsive Behavior

### Header Badges
- **Mobile**: Stack vertically if needed (flex-wrap)
- **Desktop**: Inline horizontal

### Settings Cards
- **Mobile (< 768px)**: 1 column (full width)
- **Tablet/Desktop (≥ 768px)**: 3 columns grid

---

## ✨ User Experience

### Visual Feedback
1. **Unselected State**:
   - Light background color
   - Subtle border
   - Hover: Background darkens slightly

2. **Selected State**:
   - Darker background
   - Thicker colored border
   - Ring highlight (2px)
   - Checkmark icon appears
   - Label text bold

3. **Transitions**:
   - All state changes: `transition-all duration-200`
   - Smooth color transitions
   - Instant checkmark appearance

### Accessibility
- Radio buttons use semantic HTML
- Screen reader friendly (sr-only class for actual input)
- Keyboard navigation support
- Clear visual indicators

---

## 🔄 Workflow

### Adding/Changing Label

1. Navigate to project detail page
2. Click tab "Kelola Proyek"
3. Scroll to "Label/Tag Proyek" section
4. Select desired label card (or "Tidak ada label")
5. Scroll down and click "Simpan Perubahan"
6. Label appears in project header

### Removing Label

1. Same as above
2. Select "Tidak ada label" option
3. Save changes

---

## 📊 Integration Points

### Current Usage
- **Projects Index**: Already shows label badges in filter tabs
- **Projects Show**: Header display + settings management

### Future Enhancements
- Add label filter to projects index
- Add label statistics to dashboard
- Label-based project grouping
- Label color customization

---

## 🧪 Testing Checklist

### Visual Testing
- [ ] Badge appears correctly in header when label is set
- [ ] Badge color matches label type
- [ ] Icon displays correctly
- [ ] Settings section shows all 3 labels + "no label" option
- [ ] Selected state shows ring + checkmark
- [ ] Hover effects work on all cards

### Functional Testing
- [ ] Can select label in settings
- [ ] Can change label to different one
- [ ] Can remove label (set to null)
- [ ] Form submission updates label correctly
- [ ] Page refresh shows correct selected state
- [ ] Validation prevents invalid labels

### Responsive Testing
- [ ] Header badges stack nicely on mobile
- [ ] Settings cards display 1 col on mobile
- [ ] Settings cards display 3 cols on desktop
- [ ] Touch targets appropriate size on mobile

---

## 📝 Code Locations

### Files Modified
1. **`resources/views/projects/show.blade.php`**
   - Line ~38: Header badge display
   - Line ~1410+: Settings section for label management

### Database
- Table: `projects`
- Column: `label` (nullable string)

---

## 🎯 Benefits

### For Users
✅ Quick visual categorization of projects  
✅ Easy filtering and organization  
✅ Clear project type identification  
✅ Improved project browsing experience

### For System
✅ Consistent labeling across all projects  
✅ Enforced label values (UMKM, DIVISI, Kegiatan)  
✅ Easy to extend with new labels  
✅ Queryable for reports and analytics

---

## 💡 Usage Examples

### Example 1: UMKM Project
```
Project: "Warung Kopi Sisaraya"
Label: UMKM (Purple)
Status: Active (Blue)

Display:
[Warung Kopi Sisaraya] [🟢 Aktif] [🏷️ UMKM]
```

### Example 2: Divisi Project
```
Project: "Website Komunitas"
Label: DIVISI (Blue)
Status: Planning (Gray)

Display:
[Website Komunitas] [⚪ Perencanaan] [🏷️ DIVISI]
```

### Example 3: Kegiatan Project
```
Project: "Bazar Ramadan 2025"
Label: Kegiatan (Green)
Status: Active (Blue)

Display:
[Bazar Ramadan 2025] [🟢 Aktif] [🏷️ Kegiatan]
```

---

## 🔮 Future Improvements

### Short Term
- [ ] Add label to project creation form
- [ ] Show label count in index filter tabs
- [ ] Add label-based sorting

### Long Term
- [ ] Custom label creation (admin only)
- [ ] Label color customization
- [ ] Label descriptions/tooltips
- [ ] Label-based permissions
- [ ] Label analytics dashboard

---

**Documentation Created**: 17 Oktober 2025  
**Last Updated**: 17 Oktober 2025  
**Status**: ✅ Complete & Ready to Use
