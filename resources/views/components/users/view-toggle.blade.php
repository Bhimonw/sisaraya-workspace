{{-- 
    View Toggle Component
    
    Toggle switch untuk beralih antara Grid dan Table view menggunakan Alpine.js.
    
    Props:
    - currentView (optional, default: 'grid'): Mode view default ('grid' atau 'table')
    
    Usage:
    1. Tambahkan x-data="{ viewMode: 'grid' }" di parent container
    2. Gunakan x-show="viewMode === 'grid'" dan x-show="viewMode === 'table'" di content
--}}

@props(['currentView' => 'grid'])

<div x-data="{ viewMode: '{{ $currentView }}' }" class="flex items-center gap-2">
    
    {{-- Label --}}
    <span class="text-sm font-medium text-gray-700">Tampilan:</span>
    
    {{-- Toggle Buttons --}}
    <div class="inline-flex rounded-lg border border-gray-300 bg-white p-1">
        
        {{-- Grid View Button --}}
        <button @click="viewMode = 'grid'" 
                :class="viewMode === 'grid' ? 'bg-violet-100 text-violet-700' : 'text-gray-600 hover:text-gray-900'"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md text-sm font-medium transition-all">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
            </svg>
            <span class="hidden sm:inline">Grid</span>
        </button>
        
        {{-- Table View Button --}}
        <button @click="viewMode = 'table'" 
                :class="viewMode === 'table' ? 'bg-violet-100 text-violet-700' : 'text-gray-600 hover:text-gray-900'"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md text-sm font-medium transition-all">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
            </svg>
            <span class="hidden sm:inline">Tabel</span>
        </button>
        
    </div>
    
    {{-- Slot untuk konten tambahan (optional) --}}
    {{ $slot }}
    
</div>
