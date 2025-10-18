# Back Button Component

## Overview
Komponen reusable untuk tombol "Kembali" yang konsisten di seluruh aplikasi.

## Component Location
`resources/views/components/back-button.blade.php`

## Usage

### Basic Usage
```blade
<x-back-button />
```
Secara default akan kembali ke halaman sebelumnya (`url()->previous()`).

### With Custom URL
```blade
<x-back-button :url="route('dashboard')" />
```

### With Custom Text
```blade
<x-back-button text="Kembali ke Dashboard" />
```

### With Both Custom URL and Text
```blade
<x-back-button :url="route('projects.index')" text="Kembali ke Proyek" />
```

## Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `url` | string | `url()->previous()` | URL tujuan untuk navigasi kembali |
| `text` | string | `'Kembali'` | Text yang ditampilkan di button |

## Styling Features

- **Hover Effect**: Background berubah dari white ke gray-50
- **Border**: Border gray-300 yang menjadi gray-400 saat hover
- **Shadow**: Subtle shadow yang meningkat saat hover
- **Icon Animation**: Icon berubah warna saat hover (gray-500 → gray-700)
- **Transition**: Smooth transition untuk semua efek (200ms duration)
- **Responsive**: Inline-flex layout dengan gap untuk icon dan text

## Design Tokens

- Background: `bg-white` → `hover:bg-gray-50`
- Border: `border-gray-300` → `hover:border-gray-400`
- Text: `text-gray-700`
- Icon: `text-gray-500` → `group-hover:text-gray-700`
- Padding: `px-4 py-2`
- Border Radius: `rounded-lg`
- Shadow: `shadow-sm` → `hover:shadow`

## Examples in Production

### Admin Users Index
```blade
<x-back-button url="{{ route('dashboard') }}" />
```

### Admin Users Create/Edit
```blade
<x-back-button :url="route('admin.users.index')" />
```

### Role Requests Create
```blade
<x-back-button url="{{ route('dashboard') }}" />
```

### Admin Users Manage Roles
```blade
<x-back-button :url="route('admin.users.index')" />
```

## Benefits

1. **Consistency**: Semua tombol kembali memiliki style yang sama di seluruh aplikasi
2. **Maintainability**: Perubahan style hanya perlu dilakukan di satu tempat
3. **Flexibility**: Dapat custom URL dan text sesuai kebutuhan
4. **Default Behavior**: Jika tidak ada URL, otomatis kembali ke halaman sebelumnya
5. **Accessibility**: Semantic HTML dengan proper anchor tag
6. **UX**: Visual feedback yang jelas dengan hover states

## Component Code

```blade
@props(['url' => null, 'text' => 'Kembali'])

@php
    $backUrl = $url ?? url()->previous();
@endphp

<a href="{{ $backUrl }}" 
   class="inline-flex items-center gap-2 px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 shadow-sm hover:shadow group">
    <svg class="h-5 w-5 text-gray-500 group-hover:text-gray-700 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
    </svg>
    <span class="font-medium">{{ $text }}</span>
</a>
```

## Migration Guide

### Before (Custom Implementation)
```blade
<a href="{{ route('admin.users.index') }}" 
   class="inline-flex items-center text-gray-600 hover:text-gray-900">
    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
    </svg>
    Kembali
</a>
```

### After (Component Usage)
```blade
<x-back-button :url="route('admin.users.index')" />
```

## Future Enhancements

- [ ] Add optional icon customization
- [ ] Add size variants (sm, md, lg)
- [ ] Add color variants (primary, secondary, danger)
- [ ] Add loading state for SPA navigation
- [ ] Add keyboard shortcuts (ESC key)
