# UI Pattern & Style Guide — SISARAYA Ruang Kerja

> **Panduan lengkap untuk konsistensi UI/UX di seluruh aplikasi SISARAYA**  
> Terakhir diperbarui: 17 Oktober 2025

---

## 📋 Table of Contents

1. [Overview](#overview)
2. [Design Principles](#design-principles)
3. [Color Palette](#color-palette)
4. [Card Components](#card-components)
5. [Button Styles](#button-styles)
6. [Header Patterns](#header-patterns)
7. [Grid Layouts](#grid-layouts)
8. [Status & Badges](#status--badges)
9. [Icons & SVG](#icons--svg)
10. [Responsive Breakpoints](#responsive-breakpoints)
11. [Animation & Transitions](#animation--transitions)
12. [Common Patterns](#common-patterns)

---

## Overview

Aplikasi SISARAYA menggunakan **modern card-based design** dengan konsistensi visual yang kuat. Semua halaman mengikuti pattern yang sama untuk memberikan pengalaman yang familier dan profesional.

### Tech Stack
- **Framework**: Laravel 12 + Blade Templates
- **CSS**: Tailwind CSS 3.x
- **Icons**: Heroicons (SVG)
- **Fonts**: System fonts (default)

---

## Design Principles

### 1. **Consistency First**
Semua halaman menggunakan pattern yang sama:
- Card-based grid layouts
- Gradient headers dengan warna kontekstual
- Hover effects yang smooth
- Responsive di semua device sizes

### 2. **Visual Hierarchy**
- **Primary Action**: Gradient buttons (top-right header)
- **Secondary Actions**: Card footer buttons
- **Tertiary Actions**: Text links dengan icons

### 3. **Progressive Disclosure**
- Informasi penting di atas fold
- Detail tambahan di dalam cards
- Empty states yang helpful

### 4. **Accessibility**
- Color contrast ratio minimal 4.5:1
- Focus states yang jelas
- Semantic HTML

---

## Color Palette

### Primary Colors (Per Module)

| Module | Primary | Secondary | Accent | Usage |
|--------|---------|-----------|--------|-------|
| **Projects** | `violet-600` | `blue-600` | `emerald-500` | PM, project management |
| **Businesses** | `blue-600` | `cyan-600` | `green-600` | Kewirausahaan module |
| **Tickets** | `indigo-600` | `purple-600` | `pink-600` | Task management |
| **Votes** | `purple-600` | `pink-600` | `fuchsia-600` | Voting system |
| **Documents** | `blue-600` | `cyan-600` | `sky-600` | Public docs |
| **Documents (Confidential)** | `red-600` | `pink-600` | `rose-600` | Secret docs |
| **Dashboard** | `indigo-600` | `purple-600` | `blue-600` | Main dashboard |

### Status Colors

| Status | Color | Background | Border |
|--------|-------|------------|--------|
| **Pending** | `yellow-700` | `yellow-100` | `yellow-200` |
| **Approved / Active** | `green-700` | `green-100` | `green-200` |
| **Rejected / Cancelled** | `red-700` | `red-100` | `red-200` |
| **Planning / Todo** | `gray-700` | `gray-100` | `gray-200` |
| **Doing / In Progress** | `blue-700` | `blue-100` | `blue-200` |
| **On Hold** | `yellow-700` | `yellow-100` | `yellow-200` |
| **Completed / Done** | `green-700` | `green-100` | `green-200` |

### Role Colors

| Role | Badge Color | Background |
|------|-------------|------------|
| **PM** | `violet-700` | `violet-100` |
| **Kewirausahaan** | `emerald-700` | `emerald-100` |
| **HR** | `blue-700` | `blue-100` |
| **Sekretaris** | `amber-700` | `amber-100` |
| **Finance** | `green-700` | `green-100` |
| **Default** | `gray-700` | `gray-100` |

---

## Card Components

### 1. **Standard Card**

```blade
<div class="group bg-white rounded-2xl shadow-sm border border-gray-200 
            hover:shadow-xl hover:border-{color}-200 transition-all duration-300 overflow-hidden">
    
    <!-- Gradient Header Strip (Optional) -->
    <div class="h-2 bg-gradient-to-r from-{color1}-600 via-{color2}-600 to-{color3}-500"></div>
    
    <!-- Card Header -->
    <div class="bg-gradient-to-r from-{color}-50 to-{color}-100 px-6 py-4 border-b border-{color}-200">
        <h3 class="font-bold text-gray-900 group-hover:text-{color}-600 transition-colors">
            Card Title
        </h3>
        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold 
                     bg-{color}-600 text-white">
            Badge
        </span>
    </div>
    
    <!-- Card Body -->
    <div class="p-6">
        <p class="text-sm text-gray-600 mb-4 line-clamp-2 leading-relaxed">
            Description text...
        </p>
        
        <!-- Meta Info -->
        <div class="space-y-2 mb-4 pb-4 border-b border-gray-100">
            <div class="flex items-center gap-2 text-sm">
                <div class="p-1.5 bg-gray-50 rounded-lg">
                    <svg class="h-4 w-4 text-gray-600">...</svg>
                </div>
                <span class="text-gray-600">Meta info</span>
            </div>
        </div>
        
        <!-- Action Button -->
        <a href="#" 
           class="w-full inline-flex items-center justify-center px-4 py-2.5 
                  bg-gradient-to-r from-{color1}-600 to-{color2}-600 text-white 
                  text-sm font-semibold rounded-xl hover:shadow-lg hover:scale-105 
                  active:scale-95 transition-all duration-300">
            Action Text
            <svg class="ml-2 h-4 w-4 group-hover:translate-x-1 transition-transform">...</svg>
        </a>
    </div>
</div>
```

**Key Features:**
- Rounded corners: `rounded-2xl`
- Shadow on hover: `shadow-sm` → `shadow-xl`
- Border color change on hover
- Gradient header backgrounds
- Icon-based meta information
- Smooth hover animations

### 2. **Stats Card**

```blade
<div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 
            hover:shadow-xl hover:border-{color}-300 transition-all duration-300">
    <div class="flex items-center justify-between mb-4">
        <div class="p-3 bg-gradient-to-br from-{color}-100 to-{color}-200 rounded-xl">
            <svg class="h-6 w-6 text-{color}-600">...</svg>
        </div>
    </div>
    <h3 class="text-2xl font-bold text-gray-900 mb-1">{{ $count }}</h3>
    <p class="text-sm text-gray-600">Label Text</p>
</div>
```

**Use Cases:**
- Dashboard statistics
- Quick metrics display
- Summary cards

---

## Button Styles

### 1. **Primary Button (Gradient)**

```blade
<a href="#" 
   class="inline-flex items-center justify-center px-6 py-3 
          bg-gradient-to-r from-{color1}-600 to-{color2}-600 
          text-white text-sm font-semibold rounded-full 
          hover:shadow-lg hover:scale-105 active:scale-95 
          transition-all duration-300 shadow-md">
    <svg class="h-5 w-5 mr-2">...</svg>
    Button Text
</a>
```

**Locations:**
- Page headers (top-right)
- Primary CTAs
- Create/Submit actions

### 2. **Secondary Button**

```blade
<button class="px-4 py-2 bg-gray-600 text-white rounded-xl 
               hover:bg-gray-700 hover:shadow-lg transition-all duration-300">
    Button Text
</button>
```

### 3. **Text Link Button**

```blade
<a href="#" 
   class="inline-flex items-center gap-2 text-sm font-medium 
          text-blue-600 hover:text-blue-700 transition-colors">
    Link Text
    <svg class="h-4 w-4">...</svg>
</a>
```

---

## Header Patterns

### Standard Page Header

```blade
<div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <!-- Left: Title & Description -->
    <div class="flex items-center gap-4">
        <div class="p-3 bg-gradient-to-br from-{color1}-600 to-{color2}-600 rounded-xl shadow-lg">
            <svg class="h-8 w-8 text-white">...</svg>
        </div>
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Page Title</h1>
            <p class="text-gray-600 mt-1">Subtitle or description</p>
        </div>
    </div>
    
    <!-- Right: Primary Action -->
    <a href="#" class="inline-flex items-center justify-center px-6 py-3 
                       bg-gradient-to-r from-{color1}-600 to-{color2}-600 
                       text-white text-sm font-semibold rounded-full 
                       hover:shadow-lg hover:scale-105 active:scale-95 
                       transition-all duration-300 shadow-md">
        <svg class="h-5 w-5 mr-2">...</svg>
        Action Button
    </a>
</div>
```

**Components:**
- Icon badge (gradient background, rounded-xl)
- Title (text-3xl, font-bold)
- Subtitle (text-gray-600)
- Primary action button (gradient, rounded-full)

---

## Grid Layouts

### Responsive Grid Pattern

```blade
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @foreach($items as $item)
        <!-- Card component here -->
    @endforeach
</div>
```

### Grid Variations

| Type | Mobile | Tablet | Desktop | Use Case |
|------|--------|--------|---------|----------|
| **Default** | 1 col | 2 cols | 3 cols | Cards, projects, votes |
| **Stats** | 1 col | 2 cols | 5 cols | Dashboard metrics |
| **Wide** | 1 col | 2 cols | 2 cols | Large cards, documents |
| **Narrow** | 1 col | 3 cols | 4 cols | Small items, tags |

**Breakpoints:**
- Mobile: `grid-cols-1` (< 768px)
- Tablet: `md:grid-cols-2` (≥ 768px)
- Desktop: `lg:grid-cols-3` (≥ 1024px)

**Gap Sizes:**
- Default: `gap-6` (1.5rem / 24px)
- Compact: `gap-4` (1rem / 16px)
- Spacious: `gap-8` (2rem / 32px)

---

## Status & Badges

### Standard Badge

```blade
<span class="inline-flex items-center px-3 py-1 rounded-full 
             text-xs font-semibold bg-{color}-100 text-{color}-700 
             border border-{color}-200">
    <svg class="h-3 w-3 mr-1">...</svg>
    Badge Text
</span>
```

### Badge with Solid Background

```blade
<span class="inline-flex items-center px-3 py-1 rounded-full 
             text-xs font-semibold bg-{color}-600 text-white">
    <svg class="h-3 w-3 mr-1">...</svg>
    Badge Text
</span>
```

### Counter Badge

```blade
<span class="inline-flex items-center px-2.5 py-0.5 rounded-full 
             text-xs font-medium bg-{color}-100 text-{color}-700">
    42
</span>
```

---

## Icons & SVG

### Icon Container Pattern

```blade
<div class="p-1.5 bg-{color}-50 rounded-lg">
    <svg class="h-4 w-4 text-{color}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="..."/>
    </svg>
</div>
```

### Icon Sizes

| Size | Class | Pixel | Use Case |
|------|-------|-------|----------|
| **XS** | `h-3 w-3` | 12px | Badge icons, small indicators |
| **SM** | `h-4 w-4` | 16px | Meta info, list items |
| **MD** | `h-5 w-5` | 20px | Buttons, section headers |
| **LG** | `h-6 w-6` | 24px | Stats cards, large buttons |
| **XL** | `h-8 w-8` | 32px | Page headers, empty states |
| **2XL** | `h-10 w-10` | 40px | Large empty states |

### Common Icons Reference

| Icon | Path | Use Case |
|------|------|----------|
| **Folder** | `M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z` | Projects |
| **Checklist** | `M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4` | Votes, tickets |
| **Document** | `M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z` | Documents |
| **User** | `M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z` | Creator, users |
| **Calendar** | `M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z` | Dates, events |
| **Lightning** | `M13 10V3L4 14h7v7l9-11h-7z` | Active status |
| **Check Circle** | `M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z` | Approved, completed |
| **Clock** | `M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z` | Pending, waiting |

---

## Responsive Breakpoints

### Tailwind Breakpoints

| Breakpoint | Min Width | Prefix | Example |
|------------|-----------|--------|---------|
| **Mobile** | Default | (none) | `grid-cols-1` |
| **SM** | 640px | `sm:` | `sm:grid-cols-2` |
| **MD** | 768px | `md:` | `md:grid-cols-2` |
| **LG** | 1024px | `lg:` | `lg:grid-cols-3` |
| **XL** | 1280px | `xl:` | `xl:grid-cols-4` |
| **2XL** | 1536px | `2xl:` | `2xl:grid-cols-5` |

### Common Responsive Patterns

```blade
<!-- Flex direction: column → row -->
<div class="flex flex-col sm:flex-row gap-4">

<!-- Grid: 1 → 2 → 3 columns -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

<!-- Hide on mobile, show on desktop -->
<div class="hidden lg:block">

<!-- Show on mobile, hide on desktop -->
<div class="block lg:hidden">

<!-- Text size responsive -->
<h1 class="text-2xl sm:text-3xl lg:text-4xl">

<!-- Padding responsive -->
<div class="p-4 md:p-6 lg:p-8">
```

---

## Animation & Transitions

### Standard Transitions

```blade
<!-- All properties -->
transition-all duration-300

<!-- Specific properties -->
transition-colors duration-200
transition-shadow duration-300
transition-transform duration-300
```

### Hover Effects

#### Card Hover
```blade
hover:shadow-xl hover:border-{color}-200 transition-all duration-300
```

#### Button Hover (Scale)
```blade
hover:scale-105 active:scale-95 transition-all duration-300
```

#### Icon Slide (Right)
```blade
group-hover:translate-x-1 transition-transform duration-300
```

#### Icon Slide (Down)
```blade
group-hover:translate-y-1 transition-transform duration-300
```

#### Color Change
```blade
hover:text-{color}-600 transition-colors duration-200
```

### Group Hover Pattern

```blade
<div class="group ...">
    <h3 class="group-hover:text-violet-600 transition-colors">Title</h3>
    <svg class="group-hover:translate-x-1 transition-transform">...</svg>
</div>
```

---

## Common Patterns

### 1. **Filter Tabs**

```blade
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
    <nav class="flex">
        <a href="?filter=all" 
           class="flex-1 text-center py-4 px-6 border-b-2 font-medium text-sm 
                  transition-colors
                  {{ $filter === 'all' ? 'border-blue-600 text-blue-600 bg-blue-50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50' }}">
            <div class="flex items-center justify-center gap-2">
                <svg class="h-5 w-5">...</svg>
                <span>Semua</span>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                             {{ $filter === 'all' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-600' }}">
                    {{ $count }}
                </span>
            </div>
        </a>
        <!-- More tabs... -->
    </nav>
</div>
```

### 2. **Empty State**

```blade
<div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-16 text-center">
    <div class="max-w-md mx-auto">
        <!-- Icon -->
        <div class="w-20 h-20 mx-auto mb-6 bg-gradient-to-br from-{color}-100 to-{color}-100 
                    rounded-full flex items-center justify-center">
            <svg class="h-10 w-10 text-{color}-600">...</svg>
        </div>
        
        <!-- Title -->
        <h3 class="text-xl font-bold text-gray-900 mb-2">Belum Ada Data</h3>
        
        <!-- Description -->
        <p class="text-gray-600 mb-6">Deskripsi empty state...</p>
        
        <!-- CTA -->
        <a href="#" 
           class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-{color1}-600 to-{color2}-600 
                  text-white text-sm font-semibold rounded-full hover:shadow-lg transition-all duration-300">
            <svg class="h-5 w-5 mr-2">...</svg>
            Action Button
        </a>
    </div>
</div>
```

### 3. **Section Header**

```blade
<div class="mb-6 flex items-center justify-between">
    <!-- Left: Title with icon -->
    <div class="flex items-center gap-3">
        <div class="p-2 bg-{color}-100 rounded-lg">
            <svg class="h-5 w-5 text-{color}-600">...</svg>
        </div>
        <div>
            <h2 class="text-xl font-bold text-gray-900">Section Title</h2>
            <p class="text-sm text-gray-600">{{ $count }} items</p>
        </div>
    </div>
    
    <!-- Right: Action link -->
    <a href="#" 
       class="inline-flex items-center gap-2 text-sm font-medium 
              text-blue-600 hover:text-blue-700 transition-colors">
        Lihat Semua
        <svg class="h-4 w-4">...</svg>
    </a>
</div>
```

### 4. **Success Alert**

```blade
<div class="mb-6 rounded-xl bg-green-50 border border-green-200 p-4 
            text-green-800 flex items-center gap-3">
    <svg class="h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
              d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
    {{ $message }}
</div>
```

### 5. **Meta Info List**

```blade
<div class="space-y-2 mb-4 pb-4 border-b border-gray-100">
    <!-- User -->
    <div class="flex items-center gap-2 text-sm">
        <div class="p-1.5 bg-gray-50 rounded-lg">
            <svg class="h-4 w-4 text-gray-600">...</svg>
        </div>
        <span class="text-gray-600">{{ $user->name }}</span>
    </div>
    
    <!-- Date -->
    <div class="flex items-center gap-2 text-sm">
        <div class="p-1.5 bg-gray-50 rounded-lg">
            <svg class="h-4 w-4 text-gray-600">...</svg>
        </div>
        <span class="text-gray-600">{{ $date }}</span>
    </div>
    
    <!-- Count -->
    <div class="flex items-center gap-2 text-sm">
        <div class="p-1.5 bg-blue-50 rounded-lg">
            <svg class="h-4 w-4 text-blue-600">...</svg>
        </div>
        <span class="font-semibold text-gray-900">{{ $count }}</span>
        <span class="text-gray-600">Label</span>
    </div>
</div>
```

---

## Implementation Checklist

Saat membuat halaman baru atau update UI, pastikan:

### Page Header ✓
- [ ] Icon badge dengan gradient background
- [ ] Title (text-3xl font-bold)
- [ ] Subtitle/description (text-gray-600)
- [ ] Primary action button (gradient, rounded-full)
- [ ] Responsive (flex-col → flex-row)

### Card Grid ✓
- [ ] Responsive grid (1 → 2 → 3 columns)
- [ ] Gap spacing (gap-6)
- [ ] Card rounded-2xl
- [ ] Shadow on hover (shadow-sm → shadow-xl)
- [ ] Border color change on hover

### Card Structure ✓
- [ ] Gradient header (from-{color}-50 to-{color}-100)
- [ ] Status badge dengan icon
- [ ] Description dengan line-clamp-2/3
- [ ] Meta info dengan icon containers
- [ ] Action button dengan gradient
- [ ] Hover effects (group-hover pattern)

### Interactions ✓
- [ ] Smooth transitions (duration-300)
- [ ] Hover scale effects (hover:scale-105)
- [ ] Icon slide animations (translate-x-1)
- [ ] Color transitions (transition-colors)

### Empty States ✓
- [ ] Icon dengan gradient background
- [ ] Clear title dan description
- [ ] Call-to-action button
- [ ] Centered layout (max-w-md mx-auto)

### Accessibility ✓
- [ ] Color contrast ratio ≥ 4.5:1
- [ ] Focus states visible
- [ ] Semantic HTML tags
- [ ] Alt text untuk images (jika ada)

---

## Quick Reference

### Most Used Classes

```blade
<!-- Card Base -->
bg-white rounded-2xl shadow-sm border border-gray-200 p-6

<!-- Gradient Header -->
bg-gradient-to-r from-{color1}-50 to-{color2}-100

<!-- Gradient Button -->
bg-gradient-to-r from-{color1}-600 to-{color2}-600

<!-- Badge -->
px-3 py-1 rounded-full text-xs font-semibold bg-{color}-100 text-{color}-700

<!-- Icon Container -->
p-1.5 bg-{color}-50 rounded-lg

<!-- Text Truncate -->
line-clamp-2 (or line-clamp-3)

<!-- Hover Effects -->
hover:shadow-xl hover:border-{color}-200 transition-all duration-300

<!-- Group Hover -->
group-hover:text-{color}-600 transition-colors
group-hover:translate-x-1 transition-transform
```

---

## Examples by Module

### Projects
- **Colors**: violet-600, blue-600, emerald-500
- **Pattern**: Card grid dengan gradient strip, status badges
- **File**: `resources/views/projects/index.blade.php`

### Businesses
- **Colors**: blue-600, cyan-600, green-600 (per status)
- **Pattern**: Card grid dengan status-based gradient headers
- **File**: `resources/views/businesses/index.blade.php`

### Votes
- **Colors**: purple-600, pink-600
- **Pattern**: Active/Closed sections, badge indicators
- **File**: `resources/views/votes/index.blade.php`

### Documents
- **Colors**: blue-600 (public), red-600 (confidential)
- **Pattern**: Tab navigation, confidential badges
- **File**: `resources/views/documents/index.blade.php`

### Dashboard
- **Colors**: indigo-600, purple-600
- **Pattern**: Stats cards (5 cols), ticket/project grids
- **File**: `resources/views/dashboard/index.blade.php`

---

## Maintenance Notes

### When Adding New Modules:
1. Choose primary color from palette
2. Use standard card pattern
3. Follow responsive grid (1-2-3 columns)
4. Include empty state
5. Add to this guide

### When Updating UI:
1. Keep existing animations
2. Maintain color consistency
3. Test all breakpoints
4. Update documentation

### Common Pitfalls to Avoid:
- ❌ Mixing different card styles
- ❌ Inconsistent spacing (use gap-6)
- ❌ Wrong transition durations
- ❌ Non-responsive layouts
- ❌ Missing hover effects
- ❌ Inconsistent button styles

---

## Questions?

Jika ada pertanyaan tentang UI patterns atau butuh custom component, refer to:
- Tailwind docs: https://tailwindcss.com/docs
- Heroicons: https://heroicons.com
- Existing implementations di `resources/views/`

**Last Updated**: 17 Oktober 2025  
**Maintainer**: Development Team SISARAYA
