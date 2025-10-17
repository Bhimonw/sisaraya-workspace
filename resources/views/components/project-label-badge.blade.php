@props(['label' => null, 'size' => 'md'])

@php
    $labelColor = \App\Models\Project::getLabelColor($label);
    $labelColorClasses = [
        'purple' => 'bg-purple-100 text-purple-700 border-purple-300',
        'blue' => 'bg-blue-100 text-blue-700 border-blue-300',
        'green' => 'bg-green-100 text-green-700 border-green-300',
        'gray' => 'bg-gray-100 text-gray-600 border-gray-300',
    ];
    
    $sizeClasses = [
        'sm' => 'px-2 py-0.5 text-xs',
        'md' => 'px-3 py-1 text-sm',
        'lg' => 'px-4 py-1.5 text-base',
    ];
    
    $iconSizes = [
        'sm' => 'h-3 w-3',
        'md' => 'h-3.5 w-3.5',
        'lg' => 'h-4 w-4',
    ];
@endphp

@if($label)
<span {{ $attributes->merge(['class' => 'inline-flex items-center font-medium rounded-full border ' . ($labelColorClasses[$labelColor] ?? $labelColorClasses['gray']) . ' ' . ($sizeClasses[$size] ?? $sizeClasses['md'])]) }}>
    <svg class="{{ $iconSizes[$size] ?? $iconSizes['md'] }} mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
    </svg>
    {{ $label }}
</span>
@endif
