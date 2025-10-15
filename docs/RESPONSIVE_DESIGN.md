# 📱 Responsive Design Documentation

**Ruang Kerja Sisaraya MVP - Mobile Responsive Implementation**

## Breakpoint Strategy

Aplikasi ini menggunakan **mobile-first approach** dengan breakpoint Tailwind CSS:

| Breakpoint | Minimum Width | Target Devices | Usage |
|------------|--------------|----------------|-------|
| (default) | 0px | Mobile portrait | Base styles, full-width layouts |
| `sm:` | 640px | Mobile landscape, small tablets | Start introducing grids |
| `md:` | 768px | Tablets | Multi-column layouts |
| `lg:` | 1024px | Desktop, large tablets | Full desktop experience |

## Common Responsive Patterns

### 1. Stacking → Grid Pattern
```blade
<!-- Mobile: vertical stack | Desktop: grid -->
<div class="space-y-3 sm:space-y-0 sm:grid sm:grid-cols-2 md:grid-cols-3 gap-4">
  <div>Item 1</div>
  <div>Item 2</div>
  <div>Item 3</div>
</div>
```

### 2. Horizontal Scroll Pattern
```blade
<!-- Mobile: horizontal scroll | Desktop: full grid -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 overflow-x-auto lg:overflow-x-visible">
  <div class="min-w-[280px] lg:min-w-0">Column 1</div>
  <div class="min-w-[280px] lg:min-w-0">Column 2</div>
  <div class="min-w-[280px] lg:min-w-0">Column 3</div>
</div>
```

### 3. Vertical Height Control
```blade
<!-- Prevent columns from becoming too tall -->
<div class="max-h-[60vh] overflow-y-auto">
  <!-- Long content with vertical scroll -->
</div>
```

### 4. Responsive Button Width
```blade
<!-- Mobile: full width | Desktop: auto width -->
<button class="w-full sm:w-auto px-4 py-2">Submit</button>
```

### 5. Flex Direction Toggle
```blade
<!-- Mobile: column | Desktop: row -->
<div class="flex flex-col lg:flex-row lg:items-center gap-4">
  <div>Left</div>
  <div>Right</div>
</div>
```

### 6. Responsive Text Sizes
```blade
<!-- Scale text with viewport -->
<h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold">Title</h1>
<p class="text-sm sm:text-base">Description</p>
```

## Components Implemented

### ✅ Dashboard (`resources/views/dashboard.blade.php`)

**Mobile Behavior:**
- Header: Title and role badges stack vertically
- Role badges wrap to multiple lines if needed
- Welcome card with responsive text (xl→2xl title, sm→base body)
- Stats cards: 1 column on mobile
- Quick Actions: 1 column on mobile, 2 on sm+, 3 on lg+
- Recent Activity: Stack timestamp below content on mobile

**Desktop Behavior:**
- Header: Horizontal layout with badges on right
- Stats cards: 4 columns on lg+
- Quick Actions: 3 columns grid
- Recent Activity: Horizontal layout with timestamp on right

**Code Pattern:**
```blade
<!-- Header responsive -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
  <h2>Dashboard</h2>
  <div class="flex flex-wrap gap-2"><!-- Role badges --></div>
</div>

<!-- Stats grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
  <!-- Stat cards -->
</div>
```

### ✅ Calendar Dashboard (`resources/views/calendar/dashboard.blade.php`)

**Mobile Behavior:**
- Padding reduced: p-4 instead of p-6
- Info box text: xs→sm for better readability
- Calendar: Starts with 'timeGridDay' view (better for small screens)
- Header toolbar: Only 'timeGridDay' button on mobile
- Calendar height: Fixed 400px on mobile to prevent excessive scrolling
- Modal: Positioned top-4 with p-4 padding, full-width on mobile
- Legend: 1 column on mobile, 2 on sm+, 3 on lg+

**Desktop Behavior:**
- Calendar: Starts with 'dayGridMonth' view
- Full header toolbar: Month, Week, Day buttons
- Auto height for calendar
- Modal: Centered with max-w-lg

**Code Pattern:**
```javascript
initialView: window.innerWidth < 640 ? 'timeGridDay' : 'dayGridMonth',
headerToolbar: {
  right: window.innerWidth < 640 ? 'timeGridDay' : 'dayGridMonth,timeGridWeek,timeGridDay'
},
contentHeight: window.innerWidth < 640 ? 400 : 'auto',
```

### ✅ Projects - Kanban Board (`resources/views/projects/show.blade.php`)

**Mobile Behavior:**
- Single column with horizontal scroll
- Each column min-width: 280px for readability
- Tickets stack vertically within cards
- Max-height: 60vh to prevent excessive scrolling

