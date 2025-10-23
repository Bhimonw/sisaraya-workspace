{{-- EVENTS TAB (Visible to All, but only PM/Admin can manage) --}}
<div x-show="activeTab === 'events'" x-transition>
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden" x-data="{ showEventForm: false }">
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <div>
                        <h2 class="text-lg font-semibold text-white">Event Proyek</h2>
                        <p class="text-sm text-white/90">Daftar acara dan kegiatan dalam proyek</p>
                    </div>
                </div>
                @if($project->canManage(Auth::user()))
                <button @click="showEventForm = !showEventForm" 
                        class="px-4 py-2 bg-white text-indigo-600 font-semibold rounded-lg hover:bg-indigo-50 hover:scale-105 active:scale-95 transition-all duration-300 shadow-lg">
                    <span x-show="!showEventForm" class="flex items-center gap-2">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Tambah Event
                    </span>
                    <span x-show="showEventForm" class="flex items-center gap-2">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Tutup
                    </span>
                </button>
                @endif
            </div>
        </div>
        
        <div class="p-6">
            {{-- Form Create Event (PM or Admin only) --}}
            @if($project->canManage(Auth::user()))
            <div x-show="showEventForm" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform -translate-y-2"
                 x-transition:enter-end="opacity-100 transform translate-y-0"
                 class="mb-6 p-6 bg-gradient-to-br from-indigo-50 to-purple-50 rounded-xl border-2 border-indigo-200 shadow-sm">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="h-5 w-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Buat Event Baru
                </h3>
                
                <form action="{{ route('projects.events.store', $project) }}" method="POST">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Event <span class="text-red-500">*</span></label>
                            <input name="title" required 
                                   class="w-full border-2 border-gray-300 p-3 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" 
                                   placeholder="Contoh: Workshop Desain Grafis" />
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Deskripsi</label>
                            <textarea name="description" rows="3" 
                                      class="w-full border-2 border-gray-300 p-3 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" 
                                      placeholder="Deskripsi event..."></textarea>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Mulai <span class="text-red-500">*</span></label>
                                <input type="date" name="start_date" required 
                                       class="w-full border-2 border-gray-300 p-3 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" />
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Selesai</label>
                                <input type="date" name="end_date" 
                                       class="w-full border-2 border-gray-300 p-3 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" />
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Jam Mulai</label>
                                <input type="time" name="start_time" 
                                       class="w-full border-2 border-gray-300 p-3 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" />
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Jam Selesai</label>
                                <input type="time" name="end_time" 
                                       class="w-full border-2 border-gray-300 p-3 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" />
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Lokasi</label>
                                <input name="location" 
                                       class="w-full border-2 border-gray-300 p-3 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" 
                                       placeholder="Zoom / Offline" />
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-3 mt-6">
                        <button type="submit" 
                                class="px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-semibold rounded-xl hover:shadow-lg hover:scale-105 active:scale-95 transition-all duration-300">
                            <span class="flex items-center gap-2">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                Buat Event
                            </span>
                        </button>
                        <button type="button" 
                                @click="showEventForm = false"
                                class="px-6 py-3 border-2 border-gray-300 text-gray-700 font-medium rounded-xl hover:bg-gray-50 transition">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
            @endif

            {{-- Event List --}}
            <div class="mb-4">
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide">
                    Daftar Event ({{ $project->events->count() }})
                </h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @forelse($project->events as $event)
                    <div class="group relative overflow-hidden rounded-xl border-2 border-indigo-200 bg-gradient-to-br from-indigo-50 to-purple-50 p-5 hover:border-indigo-400 hover:shadow-lg transition-all duration-300">
                        <div class="flex flex-col h-full">
                            {{-- Event Title --}}
                            <div class="mb-3">
                                <h4 class="font-bold text-lg text-gray-900 mb-1">{{ $event->title }}</h4>
                                @if($event->description)
                                    <p class="text-sm text-gray-600 line-clamp-2">{{ $event->description }}</p>
                                @endif
                            </div>
                            
                            {{-- Event Details --}}
                            <div class="space-y-2 flex-1">
                                <div class="flex items-start gap-2 text-sm text-gray-700">
                                    <svg class="h-4 w-4 mt-0.5 flex-shrink-0 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <div>
                                        <div class="font-semibold">{{ $event->start_date->format('d M Y') }}</div>
                                        @if($event->end_date)
                                            <div class="text-xs text-gray-500">s/d {{ $event->end_date->format('d M Y') }}</div>
                                        @endif
                                    </div>
                                </div>
                                
                                @if($event->start_time)
                                    <div class="flex items-center gap-2 text-sm text-gray-700">
                                        <svg class="h-4 w-4 flex-shrink-0 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span>{{ $event->start_time }}
                                        @if($event->end_time)
                                            - {{ $event->end_time }}
                                        @endif
                                        </span>
                                    </div>
                                @endif
                                
                                @if($event->location)
                                    <div class="flex items-center gap-2 text-sm text-gray-700">
                                        <svg class="h-4 w-4 flex-shrink-0 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        <span>{{ $event->location }}</span>
                                    </div>
                                @endif
                            </div>
                            
                            {{-- Action Buttons (PM Only) --}}
                            @if($project->canManage(Auth::user()))
                            <div class="mt-4 pt-4 border-t-2 border-indigo-200 flex gap-2">
                                <form method="POST" action="{{ route('project-events.destroy', $event) }}" 
                                      onsubmit="return confirm('Hapus event {{ $event->title }}?')" class="flex-1">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="w-full px-3 py-2 text-sm bg-red-600 text-white rounded-lg hover:bg-red-700 transition flex items-center justify-center gap-2">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        Hapus
                                    </button>
                                </form>
                            </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-16 px-6 bg-gradient-to-br from-gray-50 to-indigo-50 rounded-xl border-2 border-dashed border-gray-300">
                        <svg class="h-20 w-20 mx-auto mb-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <p class="text-gray-600 font-medium mb-2">Belum ada event</p>
                        <p class="text-sm text-gray-500">
                            @if($project->canManage(Auth::user()))
                                Klik tombol "Tambah Event" untuk membuat event baru
                            @else
                                Event akan ditampilkan di sini setelah dibuat oleh PM
                            @endif
                        </p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>{{-- End Events Tab --}}
