{{-- Events Section - Quick View Only --}}
<div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-4 py-3">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <h3 class="font-semibold text-white">Event Mendatang</h3>
                <span class="text-xs px-2 py-0.5 bg-white/20 rounded-full text-white">{{ $project->events->count() }}</span>
            </div>
            <button @click="activeTab = 'events'" class="text-xs px-3 py-1.5 bg-white text-indigo-600 rounded-lg hover:bg-indigo-50 transition font-medium">
                Lihat Semua →
            </button>
        </div>
    </div>
    
    <div class="p-4">
        {{-- List Events (Max 3) --}}
        <div class="space-y-3 max-h-[400px] overflow-y-auto">
            @forelse($project->events->take(3) as $event)
                <div class="border border-indigo-200 rounded p-3 bg-indigo-50 hover:bg-indigo-100 transition cursor-pointer" 
                     @click="activeTab = 'events'">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex-1">
                            <h4 class="font-semibold text-base">{{ $event->title }}</h4>
                            
                            <div class="flex flex-wrap gap-2 mt-2 text-xs text-gray-600">
                                <div class="flex items-center gap-1">
                                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    {{ $event->start_date->format('d M Y') }}
                                </div>
                                
                                @if($event->start_time)
                                    <div class="flex items-center gap-1">
                                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        {{ $event->start_time }}
                                    </div>
                                @endif
                                
                                @if($event->location)
                                    <div class="flex items-center gap-1">
                                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        {{ $event->location }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-8 text-gray-500">
                    <svg class="h-12 w-12 mx-auto text-gray-400 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <p class="text-sm">Belum ada event.</p>
                    @if($project->canManage(Auth::user()))
                    <button @click="activeTab = 'events'" class="mt-2 text-xs text-indigo-600 hover:underline">
                        + Buat Event Baru
                    </button>
                    @endif
                </div>
            @endforelse
            
            @if($project->events->count() > 3)
            <div class="text-center pt-2">
                <button @click="activeTab = 'events'" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                    Lihat {{ $project->events->count() - 3 }} event lainnya →
                </button>
            </div>
            @endif
        </div>
    </div>
</div>{{-- End Event Section --}}