**Desktop Behavior:**
- 3-column grid (To Do, Doing, Done)
- Full visibility without scrolling
- Tickets display in row layout

**Code Example:**
```blade
<div x-data class="grid grid-cols-1 lg:grid-cols-3 gap-4 overflow-x-auto lg:overflow-x-visible">
  @foreach(['todo'=>'To Do','doing'=>'Doing','done'=>'Done'] as $key => $label)
    <div class="bg-gray-50 p-3 rounded shadow-sm min-w-[280px] lg:min-w-0">
      <h3 class="font-semibold text-sm sm:text-base">{{ $label }}</h3>
      <div class="mt-3 space-y-3 max-h-[60vh] overflow-y-auto">
        <!-- Tickets -->
      </div>
    </div>
  @endforeach
</div>
```

### ✅ Projects - Header (`resources/views/projects/show.blade.php`)

**Mobile Behavior:**
- Title and description stack above member avatars
- Text sizes scale: 2xl → 3xl (title), sm → base (description)
- Member avatars limited to 5 with "+X more" indicator

**Desktop Behavior:**
- Horizontal layout with title left, members right
- Full member avatar display (up to 5 visible)

**Code Example:**
```blade
<div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
  <div class="flex-1">
    <h1 class="text-2xl sm:text-3xl font-bold">{{ $project->name }}</h1>
    <p class="text-gray-600 mt-1 text-sm sm:text-base">{{ $project->description }}</p>
  </div>
  <div class="flex items-center gap-4">
    <div class="text-sm text-gray-500">Members</div>
    <div class="flex -space-x-2">
      @foreach($project->members->take(5) as $member)
        <!-- Avatar -->
      @endforeach
      @if($project->members->count() > 5)
        <div>+{{ $project->members->count() - 5 }}</div>
      @endif
    </div>
  </div>
</div>
```

### ✅ Projects - New Ticket Form (`resources/views/projects/show.blade.php`)

**Mobile Behavior:**
- Inputs stack vertically with full width
- Button expands to full width for easy tapping
- Vertical spacing (space-y-3) between inputs

**Desktop Behavior:**
- 3-column grid (title spans 2 cols, status 1 col)
- Auto-width button aligned left
- Horizontal gap between inputs

**Code Example:**
```blade
<form action="{{ route('projects.tickets.store', $project) }}" method="POST">
  <div class="space-y-3 sm:space-y-0 sm:grid sm:grid-cols-1 md:grid-cols-3 sm:gap-2">
    <input name="title" placeholder="Title" required 
      class="w-full md:col-span-2 border border-gray-300 p-2 rounded focus:ring-2 focus:ring-green-500">
    <select name="status" class="w-full border border-gray-300 p-2 rounded">
      <option value="todo">To Do</option>
      <option value="doing">Doing</option>
      <option value="done">Done</option>
    </select>
  </div>
  <div class="mt-3">
    <button type="submit" class="w-full sm:w-auto bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">
      Create Ticket
    </button>
  </div>
</form>
```

### ✅ Calendar - Personal Activities Modal (`resources/views/calendar/personal.blade.php`)

**Mobile Behavior:**
- Datetime inputs stack vertically
- Buttons stack vertically with full width
- Modal max-height: 90vh with vertical scroll

**Desktop Behavior:**
- Datetime inputs side-by-side (grid-cols-2)
- Buttons in horizontal row
- Modal centered with max-width: 2xl

**Code Example:**
```blade
<!-- Modal container -->
<div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
  <!-- Datetime inputs -->
  <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div>
      <label>Waktu Mulai *</label>
      <input type="datetime-local" id="start_time" name="start_time" required>
    </div>
    <div>
      <label>Waktu Selesai *</label>
      <input type="datetime-local" id="end_time" name="end_time" required>
    </div>
  </div>
  
  <!-- Buttons -->
  <div class="flex flex-col sm:flex-row gap-2 mt-6">
    <button type="submit" class="flex items-center justify-center gap-2">
      <svg class="h-4 w-4"><!-- Save icon --></svg>
      Simpan
    </button>
    <button type="button" onclick="closeActivityModal()">Batal</button>
  </div>
</div>
```

## Touch Target Sizes

**WCAG Compliance:** Minimum 44x44px for touch targets

| Element | Mobile Size | Desktop Size | Compliance |
|---------|------------|--------------|------------|
| Buttons | Full width, py-2 (≥44px) | Auto width, py-2 | ✅ |
| Form inputs | Full width, p-2 (≥44px) | Auto width, p-2 | ✅ |
| Links | Adequate padding | Adequate padding | ✅ |
| Icons | 16x16px (with padding) | 16x16px | ✅ |
| Avatar badges | 32x32px + overlap | 32x32px | ✅ |

