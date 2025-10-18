@props(['status'])

@php
$statusConfig = [
    'planning' => [
        'label' => 'Perencanaan',
        'color' => 'gray',
        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>',
    ],
    'active' => [
        'label' => 'Aktif',
        'color' => 'blue',
        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>',
    ],
    'on_hold' => [
        'label' => 'Tertunda',
        'color' => 'yellow',
        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"/>',
    ],
    'completed' => [
        'label' => 'Selesai',
        'color' => 'green',
        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>',
    ],
    'blackout' => [
        'label' => 'Blackout',
        'color' => 'red',
        'icon' => '<circle cx="12" cy="12" r="10" fill="currentColor"/>',
    ],
];

$config = $statusConfig[$status] ?? $statusConfig['planning'];
$color = $config['color'];
$label = $config['label'];
$icon = $config['icon'];

$colorClasses = match($color) {
    'gray' => 'bg-gray-100 text-gray-800 border-gray-300',
    'blue' => 'bg-blue-100 text-blue-800 border-blue-300',
    'yellow' => 'bg-yellow-100 text-yellow-800 border-yellow-300',
    'green' => 'bg-green-100 text-green-800 border-green-300',
    'red' => 'bg-red-100 text-red-800 border-red-300',
    default => 'bg-gray-100 text-gray-800 border-gray-300',
};
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold border {$colorClasses}"]) }}>
    <svg class="h-3.5 w-3.5" fill="{{ $status === 'blackout' ? 'currentColor' : 'none' }}" stroke="{{ $status === 'blackout' ? 'none' : 'currentColor' }}" viewBox="0 0 24 24">
        {!! $icon !!}
    </svg>
    <span>{{ $label }}</span>
</span>
