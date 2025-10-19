@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header Section -->
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="p-3 bg-gradient-to-br from-violet-600 to-blue-600 rounded-xl shadow-lg">
                <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                </svg>
            </div>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Manajemen Proyek</h1>
                <p class="text-gray-600 mt-1">Kelola semua proyek komunitas SISARAYA</p>
            </div>
        </div>
        
        @role('pm')
        <a href="{{ route('projects.create') }}" 
           class="inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-violet-600 to-blue-600 text-white text-sm font-semibold rounded-full hover:shadow-lg hover:scale-105 active:scale-95 transition-all duration-300 shadow-md">
            <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Buat Proyek Baru
        </a>
        @endrole
    </div>

    <!-- Status Filter Tabs -->
    <div class="mb-6 bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <nav class="flex flex-wrap">
            <a href="{{ route('projects.index', ['status' => 'all']) }}" 
               class="flex-1 min-w-[120px] text-center py-4 px-4 border-b-2 font-medium text-sm transition-colors
                      @if($status === 'all') 
                          border-violet-600 text-violet-600 bg-violet-50 
                      @else 
                          border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50 
                      @endif">
                <div class="flex items-center justify-center gap-2">
                    <span>Semua</span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                 @if($status === 'all') bg-violet-100 text-violet-700 @else bg-gray-100 text-gray-600 @endif">
                        {{ $projects->count() }}
                    </span>
                </div>
            </a>
            
            <a href="{{ route('projects.index', ['status' => 'planning']) }}" 
               class="flex-1 min-w-[120px] text-center py-4 px-4 border-b-2 font-medium text-sm transition-colors
                      @if($status === 'planning') 
                          border-gray-600 text-gray-600 bg-gray-50 
                      @else 
                          border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50 
                      @endif">
                <div class="flex items-center justify-center gap-2">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <span>Perencanaan</span>
                </div>
            </a>
            
            <a href="{{ route('projects.index', ['status' => 'active']) }}" 
               class="flex-1 min-w-[120px] text-center py-4 px-4 border-b-2 font-medium text-sm transition-colors
                      @if($status === 'active') 
                          border-blue-600 text-blue-600 bg-blue-50 
                      @else 
                          border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50 
                      @endif">
                <div class="flex items-center justify-center gap-2">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    <span>Aktif</span>
                </div>
            </a>
            
            <a href="{{ route('projects.index', ['status' => 'on_hold']) }}" 
               class="flex-1 min-w-[120px] text-center py-4 px-4 border-b-2 font-medium text-sm transition-colors
                      @if($status === 'on_hold') 
                          border-yellow-600 text-yellow-600 bg-yellow-50 
                      @else 
                          border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50 
                      @endif">
                <div class="flex items-center justify-center gap-2">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Ditunda</span>
                </div>
            </a>
            
            <a href="{{ route('projects.index', ['status' => 'completed']) }}" 
               class="flex-1 min-w-[120px] text-center py-4 px-4 border-b-2 font-medium text-sm transition-colors
                      @if($status === 'completed') 
                          border-green-600 text-green-600 bg-green-50 
                      @else 
                          border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50 
                      @endif">
                <div class="flex items-center justify-center gap-2">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Selesai</span>
                </div>
            </a>
            
            <a href="{{ route('projects.index', ['status' => 'blackout']) }}" 
               class="flex-1 min-w-[120px] text-center py-4 px-4 border-b-2 font-medium text-sm transition-colors
                      @if($status === 'blackout') 
                          border-red-600 text-red-600 bg-red-50 
                      @else 
                          border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50 
                      @endif">
                <div class="flex items-center justify-center gap-2">
                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10"/>
                    </svg>
                    <span class="font-bold">Blackout</span>
                </div>
            </a>
        </nav>
    </div>

    <!-- Label Filter -->
    @if(isset($labels))
    <div class="mb-6 flex items-center gap-2 flex-wrap">
        <span class="text-sm font-medium text-gray-700">Filter Label:</span>
        <a href="{{ route('projects.index', ['status' => $status]) }}" 
           class="px-3 py-1.5 text-sm rounded-lg transition-colors {{ !$label ? 'bg-violet-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
            Semua
        </a>
        @foreach($labels as $labelOption)
            <a href="{{ route('projects.index', ['status' => $status, 'label' => $labelOption]) }}" 
               class="px-3 py-1.5 text-sm rounded-lg transition-colors 
                      {{ $label === $labelOption ? 'bg-'.(\App\Models\Project::getLabelColor($labelOption)).'-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                {{ $labelOption }}
            </a>
        @endforeach
    </div>
    @endif

    @if($projects->isEmpty())
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-16 text-center">
            <div class="max-w-md mx-auto">
                <div class="w-20 h-20 mx-auto mb-6 bg-gradient-to-br from-violet-100 to-blue-100 rounded-full flex items-center justify-center">
                    <svg class="h-10 w-10 text-violet-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Belum Ada Proyek</h3>
                <p class="text-gray-600 mb-6">
                    @if($status === 'all')
                        Belum ada proyek yang dibuat di komunitas.
                    @else
                        Belum ada proyek dengan status "{{ \App\Models\Project::getStatusLabel($status) }}".
                    @endif
                </p>
                @role('pm')
                <a href="{{ route('projects.create') }}" 
                   class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-violet-600 to-blue-600 text-white text-sm font-semibold rounded-full hover:shadow-lg transition-all duration-300">
                    <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Buat Proyek Pertama
                </a>
                @endrole
            </div>
        </div>
    @else
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($projects as $project)
                @php
                    $statusColors = [
                        'planning' => ['bg' => 'bg-gray-100', 'text' => 'text-gray-700', 'border' => 'border-gray-200'],
                        'active' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-700', 'border' => 'border-blue-200'],
                        'on_hold' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-700', 'border' => 'border-yellow-200'],
                        'completed' => ['bg' => 'bg-green-100', 'text' => 'text-green-700', 'border' => 'border-green-200'],
                    ];
                    $colors = $statusColors[$project->status] ?? $statusColors['planning'];
                @endphp
                
                <div class="group bg-white rounded-2xl shadow-sm border border-gray-200 hover:shadow-xl hover:border-violet-200 transition-all duration-300 overflow-hidden">
                    <!-- Card Header dengan Gradient -->
                    <div class="h-2 bg-gradient-to-r from-violet-600 via-blue-600 to-emerald-500"></div>
                    
                    <div class="p-6">
                        <!-- Project Title & Status with Actions Menu -->
                        <div class="mb-4">
                            <div class="flex items-start justify-between gap-3 mb-3">
                                <h3 class="text-lg font-bold text-gray-900 group-hover:text-violet-600 transition-colors line-clamp-2 flex-1">
                                    {{ $project->name }}
                                </h3>
                                
                                {{-- Quick Actions Menu (PM only) --}}
                                @if(auth()->user()->hasRole('pm') && $project->owner_id === auth()->id())
                                    <div class="relative" x-data="{ open: false }">
                                        <button @click="open = !open" 
                                                @click.away="open = false"
                                                class="p-2 hover:bg-gray-100 rounded-lg transition-colors"
                                                type="button">
                                            <svg class="h-5 w-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                                            </svg>
                                        </button>
                                        
                                        {{-- Dropdown Menu --}}
                                        <div x-show="open"
                                             x-transition:enter="transition ease-out duration-100"
                                             x-transition:enter-start="transform opacity-0 scale-95"
                                             x-transition:enter-end="transform opacity-100 scale-100"
                                             x-transition:leave="transition ease-in duration-75"
                                             x-transition:leave-start="transform opacity-100 scale-100"
                                             x-transition:leave-end="transform opacity-0 scale-95"
                                             class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-10"
                                             style="display: none;">
                                            
                                            {{-- Edit --}}
                                            <a href="{{ route('projects.edit', $project) }}" 
                                               class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-violet-50 hover:text-violet-700 transition-colors">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                                <span class="font-medium">Edit Proyek</span>
                                            </a>
                                            
                                            {{-- View --}}
                                            <a href="{{ route('projects.show', $project) }}" 
                                               class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                                <span class="font-medium">Lihat Detail</span>
                                            </a>
                                            
                                            <div class="border-t border-gray-100 my-1"></div>
                                            
                                            {{-- Delete --}}
                                            <form action="{{ route('projects.destroy', $project) }}" method="POST" 
                                                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus proyek ini? Semua tiket dan data terkait akan ikut terhapus!')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                    <span class="font-medium">Hapus Proyek</span>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <div class="flex flex-wrap gap-2">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $colors['bg'] }} {{ $colors['text'] }} border {{ $colors['border'] }}">
                                    {{ \App\Models\Project::getStatusLabel($project->status) }}
                                </span>
                                <x-project-label-badge :label="$project->label" size="sm" />
                            </div>
                        </div>

                        <!-- Project Description -->
                        @if($project->description)
                            <p class="text-sm text-gray-600 mb-4 line-clamp-3 leading-relaxed">
                                {{ $project->description }}
                            </p>
                        @else
                            <p class="text-sm text-gray-400 italic mb-4">
                                Tidak ada deskripsi
                            </p>
                        @endif

                        <!-- Stats -->
                        <div class="flex items-center gap-4 mb-4 pb-4 border-b border-gray-100">
                            <div class="flex items-center gap-2 text-sm">
                                <div class="p-1.5 bg-blue-50 rounded-lg">
                                    <svg class="h-4 w-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                </div>
                                <span class="font-semibold text-gray-900">{{ $project->tickets_count }}</span>
                                <span class="text-gray-500">Tiket</span>
                            </div>
                            
                            <div class="flex items-center gap-2 text-sm">
                                <div class="p-1.5 bg-emerald-50 rounded-lg">
                                    <svg class="h-4 w-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                </div>
                                <span class="font-semibold text-gray-900">{{ $project->members->count() }}</span>
                                <span class="text-gray-500">Member</span>
                            </div>
                        </div>

                        <!-- Owner Info -->
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-violet-600 to-blue-600 flex items-center justify-center text-white font-semibold text-sm">
                                {{ strtoupper(substr($project->owner->name, 0, 1)) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs text-gray-500">Project Manager</p>
                                <p class="text-sm font-medium text-gray-900 truncate">{{ $project->owner->name }}</p>
                            </div>
                        </div>

                        <!-- Action Button -->
                        <a href="{{ route('projects.show', $project) }}" 
                           class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-gradient-to-r from-violet-600 to-blue-600 text-white text-sm font-semibold rounded-xl hover:shadow-lg hover:scale-105 active:scale-95 transition-all duration-300">
                            <span>Lihat Detail</span>
                            <svg class="ml-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                            </svg>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