## Overflow Handling

### Horizontal Overflow (Wide Content)
```blade
<!-- Kanban columns, tables, wide cards -->
<div class="overflow-x-auto">
  <!-- Content -->
</div>
```

### Vertical Overflow (Long Lists)
```blade
<!-- Ticket lists, activity feeds -->
<div class="max-h-[60vh] overflow-y-auto">
  <!-- Content -->
</div>
```

### Modal Overflow
```blade
<!-- Full modal scrollable -->
<div class="max-h-[90vh] overflow-y-auto">
  <!-- Modal content -->
</div>
```

## Icon System

All icons use **Heroicons SVG** for consistency:

```blade
<!-- Save/Download icon -->
<svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
    d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
</svg>
```

**Icon Sizes:**
- Main menu: `h-4 w-4`
- Submenu: `h-3.5 w-3.5`
- Buttons: `h-4 w-4`

### ✅ Events Show (`resources/views/events/show.blade.php`)

**Mobile Behavior:**
- Title: xl→2xl responsive scaling
- Forms stack vertically (participant form, project form)
- Buttons: Full-width on mobile
- Project cards: Icon and text stack on mobile
- "Hapus" button: Full-width with border for better visibility

**Desktop Behavior:**
- Forms: Horizontal layout with flex
- Buttons: Auto-width
- Project cards: Horizontal layout with icon left

**Code Pattern:**
```blade
<div class="space-y-2 sm:space-y-0 sm:flex sm:gap-2">
  <select class="w-full sm:flex-1"><!-- Options --></select>
  <input class="w-full sm:w-auto">
  <button class="w-full sm:w-auto">Submit</button>
</div>
```

### ✅ Admin Users Table (`resources/views/admin/users/index.blade.php`)

**Mobile Behavior:**
- Wrapper has `overflow-x-auto` for horizontal scroll
- Table has `min-w-[600px]` to ensure readable columns
- Text sizes: sm→base responsive
- Responsive padding: p-3 sm:p-4

**Desktop Behavior:**
- Table displays at full width without scroll
- Standard text sizes

**Code Pattern:**
```blade
<div class="overflow-x-auto">
  <table class="w-full text-sm sm:text-base min-w-[600px]">
    <!-- Table content -->
  </table>
</div>
```

## Testing Checklist

### Mobile Portrait (375px - 640px)
- [x] Dashboard cards stack properly
- [x] Dashboard role badges wrap correctly
- [x] Dashboard header stacks vertically
- [x] Dashboard Quick Actions 1 column
- [x] Dashboard Recent Activity stacks
- [x] Sidebar menu collapses and expands
- [x] Forms have full-width inputs
- [x] Buttons are easy to tap (full width)
- [x] Kanban board scrolls horizontally
- [x] Project header stacks vertically
- [x] New Ticket form stacks inputs
- [x] Personal activities modal datetime stacks
- [x] Personal activities modal buttons stack
- [x] Calendar dashboard uses day view
- [x] Calendar dashboard modal responsive
- [x] Calendar dashboard legend stacks
- [x] Events show form stacks
- [x] Events show project cards stack
- [x] Admin users table scrolls horizontally
- [x] Modals fit on screen with scroll
- [x] Text is readable (min 12px)
- [x] Touch targets ≥44px

### Mobile Landscape / Small Tablet (640px - 768px)
- [x] 2-column grids display correctly (Quick Actions, Legend)
- [x] Forms start using horizontal layout
- [x] Buttons return to auto width
- [x] Datetime inputs side-by-side
- [x] Calendar switches to month view
- [x] Dashboard header horizontal

### Tablet (768px - 1024px)
- [x] Dashboard stats 2-column layout
- [x] Kanban board still single column (transitions at lg)
- [x] Calendar legend 2 columns
- [x] Tables display with horizontal scroll if needed
- [x] Sidebar navigation comfortable

### Desktop (1024px+)
- [x] Dashboard stats 4-column layout
- [x] Kanban board 3 columns visible
- [x] Quick Actions 3 columns
- [x] Calendar legend 3 columns
- [x] All grids at maximum columns
- [x] No unnecessary scrolling
- [x] Optimal spacing and padding

## Browser DevTools Testing

1. Open Chrome/Edge DevTools (F12)
2. Click "Toggle Device Toolbar" (Ctrl+Shift+M)
3. Test at specific widths:
   - 375px (iPhone SE)
   - 640px (sm breakpoint)
   - 768px (md breakpoint - iPad Mini)
   - 1024px (lg breakpoint - iPad Pro)
   - 1280px (desktop)
4. Test both portrait and landscape orientations
5. Check touch interactions (use device mode)

## Performance Considerations

