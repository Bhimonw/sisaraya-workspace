@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto">
    {{-- Smart Back Button --}}
    <div class="mb-6">
        @php
            // Determine smart back URL based on ticket context
            $backUrl = url()->previous();
            $backText = 'Kembali';
            
            if ($ticket->project_id) {
                // Ticket belongs to a project - go back to project detail
                $backUrl = route('projects.show', $ticket->project_id);
                $backText = 'Kembali ke Proyek';
            } elseif ($ticket->context === 'event') {
                // Event ticket without project - go to calendar or tickets list
                if (str_contains(url()->previous(), 'calendar')) {
                    $backUrl = route('calendar.index');
                    $backText = 'Kembali ke Kalender';
                } else {
                    $backUrl = route('tickets.index');
                    $backText = 'Kembali ke Daftar Tiket';
                }
            } else {
                // General ticket (umum) or assigned to me - go to my tickets
                if (str_contains(url()->previous(), 'tickets.mine')) {
                    $backUrl = route('tickets.mine');
                    $backText = 'Kembali ke Tiketku';
                } else {
                    $backUrl = route('tickets.index');
                    $backText = 'Kembali ke Daftar Tiket';
                }
            }
        @endphp
        
        <x-back-button :url="$backUrl" :text="$backText" />
    </div>

    {{-- Page Header with Enhanced Design --}}
    <div class="bg-gradient-to-br from-white via-indigo-50/30 to-purple-50/30 rounded-xl shadow-lg border border-indigo-100 p-8 mb-6">
        <div class="flex items-start justify-between mb-6">
            <div class="flex-1">
                <div class="flex items-center gap-3 mb-3">
                    <div class="p-2 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg shadow-md">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold bg-gradient-to-r from-gray-900 to-gray-700 bg-clip-text text-transparent">Detail Tiket</h1>
                        <p class="text-sm text-gray-500 mt-0.5">Informasi lengkap tiket dan tindakan</p>
                    </div>
                </div>
            </div>

            {{-- Status Badge with Enhanced Shadow --}}
            <div class="flex items-center gap-2">
                @if($ticket->status === 'todo')
                    <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-bold bg-gradient-to-r from-amber-400 to-orange-500 text-white shadow-lg shadow-orange-500/50 hover:shadow-xl hover:shadow-orange-500/60 transition-shadow">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        To Do
                    </span>
                @elseif($ticket->status === 'doing')
                    <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-bold bg-gradient-to-r from-purple-500 to-indigo-600 text-white shadow-lg shadow-purple-500/50 hover:shadow-xl hover:shadow-purple-500/60 transition-shadow">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        Doing
                    </span>
                @elseif($ticket->status === 'done')
                    <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-bold bg-gradient-to-r from-green-500 to-teal-600 text-white shadow-lg shadow-green-500/50 hover:shadow-xl hover:shadow-green-500/60 transition-shadow">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Done
                    </span>
                @elseif($ticket->status === 'blackout')
                    <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-md text-sm font-bold bg-gradient-to-r from-gray-700 to-gray-900 text-white shadow-sm">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                        </svg>
                        Blackout
                    </span>
                @endif
            </div>
        </div>

        {{-- Ticket Title with Enhanced Styling --}}
        <h2 class="text-2xl font-bold text-gray-900 mb-5 leading-tight">{{ $ticket->title }}</h2>

        {{-- Meta Information with Enhanced Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
            @if($ticket->creator)
                <div class="flex items-center gap-2 px-3 py-2 bg-white/80 rounded-lg border border-gray-200 shadow-sm">
                    <div class="flex-shrink-0">
                        <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs text-gray-500 font-medium">Pembuat</p>
                        <p class="text-sm font-semibold text-gray-900 truncate">{{ $ticket->creator->name }}</p>
                    </div>
                </div>
            @endif

            @if($ticket->created_at)
                <div class="flex items-center gap-2 px-3 py-2 bg-white/80 rounded-lg border border-gray-200 shadow-sm">
                    <div class="flex-shrink-0">
                        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs text-gray-500 font-medium">Dibuat</p>
                        <p class="text-sm font-semibold text-gray-900">{{ $ticket->created_at->format('d M Y, H:i') }}</p>
                    </div>
                </div>
            @endif

            @if($ticket->context)
                <div class="flex items-center gap-2 px-3 py-2 bg-white/80 rounded-lg border border-gray-200 shadow-sm">
                    <div class="flex-shrink-0">
                        @if($ticket->context === 'umum')
                            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                            </svg>
                        @elseif($ticket->context === 'event')
                            <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        @else
                            <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                            </svg>
                        @endif
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs text-gray-500 font-medium">Konteks</p>
                        <p class="text-sm font-semibold text-gray-900 capitalize">
                            @if($ticket->context === 'umum')
                                Umum
                            @elseif($ticket->context === 'event')
                                Event
                            @else
                                Proyek
                            @endif
                        </p>
                    </div>
                </div>
            @endif

            @if($ticket->assigned_to)
                <div class="flex items-center gap-2 px-3 py-2 bg-white/80 rounded-lg border border-gray-200 shadow-sm">
                    <div class="flex-shrink-0">
                        <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs text-gray-500 font-medium">Ditugaskan ke</p>
                        <p class="text-sm font-semibold text-gray-900 truncate">{{ $ticket->assignee->name ?? '-' }}</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Ticket Details Grid with Enhanced Design --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        {{-- Left Column: Main Info --}}
        <div class="space-y-6">
            {{-- Description --}}
            @if($ticket->description)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Deskripsi</h3>
                    <div class="prose prose-sm max-w-none text-gray-700">
                        {!! nl2br(e($ticket->description)) !!}
                    </div>
                </div>
            @endif

            {{-- Project Info --}}
            @if($ticket->project)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Proyek</h3>
                    <a href="{{ route('projects.show', $ticket->project) }}" class="inline-flex items-center text-indigo-600 hover:text-indigo-700 font-medium">
                        {{ $ticket->project->name }}
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                        </svg>
                    </a>
                </div>
            @elseif($ticket->projectEvent)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Event</h3>
                    <div class="text-gray-700">
                        <div class="font-medium">{{ $ticket->projectEvent->name }}</div>
                        @if($ticket->projectEvent->project)
                            <a href="{{ route('projects.show', $ticket->projectEvent->project) }}" class="text-sm text-indigo-600 hover:text-indigo-700">
                                {{ $ticket->projectEvent->project->name }}
                            </a>
                        @endif
                    </div>
                </div>
            @endif

            {{-- RAB Link --}}
            @if($ticket->rab)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">RAB Terkait</h3>
                    <a href="{{ route('rabs.show', $ticket->rab) }}" class="inline-flex items-center text-indigo-600 hover:text-indigo-700 font-medium">
                        {{ $ticket->rab->title }}
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                        </svg>
                    </a>
                </div>
            @endif
        </div>

        {{-- Right Column: Metadata --}}
        <div class="space-y-6">
            {{-- Priority & Weight --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Prioritas & Bobot</h3>
                
                {{-- Priority --}}
                <div class="mb-4">
                    <div class="text-sm text-gray-600 mb-2">Prioritas</div>
                    @if($ticket->priority)
                        @php
                            $priorityColor = \App\Models\Ticket::getPriorityColor($ticket->priority);
                            $priorityBadgeClasses = [
                                'gray' => 'bg-gray-100 text-gray-700',
                                'blue' => 'bg-blue-100 text-blue-700',
                                'orange' => 'bg-orange-100 text-orange-700',
                                'red' => 'bg-red-100 text-red-700',
                            ];
                        @endphp
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium {{ $priorityBadgeClasses[$priorityColor] ?? 'bg-gray-100 text-gray-700' }}">
                            {{ \App\Models\Ticket::getPriorityLabel($ticket->priority) }}
                        </span>
                    @else
                        <span class="text-gray-400">—</span>
                    @endif
                </div>

                {{-- Weight --}}
                <div>
                    <div class="text-sm text-gray-600 mb-2">Bobot</div>
                    <div class="text-2xl font-bold text-gray-900">{{ $ticket->weight ?? 5 }}<span class="text-sm text-gray-500 font-normal">/10</span></div>
                </div>
            </div>

            {{-- Assignment Info --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Penugasan</h3>
                
                @if($ticket->claimedBy)
                    <div class="mb-4">
                        <div class="text-sm text-gray-600 mb-2">Dikerjakan oleh</div>
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center mr-2">
                                <span class="text-indigo-600 font-semibold text-sm">{{ strtoupper(substr($ticket->claimedBy->name, 0, 1)) }}</span>
                            </div>
                            <span class="font-medium text-gray-900">{{ $ticket->claimedBy->name }}</span>
                        </div>
                    </div>

                    @if($ticket->claimed_at)
                        <div class="text-sm text-gray-600">
                            Diklaim pada: {{ $ticket->claimed_at->format('d M Y, H:i') }}
                        </div>
                    @endif
                @elseif($ticket->target_role)
                    <div class="mb-2">
                        <div class="text-sm text-gray-600 mb-1">Target Role</div>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            {{ ucfirst($ticket->target_role) }}
                        </span>
                    </div>
                @elseif($ticket->target_user_id)
                    <div class="text-sm text-gray-600">
                        Ditugaskan ke user tertentu
                    </div>
                @else
                    <div class="text-gray-500 italic">Belum ada yang mengambil</div>
                @endif
            </div>

            {{-- Dates --}}
            @if($ticket->due_date || $ticket->started_at || $ticket->completed_at)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Timeline</h3>
                    
                    @if($ticket->due_date)
                        <div class="mb-3">
                            <div class="text-sm text-gray-600">Deadline</div>
                            <div class="font-medium text-gray-900">{{ $ticket->due_date->format('d M Y') }}</div>
                        </div>
                    @endif

                    @if($ticket->started_at)
                        <div class="mb-3">
                            <div class="text-sm text-gray-600">Dimulai</div>
                            <div class="font-medium text-gray-900">{{ $ticket->started_at->format('d M Y, H:i') }}</div>
                        </div>
                    @endif

                    @if($ticket->completed_at)
                        <div>
                            <div class="text-sm text-gray-600">Selesai</div>
                            <div class="font-medium text-gray-900">{{ $ticket->completed_at->format('d M Y, H:i') }}</div>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>

    {{-- Actions --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Aksi</h3>
        
        <div class="flex flex-wrap gap-3">
            @if($ticket->status === 'todo' && !$ticket->claimedBy && $ticket->canBeClaimedBy(auth()->user()))
                <form action="{{ route('tickets.claim', $ticket) }}" method="POST">
                    @csrf
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Klaim Tiket
                    </button>
                </form>
            @endif

            @if($ticket->claimedBy && $ticket->claimedBy->id === auth()->id())
                @if($ticket->status === 'todo')
                    <form action="{{ route('tickets.start', $ticket) }}" method="POST">
                        @csrf
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Mulai Kerjakan
                        </button>
                    </form>
                @endif

                @if($ticket->status === 'doing')
                    <form action="{{ route('tickets.complete', $ticket) }}" method="POST">
                        @csrf
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Tandai Selesai
                        </button>
                    </form>
                @endif

                <form action="{{ route('tickets.unclaim', $ticket) }}" method="POST">
                    @csrf
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Batalkan Klaim
                    </button>
                </form>
            @endif

            @can('update', $ticket->project ?? new \App\Models\Project)
                @if($ticket->status === 'doing')
                    <form action="{{ route('tickets.setTodo', $ticket) }}" method="POST">
                        @csrf
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            Kembalikan ke Todo
                        </button>
                    </form>
                @endif
            @endcan
        </div>
    </div>
</div>
@endsection
