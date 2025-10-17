@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Welcome Header -->
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="p-3 bg-gradient-to-br from-indigo-600 to-purple-600 rounded-xl shadow-lg">
                <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
            </div>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
                <p class="text-gray-600 mt-1">Selamat datang kembali, {{ auth()->user()->name }}!</p>
            </div>
        </div>
        
        <div class="flex items-center gap-2">
            @php
                $userRoles = auth()->user()->getRoleNames();
            @endphp
            @foreach($userRoles as $role)
                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold 
                             {{ $role === 'pm' ? 'bg-violet-100 text-violet-700 border border-violet-200' : '' }}
                             {{ $role === 'kewirausahaan' ? 'bg-emerald-100 text-emerald-700 border border-emerald-200' : '' }}
                             {{ $role === 'hr' ? 'bg-blue-100 text-blue-700 border border-blue-200' : '' }}
                             {{ $role === 'sekretaris' ? 'bg-amber-100 text-amber-700 border border-amber-200' : '' }}
                             {{ $role === 'finance' ? 'bg-green-100 text-green-700 border border-green-200' : '' }}
                             {{ !in_array($role, ['pm', 'kewirausahaan', 'hr', 'sekretaris', 'finance']) ? 'bg-gray-100 text-gray-700 border border-gray-200' : '' }}">
                    <svg class="h-3 w-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                    </svg>
                    {{ strtoupper($role) }}
                </span>
            @endforeach
        </div>
    </div>

    <!-- Stats Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-6 mb-8">
        <!-- My Tickets -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 hover:shadow-xl hover:border-blue-300 transition-all duration-300">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-gradient-to-br from-blue-100 to-blue-200 rounded-xl">
                    <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 mb-1">{{ $stats['my_tickets_count'] }}</h3>
            <p class="text-sm text-gray-600">Tiket Saya</p>
        </div>

        <!-- Doing Tickets -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 hover:shadow-xl hover:border-purple-300 transition-all duration-300">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-gradient-to-br from-purple-100 to-purple-200 rounded-xl">
                    <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 mb-1">{{ $stats['doing_tickets_count'] }}</h3>
            <p class="text-sm text-gray-600">Sedang Dikerjakan</p>
        </div>

        <!-- Available Tickets -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 hover:shadow-xl hover:border-amber-300 transition-all duration-300">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-gradient-to-br from-amber-100 to-amber-200 rounded-xl">
                    <svg class="h-6 w-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 mb-1">{{ $stats['available_tickets_count'] }}</h3>
            <p class="text-sm text-gray-600">Tiket Tersedia</p>
        </div>

        <!-- My Projects -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 hover:shadow-xl hover:border-emerald-300 transition-all duration-300">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-gradient-to-br from-emerald-100 to-emerald-200 rounded-xl">
                    <svg class="h-6 w-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                    </svg>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 mb-1">{{ $stats['my_projects_count'] }}</h3>
            <p class="text-sm text-gray-600">Proyek Saya</p>
        </div>

        <!-- Active Projects -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 hover:shadow-xl hover:border-green-300 transition-all duration-300">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-gradient-to-br from-green-100 to-green-200 rounded-xl">
                    <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 mb-1">{{ $stats['active_projects_count'] }}</h3>
            <p class="text-sm text-gray-600">Proyek Aktif</p>
        </div>
    </div>

    <!-- My Active Tickets Section -->
    @if($activeTickets->isNotEmpty())
    <div class="mb-8">
        <div class="mb-6 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-purple-100 rounded-lg">
                    <svg class="h-5 w-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-900">Tiket Aktif Saya</h2>
                    <p class="text-sm text-gray-600">Tiket yang sedang Anda kerjakan</p>
                </div>
            </div>
            <a href="{{ route('tickets.index') }}" 
               class="inline-flex items-center gap-2 text-sm font-medium text-blue-600 hover:text-blue-700 transition-colors">
                Lihat Semua
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                </svg>
            </a>
        </div>
        
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($activeTickets as $ticket)
                @php
                    $statusColors = [
                        'todo' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-700', 'border' => 'border-blue-200'],
                        'doing' => ['bg' => 'bg-purple-100', 'text' => 'text-purple-700', 'border' => 'border-purple-200'],
                    ];
                    $colors = $statusColors[$ticket->status] ?? $statusColors['todo'];
                @endphp
                
                <div class="group bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-xl hover:border-purple-200 transition-all duration-300 overflow-hidden">
                    <div class="bg-gradient-to-r from-{{ $ticket->status === 'doing' ? 'purple' : 'blue' }}-50 to-{{ $ticket->status === 'doing' ? 'pink' : 'cyan' }}-100 px-4 py-3 border-b border-{{ $ticket->status === 'doing' ? 'purple' : 'blue' }}-200">
                        <div class="flex items-center justify-between mb-2">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $colors['bg'] }} {{ $colors['text'] }} border {{ $colors['border'] }}">
                                {{ strtoupper($ticket->status) }}
                            </span>
                            @if($ticket->priority)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                             {{ $ticket->priority === 'urgent' ? 'bg-red-100 text-red-700' : '' }}
                                             {{ $ticket->priority === 'high' ? 'bg-orange-100 text-orange-700' : '' }}
                                             {{ $ticket->priority === 'medium' ? 'bg-yellow-100 text-yellow-700' : '' }}
                                             {{ $ticket->priority === 'low' ? 'bg-gray-100 text-gray-700' : '' }}">
                                    {{ ucfirst($ticket->priority) }}
                                </span>
                            @endif
                        </div>
                        <h3 class="font-bold text-gray-900 text-sm line-clamp-2 group-hover:text-purple-600 transition-colors">
                            {{ $ticket->title }}
                        </h3>
                    </div>
                    
                    <div class="p-4">
                        @if($ticket->description)
                            <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ $ticket->description }}</p>
                        @endif
                        
                        @if($ticket->project)
                            <div class="flex items-center gap-2 mb-3 text-xs">
                                <div class="p-1 bg-blue-50 rounded">
                                    <svg class="h-3 w-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                                    </svg>
                                </div>
                                <span class="text-gray-600 font-medium">{{ $ticket->project->name }}</span>
                            </div>
                        @endif
                        
                        <a href="{{ route('tickets.show', $ticket) }}" 
                           class="w-full inline-flex items-center justify-center px-3 py-2 bg-gradient-to-r from-purple-600 to-pink-600 text-white text-sm font-semibold rounded-lg hover:shadow-lg hover:scale-105 active:scale-95 transition-all duration-300">
                            Lihat Detail
                            <svg class="ml-2 h-4 w-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                            </svg>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- My Projects Section -->
    @if($userProjects->isNotEmpty())
    <div class="mb-8">
        <div class="mb-6 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-violet-100 rounded-lg">
                    <svg class="h-5 w-5 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-900">Proyek Saya</h2>
                    <p class="text-sm text-gray-600">Proyek yang Anda ikuti atau kelola</p>
                </div>
            </div>
            <a href="{{ route('projects.index') }}" 
               class="inline-flex items-center gap-2 text-sm font-medium text-blue-600 hover:text-blue-700 transition-colors">
                Lihat Semua
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                </svg>
            </a>
        </div>
        
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($userProjects as $project)
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
                    <div class="h-2 bg-gradient-to-r from-violet-600 via-blue-600 to-emerald-500"></div>
                    
                    <div class="p-6">
                        <div class="mb-4">
                            <div class="flex items-start justify-between gap-3 mb-3">
                                <h3 class="text-lg font-bold text-gray-900 group-hover:text-violet-600 transition-colors line-clamp-2 flex-1">
                                    {{ $project->name }}
                                </h3>
                            </div>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $colors['bg'] }} {{ $colors['text'] }} border {{ $colors['border'] }}">
                                {{ \App\Models\Project::getStatusLabel($project->status) }}
                            </span>
                        </div>

                        @if($project->description)
                            <p class="text-sm text-gray-600 mb-4 line-clamp-3 leading-relaxed">{{ $project->description }}</p>
                        @else
                            <p class="text-sm text-gray-400 italic mb-4">Tidak ada deskripsi</p>
                        @endif

                        <div class="flex items-center gap-4 mb-4 pb-4 border-b border-gray-100">
                            <div class="flex items-center gap-2 text-sm">
                                <div class="p-1.5 bg-blue-50 rounded-lg">
                                    <svg class="h-4 w-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                </div>
                                <span class="font-semibold text-gray-900">{{ $project->tickets->count() }}</span>
                                <span class="text-gray-500">Tiket</span>
                            </div>
                            
                            <div class="flex items-center gap-2 text-sm">
                                <div class="p-1.5 bg-emerald-50 rounded-lg">
                                    <svg class="h-4 w-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                    </svg>
                                </div>
                                <span class="font-semibold text-gray-900">{{ $project->members->count() }}</span>
                                <span class="text-gray-500">Member</span>
                            </div>
                        </div>

                        <a href="{{ route('projects.show', $project) }}" 
                           class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-gradient-to-r from-violet-600 to-blue-600 text-white text-sm font-semibold rounded-xl hover:shadow-lg hover:scale-105 active:scale-95 transition-all duration-300">
                            <span>Lihat Detail</span>
                            <svg class="ml-2 h-4 w-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                            </svg>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Empty State if no data -->
    @if($activeTickets->isEmpty() && $userProjects->isEmpty())
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-16 text-center">
        <div class="max-w-md mx-auto">
            <div class="w-20 h-20 mx-auto mb-6 bg-gradient-to-br from-indigo-100 to-purple-100 rounded-full flex items-center justify-center">
                <svg class="h-10 w-10 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Belum Ada Aktivitas</h3>
            <p class="text-gray-600 mb-6">Anda belum memiliki tiket atau proyek aktif saat ini</p>
            <div class="flex gap-3 justify-center">
                <a href="{{ route('tickets.index') }}" 
                   class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-purple-600 to-pink-600 text-white text-sm font-semibold rounded-full hover:shadow-lg transition-all duration-300">
                    <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    Lihat Tiket
                </a>
                <a href="{{ route('projects.index') }}" 
                   class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-violet-600 to-blue-600 text-white text-sm font-semibold rounded-full hover:shadow-lg transition-all duration-300">
                    <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                    </svg>
                    Lihat Proyek
                </a>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