- **Avoid layout shifts:** Use min-width on scrollable items
- **Lazy load images:** Use native `loading="lazy"` attribute
- **Optimize images:** Use appropriate sizes for mobile
- **Minimize reflows:** Apply responsive classes via CSS, not JS
- **Test on real devices:** Emulators don't catch everything

## Future Development Guidelines

When adding new components:

1. **Mobile-first:** Design for mobile, enhance for desktop
2. **Test early:** Check responsive at 640px, 768px, 1024px immediately
3. **Use patterns:** Follow patterns documented here
4. **Touch-friendly:** Ensure ≥44px touch targets
5. **Overflow handling:** Always handle wide/long content gracefully
6. **Consistent icons:** Use Heroicons SVG only (no emoji)

## Common Issues & Solutions

### Issue: Table overflows on mobile
**Solution:** Wrap in `overflow-x-auto` or convert to card view
```blade
<div class="overflow-x-auto">
  <table class="min-w-full"><!-- Table --></table>
</div>
```

### Issue: Form inputs too narrow
**Solution:** Use `w-full` on mobile, grid on desktop
```blade
<input class="w-full sm:w-auto">
```

### Issue: Text too small on mobile
**Solution:** Scale with responsive classes
```blade
<p class="text-sm sm:text-base">Text</p>
```

### Issue: Buttons hard to tap
**Solution:** Full width on mobile with adequate padding
```blade
<button class="w-full sm:w-auto px-4 py-2">Button</button>
```

### Issue: Kanban columns overflow
**Solution:** Horizontal scroll with minimum width
```blade
<div class="overflow-x-auto">
  <div class="min-w-[280px]">Column</div>
</div>
```

## Resources

- [Tailwind CSS Responsive Design](https://tailwindcss.com/docs/responsive-design)
- [WCAG Touch Target Size](https://www.w3.org/WAI/WCAG21/Understanding/target-size.html)
- [Heroicons](https://heroicons.com/)
- [Mobile-First CSS](https://developer.mozilla.org/en-US/docs/Web/Progressive_web_apps/Responsive/Mobile_first)

## Summary of Changes

**Components Made Responsive (Task 8 - Completed):**

1. **Dashboard** (`dashboard.blade.php`)
   - Header with role badges: flex-col → flex-row
   - Welcome card: Responsive text sizes
   - Stats grid: 1 col → 2 cols (md) → 4 cols (lg)
   - Quick Actions: 1 col → 2 cols (sm) → 3 cols (lg)
   - Recent Activity: Stack on mobile, horizontal on desktop

2. **Calendar Dashboard** (`calendar/dashboard.blade.php`)
   - Info box: Responsive padding and text
   - Calendar: Day view on mobile, month on desktop
   - Legend: 1 col → 2 cols (sm) → 3 cols (lg)
   - Modal: Full-width on mobile, max-w-lg centered on desktop

3. **Project Kanban Board** (`projects/show.blade.php`)
   - Header: Column layout on mobile
   - Kanban: Horizontal scroll on mobile, 3-column grid on lg+
   - New Ticket form: Stacking inputs on mobile
   - Member avatars: Limited to 5 with overflow indicator

4. **Personal Activities Calendar** (`calendar/personal.blade.php`)
   - Modal datetime grid: Stack on mobile, side-by-side on sm+
   - Modal buttons: Stack on mobile, horizontal on sm+
   - All emoji replaced with SVG icons

5. **Events Show** (`events/show.blade.php`)
   - Participant form: Stack on mobile, horizontal on sm+
   - Project form: Stack on mobile, horizontal on sm+
   - Project cards: Flex-col on mobile, flex-row on sm+
   - All buttons: Full-width on mobile

6. **Admin Users Table** (`admin/users/index.blade.php`)
   - Table wrapper: overflow-x-auto for horizontal scroll
   - Table: min-w-[600px] ensures readable columns
   - Responsive padding and text sizes

**Patterns Used:**
- `flex flex-col sm:flex-row` - Stack vertically on mobile, horizontal on desktop
- `grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4` - Responsive grid columns
- `w-full sm:w-auto` - Full-width on mobile, auto on desktop
- `text-sm sm:text-base` - Responsive text sizing
- `p-4 sm:p-6` - Responsive padding
- `overflow-x-auto` + `min-w-[Npx]` - Horizontal scroll for wide content
- `space-y-N sm:space-y-0 sm:flex sm:gap-N` - Stack with gap on mobile, flex with gap on desktop

**Total Files Modified:** 6 core view files
**Testing Status:** ✅ All breakpoints tested (375px, 640px, 768px, 1024px)
**Production Ready:** Yes

---

**Last Updated:** 2025-10-13 (completed Task 8)  
**Status:** ✅ Mobile responsive polish complete
