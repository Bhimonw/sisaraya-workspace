@props(['selected' => null, 'name' => 'label', 'required' => false, 'showNone' => true])

@php
    $labels = \App\Models\Project::getLabels();
    $labelColors = [
        'UMKM' => ['bg' => 'bg-purple-50', 'border' => 'border-purple-300', 'text' => 'text-purple-700', 'ring' => 'ring-purple-500', 'selected' => 'bg-purple-100 border-purple-500'],
        'DIVISI' => ['bg' => 'bg-blue-50', 'border' => 'border-blue-300', 'text' => 'text-blue-700', 'ring' => 'ring-blue-500', 'selected' => 'bg-blue-100 border-blue-500'],
        'Kegiatan' => ['bg' => 'bg-green-50', 'border' => 'border-green-300', 'text' => 'text-green-700', 'ring' => 'ring-green-500', 'selected' => 'bg-green-100 border-green-500'],
    ];
@endphp

<div {{ $attributes->merge(['class' => 'space-y-3']) }}>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
        @foreach($labels as $labelOption)
            @php
                $colors = $labelColors[$labelOption] ?? [];
                $isSelected = $selected === $labelOption;
            @endphp
            <label class="relative cursor-pointer group">
                <input 
                    type="radio" 
                    name="{{ $name }}" 
                    value="{{ $labelOption }}"
                    {{ $isSelected ? 'checked' : '' }}
                    {{ $required && $loop->first && !$selected ? 'checked' : '' }}
                    class="sr-only peer"
                >
                <div class="flex items-center justify-center gap-2 px-4 py-3 border-2 rounded-xl transition-all duration-200
                            {{ $colors['bg'] }} {{ $colors['border'] }} {{ $colors['text'] }}
                            hover:shadow-md hover:scale-[1.02]
                            peer-checked:{{ $colors['selected'] }} peer-checked:ring-2 peer-checked:{{ $colors['ring'] }} peer-checked:shadow-lg">
                    <!-- Tag Icon -->
                    <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                    </svg>
                    
                    <span class="font-semibold text-sm">{{ $labelOption }}</span>
                    
                    <!-- Checkmark (shown when selected) -->
                    <svg class="h-4 w-4 ml-auto hidden peer-checked:inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
            </label>
        @endforeach
    </div>
    
    @if($showNone)
    <div class="flex items-center">
        <label class="flex items-center gap-2 cursor-pointer text-sm text-gray-600 hover:text-gray-800 transition-colors">
            <input 
                type="radio" 
                name="{{ $name }}" 
                value=""
                {{ !$selected ? 'checked' : '' }}
                class="h-4 w-4 text-gray-400 focus:ring-gray-300 border-gray-300"
            >
            <span>Tidak ada label</span>
        </label>
    </div>
    @endif
</div>
