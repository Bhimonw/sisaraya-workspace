{{-- 
    Role Badge Component
    
    Menampilkan badge role dengan warna yang konsisten untuk setiap role.
    
    Props:
    - role (required): Nama role (string)
    
    Available Roles & Colors:
    - pm: Violet
    - hr: Blue
    - sekretaris: Amber
    - bendahara: Green
    - bisnis_manager: Emerald
    - media: Pink
    - pr: Indigo
    - researcher: Teal
    - talent_manager: Cyan
    - talent: Lime
    - member: Gray (default)
--}}

@props(['role'])

@php
// Mapping role ke warna
$roleColors = [
    'pm' => 'bg-violet-100 text-violet-700 border-violet-200',
    'hr' => 'bg-blue-100 text-blue-700 border-blue-200',
    'sekretaris' => 'bg-amber-100 text-amber-700 border-amber-200',
    'bendahara' => 'bg-green-100 text-green-700 border-green-200',
    'bisnis_manager' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
    'media' => 'bg-pink-100 text-pink-700 border-pink-200',
    'pr' => 'bg-indigo-100 text-indigo-700 border-indigo-200',
    'researcher' => 'bg-teal-100 text-teal-700 border-teal-200',
    'talent_manager' => 'bg-cyan-100 text-cyan-700 border-cyan-200',
    'talent' => 'bg-lime-100 text-lime-700 border-lime-200',
    'member' => 'bg-gray-100 text-gray-700 border-gray-200',
];

// Fallback ke gray jika role tidak dikenali
$colorClass = $roleColors[$role] ?? 'bg-gray-100 text-gray-700 border-gray-200';
@endphp

<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold border {{ $colorClass }}">
    {{ strtoupper($role) }}
</span>
