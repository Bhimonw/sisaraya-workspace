{{-- 
    Page Header Component
    
    Header untuk halaman dengan title, description, dan optional action button.
    
    Props:
    - title (optional, default: 'Page Title'): Judul halaman
    - description (optional, default: null): Deskripsi/subtitle halaman
    - actionUrl (optional, default: null): URL untuk action button
    - actionText (optional, default: 'Tambah Baru'): Text untuk action button
    - actionIcon (optional, default: true): Tampilkan icon plus
    - showAction (optional, default: true): Tampilkan action button
--}}

@props([
    'title' => 'Page Title',
    'description' => null,
    'actionUrl' => null,
    'actionText' => 'Tambah Baru',
    'actionIcon' => true,
    'showAction' => true
])

<div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    
    {{-- LEFT: Title & Description --}}
    <div>
        <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $title }}</h1>
        @if($description)
        <p class="text-gray-600">{{ $description }}</p>
        @endif
    </div>
    
    {{-- RIGHT: Action Button --}}
    @if($showAction && $actionUrl)
    <a href="{{ $actionUrl }}" 
       class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-violet-600 to-blue-600 text-white font-semibold rounded-xl hover:shadow-lg hover:scale-105 active:scale-95 transition-all duration-300">
        @if($actionIcon)
        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        @endif
        {{ $actionText }}
    </a>
    @endif
    
</div>
