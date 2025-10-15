<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Dashboard') }}
            </h2>
            <div class="flex flex-wrap gap-2">
                @foreach(auth()->user()->getRoleNames() as $role)
                    <span class="px-3 py-1 text-xs font-semibold rounded-full 
                        @if($role === 'pm') bg-purple-100 text-purple-700
                        @elseif($role === 'hr') bg-blue-100 text-blue-700
                        @elseif($role === 'sekretaris') bg-green-100 text-green-700
                        @elseif($role === 'bendahara') bg-yellow-100 text-yellow-700
                        @elseif($role === 'media') bg-pink-100 text-pink-700
                        @elseif($role === 'pr') bg-indigo-100 text-indigo-700
                        @elseif($role === 'kewirausahaan') bg-orange-100 text-orange-700
                        @elseif($role === 'researcher') bg-teal-100 text-teal-700
                        @elseif($role === 'talent_manager') bg-cyan-100 text-cyan-700
                        @elseif($role === 'talent') bg-lime-100 text-lime-700
                        @else bg-gray-100 text-gray-700
                        @endif">
                        {{ strtoupper($role) }}
                    </span>
                @endforeach
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            
            {{-- Grid 1: Statistics Only --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                        {{-- Tiket Saya --}}
                        <div class="text-center p-4 bg-blue-50 rounded-lg">
                            <div class="text-3xl font-bold text-blue-600">
                                {{ auth()->user()->claimedTickets()->count() }}
                            </div>
                            <div class="text-xs text-gray-600 mt-1">Tiket Saya</div>
                        </div>

                        {{-- Tiket Aktif --}}
                        <div class="text-center p-4 bg-green-50 rounded-lg">
                            <div class="text-3xl font-bold text-green-600">
                                {{ auth()->user()->claimedTickets()->where('status', 'doing')->count() }}
                            </div>
                            <div class="text-xs text-gray-600 mt-1">Dikerjakan</div>
                        </div>

                        {{-- Total Proyek --}}
                        <div class="text-center p-4 bg-purple-50 rounded-lg">
                            <div class="text-3xl font-bold text-purple-600">
                                {{ \App\Models\Project::count() }}
                            </div>
                            <div class="text-xs text-gray-600 mt-1">Total Proyek</div>
                        </div>

                        {{-- Proyek Aktif --}}
                        <div class="text-center p-4 bg-orange-50 rounded-lg">
                            <div class="text-3xl font-bold text-orange-600">
                                {{ \App\Models\Project::where('status', 'active')->count() }}
                            </div>
                            <div class="text-xs text-gray-600 mt-1">Proyek Aktif</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Grid 2: Quick Actions (Circular Buttons) --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4">
                    <h4 class="text-lg font-semibold text-gray-800 mb-3">Quick Actions</h4>
                    <div class="flex flex-wrap justify-center gap-6">
                        
                        {{-- Everyone: Tiket Saya --}}
                        <a href="{{ route('tickets.mine') }}" class="flex flex-col items-center group" title="Tiket Saya">
                            <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center text-white shadow-lg hover:shadow-xl transform hover:scale-110 transition-all duration-200">
                                <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                            </div>
                        </a>

                        {{-- Everyone: Projects --}}
                        <a href="{{ route('projects.index') }}" class="flex flex-col items-center group" title="Proyek">
                            <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-purple-600 rounded-full flex items-center justify-center text-white shadow-lg hover:shadow-xl transform hover:scale-110 transition-all duration-200">
                                <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                                </svg>
                            </div>
                        </a>

                        {{-- PM Only: Tambah Tiket & Buat Proyek --}}
                        @role('pm')
                        <a href="{{ route('tickets.createGeneral') }}" class="flex flex-col items-center group" title="Tambah Tiket">
                            <div class="w-16 h-16 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-full flex items-center justify-center text-white shadow-lg hover:shadow-xl transform hover:scale-110 transition-all duration-200">
                                <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                            </div>
                        </a>
                        
                        <a href="{{ route('projects.create') }}" class="flex flex-col items-center group" title="Buat Proyek">
                            <div class="w-16 h-16 bg-gradient-to-br from-pink-500 to-pink-600 rounded-full flex items-center justify-center text-white shadow-lg hover:shadow-xl transform hover:scale-110 transition-all duration-200">
                                <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                </svg>
                            </div>
                        </a>
                        @endrole

                        {{-- Sekretaris: Documents --}}
                        @role('sekretaris')
                        <a href="{{ route('documents.index') }}" class="flex flex-col items-center group" title="Dokumen">
                            <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-green-600 rounded-full flex items-center justify-center text-white shadow-lg hover:shadow-xl transform hover:scale-110 transition-all duration-200">
                                <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                        </a>
                        @endrole

                        {{-- Bendahara: RAB --}}
                        @role('bendahara')
                        <a href="{{ route('rabs.index') }}" class="flex flex-col items-center group" title="RAB">
                            <div class="w-16 h-16 bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-full flex items-center justify-center text-white shadow-lg hover:shadow-xl transform hover:scale-110 transition-all duration-200">
                                <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </a>
                        @endrole

                        {{-- HR: User Management --}}
                        @role('hr')
                        <a href="{{ route('admin.users.index') }}" class="flex flex-col items-center group" title="Kelola User">
                            <div class="w-16 h-16 bg-gradient-to-br from-cyan-500 to-cyan-600 rounded-full flex items-center justify-center text-white shadow-lg hover:shadow-xl transform hover:scale-110 transition-all duration-200">
                                <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                            </div>
                        </a>
                        @endrole

                        {{-- Everyone: Personal Calendar --}}
                        @if(!auth()->user()->hasRole('guest'))
                        <a href="{{ route('calendar.personal') }}" class="flex flex-col items-center group" title="Kalender Pribadi">
                            <div class="w-16 h-16 bg-gradient-to-br from-teal-500 to-teal-600 rounded-full flex items-center justify-center text-white shadow-lg hover:shadow-xl transform hover:scale-110 transition-all duration-200">
                                <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                        </a>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Grid 3: Two Column Layout (Left: Calendar, Right: Tickets & Projects) --}}
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                
                {{-- Left Column (3/4) - Kalender Umum --}}
                <div class="lg:col-span-3">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-4 border-b border-gray-200">
                            <h4 class="text-base font-semibold text-gray-800">Kalender Umum</h4>
                        </div>
                        <div class="p-4">
                            <div id="dashboard-calendar"></div>
                        </div>
                    </div>
                    
                    {{-- Legend - Moved below calendar --}}
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-4">
                        <div class="p-4">
                            <h5 class="text-sm font-semibold text-gray-700 mb-3">Legenda</h5>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                {{-- Tiket Status --}}
                                <div>
                                    <p class="text-xs font-medium text-gray-600 mb-2">Status Tiket</p>
                                    <div class="space-y-1">
                                        <div class="flex items-center gap-2">
                                            <div class="w-3 h-3 rounded bg-gray-500"></div>
                                            <span class="text-xs text-gray-600">To Do</span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <div class="w-3 h-3 rounded bg-blue-500"></div>
                                            <span class="text-xs text-gray-600">Doing</span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <div class="w-3 h-3 rounded bg-green-500"></div>
                                            <span class="text-xs text-gray-600">Done</span>
                                        </div>
                                    </div>
                                </div>

                                {{-- Project Status --}}
                                <div>
                                    <p class="text-xs font-medium text-gray-600 mb-2">Status Proyek</p>
                                    <div class="space-y-1">
                                        <div class="flex items-center gap-2">
                                            <div class="w-3 h-3 rounded bg-gray-500"></div>
                                            <span class="text-xs text-gray-600">Perencanaan</span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <div class="w-3 h-3 rounded bg-blue-500"></div>
                                            <span class="text-xs text-gray-600">Aktif</span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <div class="w-3 h-3 rounded bg-yellow-500"></div>
                                            <span class="text-xs text-gray-600">Tertunda</span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <div class="w-3 h-3 rounded bg-green-500"></div>
                                            <span class="text-xs text-gray-600">Selesai</span>
                                        </div>
                                    </div>
                                </div>

                                {{-- Personal Activities --}}
                                @if(!auth()->user()->hasRole('guest'))
                                <div>
                                    <p class="text-xs font-medium text-gray-600 mb-2">Kegiatan Pribadi</p>
                                    <div class="space-y-1">
                                        <div class="flex items-center gap-2">
                                            <div class="w-3 h-3 rounded bg-blue-500"></div>
                                            <span class="text-xs text-gray-600">Pribadi</span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <div class="w-3 h-3 rounded bg-green-500"></div>
                                            <span class="text-xs text-gray-600">Keluarga</span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <div class="w-3 h-3 rounded bg-orange-500"></div>
                                            <span class="text-xs text-gray-600">Pekerjaan</span>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Right Column (1/4) - Tiket Aktif & Proyek Saya --}}
                <div class="lg:col-span-1 space-y-6">
                    
                    {{-- Tiket Aktif Saya --}}
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-4 border-b border-gray-200">
                            <h4 class="text-base font-semibold text-gray-800">Tiket Aktif</h4>
                        </div>
                        <div class="p-4">
                            <div class="space-y-3 max-h-80 overflow-y-auto">
                                @forelse($activeTickets as $ticket)
                                    <a href="{{ route('projects.show', $ticket->project_id) }}" class="block p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                                        <div class="flex items-start gap-2">
                                            <div class="flex-shrink-0 mt-0.5">
                                                <div class="w-2 h-2 rounded-full
                                                    @if($ticket->status === 'todo') bg-gray-500
                                                    @elseif($ticket->status === 'doing') bg-blue-500
                                                    @else bg-green-500
                                                    @endif"></div>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-medium text-gray-800 truncate">
                                                    {{ $ticket->title }}
                                                </p>
                                                <p class="text-xs text-gray-500 mt-1">
                                                    {{ $ticket->project->name ?? 'Umum' }}
                                                </p>
                                                <span class="inline-block mt-1 px-2 py-0.5 text-xs rounded
                                                    @if($ticket->status === 'todo') bg-gray-100 text-gray-700
                                                    @elseif($ticket->status === 'doing') bg-blue-100 text-blue-700
                                                    @else bg-green-100 text-green-700
                                                    @endif">
                                                    {{ ucfirst($ticket->status) }}
                                                </span>
                                            </div>
                                        </div>
                                    </a>
                                @empty
                                    <div class="text-center py-8">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                        </svg>
                                        <p class="mt-2 text-sm text-gray-500">Tidak ada tiket aktif</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    {{-- Proyek Saya --}}
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-4 border-b border-gray-200">
                            <h4 class="text-base font-semibold text-gray-800">Proyek Saya</h4>
                        </div>
                        <div class="p-4">
                            <div class="space-y-3 max-h-80 overflow-y-auto">
                                @forelse($userProjects as $project)
                                    <a href="{{ route('projects.show', $project->id) }}" class="block p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                                        <div class="flex items-start gap-3">
                                            <div class="flex-shrink-0">
                                                <div class="w-10 h-10 rounded-lg flex items-center justify-center text-white font-bold
                                                    @if($project->status === 'planning') bg-gray-500
                                                    @elseif($project->status === 'active') bg-blue-500
                                                    @elseif($project->status === 'on_hold') bg-yellow-500
                                                    @else bg-green-500
                                                    @endif">
                                                    {{ strtoupper(substr($project->name, 0, 1)) }}
                                                </div>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-medium text-gray-800 truncate">
                                                    {{ $project->name }}
                                                </p>
                                                <p class="text-xs text-gray-500 mt-1">
                                                    {{ $project->tickets->count() }} tiket
                                                </p>
                                                <span class="inline-block mt-1 px-2 py-0.5 text-xs rounded
                                                    @if($project->status === 'planning') bg-gray-100 text-gray-700
                                                    @elseif($project->status === 'active') bg-blue-100 text-blue-700
                                                    @elseif($project->status === 'on_hold') bg-yellow-100 text-yellow-700
                                                    @else bg-green-100 text-green-700
                                                    @endif">
                                                    {{ ucfirst(str_replace('_', ' ', $project->status)) }}
                                                </span>
                                            </div>
                                        </div>
                                    </a>
                                @empty
                                    <div class="text-center py-8">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                                        </svg>
                                        <p class="mt-2 text-sm text-gray-500">Tidak ada proyek</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                </div>

            </div>

        </div>
    </div>

    {{-- Toast Notification Container --}}
    <div id="toast-container" class="fixed top-20 right-4 z-50 space-y-2"></div>

    {{-- FullCalendar CSS - Loaded inline untuk memastikan load order --}}
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.5/main.min.css' rel='stylesheet' />
    
    {{-- Custom Calendar Styles --}}
    <style>
    #dashboard-calendar {
        background: white;
        border-radius: 8px;
        padding: 20px;
        min-height: 500px;
        display: block !important;
    }
    
    #dashboard-calendar .fc-scroller {
        overflow: visible !important;
    }
    
    #dashboard-calendar .fc-scroller-liquid-absolute {
        position: static !important;
    }
    
    #dashboard-calendar .fc-daygrid-day-frame {
        min-height: 100px !important;
        display: block !important;
        position: relative !important;
    }
    
    #dashboard-calendar .fc-daygrid-day-events {
        margin-top: 2px !important;
        padding: 0 2px !important;
    }
    
    #dashboard-calendar .fc-daygrid-event {
        margin: 1px 0 !important;
        padding: 2px 4px !important;
        font-size: 0.75rem !important;
    }
    
    /* Custom scrollbar */
    .overflow-y-auto::-webkit-scrollbar {
        width: 6px;
    }
    
    .overflow-y-auto::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    
    .overflow-y-auto::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 10px;
    }
    
    .overflow-y-auto::-webkit-scrollbar-thumb:hover {
        background: #555;
    }
    
    /* Toast Animation */
    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
    
    .toast {
        animation: slideIn 0.3s ease-out;
    }
    
    .toast.hide {
        animation: slideOut 0.3s ease-out;
    }
    </style>
    
    {{-- FullCalendar JS - Loaded inline setelah CSS --}}
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.5/main.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.5/locales/id.js'></script>
    
    <script>
        // Toast Notification Function
        function showToast(message, type = 'success') {
            const toastContainer = document.getElementById('toast-container');
            const toast = document.createElement('div');
            
            const bgColor = type === 'success' ? 'bg-green-500' : type === 'error' ? 'bg-red-500' : 'bg-blue-500';
            const icon = type === 'success' 
                ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />'
                : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />';
            
            toast.className = `toast flex items-center gap-3 ${bgColor} text-white px-6 py-4 rounded-lg shadow-lg max-w-md`;
            toast.innerHTML = `
                <svg class="h-6 w-6 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    ${icon}
                </svg>
                <span class="flex-1">${message}</span>
                <button onclick="this.parentElement.remove()" class="flex-shrink-0 text-white hover:text-gray-200">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            `;
            
            toastContainer.appendChild(toast);
            
            // Auto remove after 5 seconds
            setTimeout(() => {
                toast.classList.add('hide');
                setTimeout(() => {
                    toast.remove();
                }, 300);
            }, 5000);
        }
        
        // Show welcome toast on page load
        document.addEventListener('DOMContentLoaded', function() {
            const userName = "{{ auth()->user()->name }}";
            const userRole = "{{ auth()->user()->getRoleNames()->first() }}";
            showToast(`Selamat datang, ${userName}! 👋 Anda login sebagai ${userRole}`, 'success');
            
            // Initialize Calendar
            console.log('🔍 Initializing Dashboard Calendar...');
            const calendarEl = document.getElementById('dashboard-calendar');
            
            if (!calendarEl) {
                console.error('❌ Calendar element not found!');
                return;
            }
            
            console.log('📅 Calendar element found, creating calendar instance...');
            
            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'id',
                height: 650,
                contentHeight: 650,
                aspectRatio: 1.8,
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth'
                },
                buttonText: {
                    today: 'Hari Ini',
                    month: 'Bulan'
                },
                dayMaxEvents: true,
                eventDisplay: 'block',
                displayEventTime: true,
                eventTimeFormat: {
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: false
                },
                eventSources: [
                    {
                        url: '/api/calendar/user/events',
                        failure: function(error) {
                            console.error('❌ Gagal memuat event:', error);
                        },
                        success: function(data) {
                            console.log('✅ Event loaded:', data);
                        }
                    },
                    @if(!auth()->user()->hasRole('guest'))
                    {
                        url: '/api/calendar/all-personal-activities',
                        failure: function(error) {
                            console.error('❌ Gagal memuat kegiatan pribadi:', error);
                        },
                        success: function(data) {
                            console.log('✅ Personal activities loaded:', data);
                        }
                    },
                    @endif
                    {
                        url: '/api/calendar/user/projects',
                        failure: function(error) {
                            console.error('❌ Gagal memuat proyek:', error);
                        },
                        success: function(data) {
                            console.log('✅ Projects loaded:', data);
                        }
                    }
                ],
                eventClick: function(info) {
                    alert(info.event.title + '\n' + (info.event.extendedProps.description || ''));
                },
                viewDidMount: function(info) {
                    console.log('🎨 Calendar view mounted:', info.view.type);
                },
                datesSet: function(info) {
                    console.log('📆 Dates set:', info.startStr, 'to', info.endStr);
                }
            });
            
            console.log('🚀 Rendering dashboard calendar...');
            calendar.render();
            
            // Force update size after render
            setTimeout(() => {
                if (calendar) {
                    calendar.updateSize();
                    console.log('📏 Dashboard calendar size updated');
                }
            }, 100);
            
            // Additional force render
            setTimeout(() => {
                if (calendar) {
                    calendar.render();
                    calendar.updateSize();
                    console.log('🔄 Dashboard calendar force re-rendered');
                }
            }, 500);
            
            console.log('✅ Dashboard calendar initialized successfully');
        });
    </script>

</x-app-layout>
