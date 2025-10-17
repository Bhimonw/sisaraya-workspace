@props(['status', 'size' => 'md'])

@php
    $statusConfig = [
        'draft' => [
            'label' => 'Draft',
            'bg' => 'bg-gray-100',
            'text' => 'text-gray-700',
            'border' => 'border-gray-300',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />'
        ],
        'pending' => [
            'label' => 'Menunggu',
            'bg' => 'bg-yellow-100',
            'text' => 'text-yellow-700',
            'border' => 'border-yellow-300',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />'
        ],
        'approved' => [
            'label' => 'Disetujui',
            'bg' => 'bg-green-100',
            'text' => 'text-green-700',
            'border' => 'border-green-300',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />'
        ],
        'rejected' => [
            'label' => 'Ditolak',
            'bg' => 'bg-red-100',
            'text' => 'text-red-700',
            'border' => 'border-red-300',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />'
        ]
    ];

    $sizeClasses = [
        'sm' => 'px-2 py-0.5 text-xs',
        'md' => 'px-3 py-1 text-sm',
        'lg' => 'px-4 py-1.5 text-base'
    ];

    $iconSizes = [
        'sm' => 'h-3 w-3',
        'md' => 'h-4 w-4',
        'lg' => 'h-5 w-5'
    ];

    $normalizedStatus = strtolower($status ?? 'draft');
    $config = $statusConfig[$normalizedStatus] ?? $statusConfig['draft'];
    $sizeClass = $sizeClasses[$size] ?? $sizeClasses['md'];
    $iconSize = $iconSizes[$size] ?? $iconSizes['md'];
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1.5 rounded-full font-semibold border {$config['bg']} {$config['text']} {$config['border']} {$sizeClass}"]) }}>
    <svg class="{{ $iconSize }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        {!! $config['icon'] !!}
    </svg>
    <span>{{ $config['label'] }}</span>
</span>
