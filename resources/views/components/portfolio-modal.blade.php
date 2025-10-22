@props(['id', 'title', 'description', 'date', 'category', 'tags' => [], 'images' => [], 'details' => ''])

<div x-data="{ open: false }" x-cloak>
    <!-- Trigger Button -->
    <button @click="open = true" class="group w-full px-6 py-3 bg-gradient-to-r from-violet-600 to-blue-600 text-white font-semibold rounded-lg hover:shadow-xl hover:scale-[1.02] transition-all duration-300 flex items-center justify-center gap-2">
        <span>Lihat Detail</span>
        <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
        </svg>
    </button>

    <!-- Modal Overlay -->
    <div x-show="open" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto" 
         aria-labelledby="modal-title" 
         role="dialog" 
         aria-modal="true"
         @keydown.escape.window="open = false"
         style="display: none;"
         x-show.important="open">
        
        <!-- Background Overlay -->
        <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity backdrop-blur-sm" @click="open = false"></div>

        <!-- Modal Content -->
        <div class="flex min-h-full items-center justify-center p-4 sm:p-6">
            <div x-show="open"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-8 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-8 sm:scale-95"
                 class="relative bg-white rounded-2xl shadow-2xl max-w-4xl w-full overflow-hidden"
                 @click.stop>
                
                <!-- Close Button -->
                <button @click="open = false" 
                        class="absolute top-4 right-4 z-10 p-3 bg-white/95 hover:bg-red-500 rounded-full shadow-xl transition-all duration-200 group hover:scale-110 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                        title="Tutup (ESC)">
                    <svg class="w-5 h-5 text-gray-700 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>

                <!-- Scrollable Content -->
                <div class="overflow-y-auto max-h-[85vh] scroll-smooth">
                    <!-- Scroll Indicator -->
                    <div class="sticky top-0 left-0 right-0 h-1 bg-gray-200 z-10">
                        <div class="h-full bg-gradient-to-r from-violet-600 to-blue-600 transition-all duration-300" 
                             x-data="{ scroll: 0 }"
                             x-init="$el.parentElement.parentElement.addEventListener('scroll', (e) => { 
                                 const el = e.target;
                                 const scrollPercent = (el.scrollTop / (el.scrollHeight - el.clientHeight)) * 100;
                                 scroll = scrollPercent;
                             })"
                             :style="`width: ${scroll}%`">
                        </div>
                    </div>
                    <!-- Header Image -->
                    <div class="relative h-64 bg-gradient-to-br from-violet-500 to-blue-500">
                        @if(count($images) > 0)
                            <img src="{{ $images[0] }}" alt="{{ $title }}" class="w-full h-full object-cover">
                        @else
                            <div class="absolute inset-0 flex items-center justify-center">
                                <svg class="w-24 h-24 text-white/30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                        @endif
                        
                        <!-- Category Badge -->
                        <div class="absolute top-4 left-4">
                            <span class="px-4 py-2 bg-white/20 backdrop-blur-sm text-white text-sm font-semibold rounded-full border border-white/30">
                                {{ $category }}
                            </span>
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="p-6 sm:p-8">
                        <!-- Title & Date -->
                        <div class="mb-6 animate-fadeIn">
                            <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-3 leading-tight">{{ $title }}</h2>
                            <div class="flex items-center text-gray-600 bg-gray-50 px-4 py-2 rounded-lg w-fit">
                                <svg class="w-5 h-5 mr-2 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <span class="font-semibold">{{ $date }}</span>
                            </div>
                        </div>

                        <!-- Tags -->
                        @if(count($tags) > 0)
                        <div class="flex flex-wrap gap-2 mb-6">
                            @foreach($tags as $tag)
                            <span class="px-3 py-1 bg-violet-100 text-violet-600 text-sm font-semibold rounded-full">
                                {{ $tag }}
                            </span>
                            @endforeach
                        </div>
                        @endif

                        <!-- Description -->
                        <div class="mb-6 bg-gradient-to-br from-violet-50 to-blue-50 p-6 rounded-xl border border-violet-100">
                            <div class="flex items-start gap-3 mb-3">
                                <svg class="w-6 h-6 text-violet-600 flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900 mb-2">Deskripsi</h3>
                                    <p class="text-gray-700 leading-relaxed">{{ $description }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Details -->
                        @if($details)
                        <div class="mb-6 bg-white border border-gray-200 p-6 rounded-xl">
                            <div class="flex items-start gap-3">
                                <svg class="w-6 h-6 text-blue-600 flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <div class="flex-1">
                                    <h3 class="text-lg font-bold text-gray-900 mb-3">Detail Event</h3>
                                    <div class="prose max-w-none text-gray-700 leading-relaxed whitespace-pre-line">{{ $details }}</div>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Image Gallery -->
                        @if(count($images) > 1)
                        <div class="mb-6">
                            <div class="flex items-center gap-3 mb-4">
                                <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <h3 class="text-lg font-bold text-gray-900">Galeri</h3>
                            </div>
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-3 sm:gap-4">
                                @foreach(array_slice($images, 1) as $image)
                                <div class="aspect-square rounded-xl overflow-hidden shadow-md hover:shadow-xl transition-shadow cursor-pointer group">
                                    <img src="{{ $image }}" alt="Gallery image" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <!-- Action Buttons -->
                        <div class="sticky bottom-0 bg-white pt-6 pb-2 border-t border-gray-200">
                            <div class="flex flex-col sm:flex-row gap-3">
                                <button @click="open = false" 
                                        class="flex-1 px-6 py-3 bg-gray-100 text-gray-700 font-semibold rounded-lg hover:bg-gray-200 transition-all duration-200 flex items-center justify-center gap-2 group border border-gray-300">
                                    <svg class="w-5 h-5 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                                    </svg>
                                    <span>Tutup</span>
                                </button>
                                <a href="{{ url('/') }}#contact" 
                                   class="flex-1 px-6 py-3 bg-gradient-to-r from-violet-600 to-blue-600 text-white font-semibold rounded-lg hover:shadow-xl hover:scale-[1.02] transition-all duration-200 flex items-center justify-center gap-2 group">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                    </svg>
                                    <span>Hubungi Kami</span>
                                    <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </a>
                            </div>
                            <p class="text-center text-sm text-gray-500 mt-3">
                                Tekan <kbd class="px-2 py-1 bg-gray-200 rounded text-xs font-mono">ESC</kbd> untuk menutup
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .animate-fadeIn {
        animation: fadeIn 0.5s ease-out;
    }
    
    kbd {
        box-shadow: 0 2px 0 0 rgba(0,0,0,0.1);
        border: 1px solid #e5e7eb;
    }
    
    /* Custom scrollbar */
    .overflow-y-auto::-webkit-scrollbar {
        width: 8px;
    }
    
    .overflow-y-auto::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    
    .overflow-y-auto::-webkit-scrollbar-thumb {
        background: linear-gradient(to bottom, #7c3aed, #2563eb);
        border-radius: 10px;
    }
    
    .overflow-y-auto::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(to bottom, #6d28d9, #1d4ed8);
    }
</style>
