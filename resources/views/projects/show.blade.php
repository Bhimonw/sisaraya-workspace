@extends('layouts.app')

@section('content')
<div class="relative" x-data="{ 
    activeTab: 'overview',
    showTicketModal: false,
    selectedTicket: null,
    showTicket(ticket) {
        this.selectedTicket = ticket;
        this.showTicketModal = true;
    },
    init() {
        console.log('✅ Alpine initialized with:', {
            activeTab: this.activeTab,
            showTicketModal: this.showTicketModal,
            selectedTicket: this.selectedTicket
        });
        // Watch for tab changes to refresh calendar
        this.$watch('activeTab', (value) => {
            if (value === 'overview') {
                // Trigger calendar refresh after a short delay
                setTimeout(() => {
                    if (window.projectCalendar) {
                        console.log('📐 Tab switched to overview - refreshing calendar');
                        // Tui Calendar doesn't need render() call
                        // Just log that tab is visible
                    }
                }, 200);
            }
        });
    }
}">
    {{-- Project Header --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
            <div class="flex-1">
                <div class="flex items-center gap-3 mb-2">
                    <h1 class="text-3xl font-bold text-gray-900">{{ $project->name }}</h1>
                    {{-- Status Badge --}}
                    @php
                        $statusColor = \App\Models\Project::getStatusColor($project->status);
                        $colorClasses = [
                            'gray' => 'bg-gray-100 text-gray-700 border-gray-300',
                            'blue' => 'bg-blue-100 text-blue-700 border-blue-300',
                            'yellow' => 'bg-yellow-100 text-yellow-700 border-yellow-300',
                            'green' => 'bg-green-100 text-green-700 border-green-300',
                        ];
                    @endphp
                    <span class="inline-flex items-center px-3 py-1 text-sm font-medium rounded-full border {{ $colorClasses[$statusColor] ?? 'bg-gray-100 text-gray-700 border-gray-300' }}">
                        {{ \App\Models\Project::getStatusLabel($project->status) }}
                    </span>
                </div>
                <p class="text-gray-600 text-base leading-relaxed mb-3">{{ $project->description }}</p>
                
                {{-- Project Timeline --}}
                @if($project->start_date || $project->end_date)
                <div class="flex items-center gap-2 text-sm">
                    <svg class="h-5 w-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span class="font-semibold text-gray-700">Timeline:</span>
                    <span class="text-indigo-600 font-medium">
                        @if($project->start_date)
                            {{ $project->start_date->format('d M Y') }}
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                        <span class="mx-1 text-gray-400">→</span>
                        @if($project->end_date)
                            {{ $project->end_date->format('d M Y') }}
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </span>
                    @if($project->start_date && $project->end_date)
                        @php
                            $duration = $project->start_date->diffInDays($project->end_date);
                        @endphp
                        <span class="text-xs text-gray-500 bg-gray-100 px-2 py-0.5 rounded-full">
                            {{ $duration }} hari
                        </span>
                    @endif
                </div>
                @else
                <div class="flex items-center gap-2 text-sm text-gray-400">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span class="italic">Tanpa batas waktu tertentu</span>
                </div>
                @endif
            </div>
            
            {{-- Members Preview --}}
            <div class="flex items-center gap-4 lg:ml-6">
                <div class="text-sm text-gray-500 font-medium">{{ $project->members->count() + 1 }} Members</div>
                <div class="flex -space-x-2">
                    @foreach($project->members->take(5) as $member)
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 text-white text-sm font-semibold flex items-center justify-center border-2 border-white shadow-sm" 
                             title="{{ $member->name }}">
                            {{ strtoupper(substr($member->name, 0, 1)) }}
                        </div>
                    @endforeach
                    @if($project->members->count() > 4)
                        <div class="w-10 h-10 rounded-full bg-gray-200 text-gray-600 text-xs font-semibold flex items-center justify-center border-2 border-white shadow-sm" 
                             title="{{ $project->members->count() - 4 }} more">
                            +{{ $project->members->count() - 4 }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Tab Navigation --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
        <div class="flex border-b border-gray-200 overflow-x-auto">
            {{-- Overview Tab --}}
            <button @click="activeTab = 'overview'" 
                    :class="activeTab === 'overview' ? 'border-b-2 border-violet-600 text-violet-600' : 'text-gray-600 hover:text-gray-800'"
                    class="px-6 py-3 font-medium text-sm whitespace-nowrap transition-all duration-200">
                <div class="flex items-center gap-2">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <span>Overview & Tickets</span>
                    <span class="ml-1 px-2 py-0.5 text-xs rounded-full bg-gray-100">{{ $project->tickets->count() }}</span>
                </div>
            </button>

            {{-- Members Tab (Visible to All) --}}
            <button @click="activeTab = 'members'" 
                    :class="activeTab === 'members' ? 'border-b-2 border-violet-600 text-violet-600' : 'text-gray-600 hover:text-gray-800'"
                    class="px-6 py-3 font-medium text-sm whitespace-nowrap transition-all duration-200">
                <div class="flex items-center gap-2">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <span>Kelola Member</span>
                    <span class="ml-1 px-2 py-0.5 text-xs rounded-full bg-gray-100">{{ $project->members->count() + 1 }}</span>
                </div>
            </button>

            {{-- Events Tab (Visible to All) --}}
            <button @click="activeTab = 'events'" 
                    :class="activeTab === 'events' ? 'border-b-2 border-violet-600 text-violet-600' : 'text-gray-600 hover:text-gray-800'"
                    class="px-6 py-3 font-medium text-sm whitespace-nowrap transition-all duration-200">
                <div class="flex items-center gap-2">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span>Event Proyek</span>
                    <span class="ml-1 px-2 py-0.5 text-xs rounded-full bg-gray-100">{{ $project->events->count() }}</span>
                </div>
            </button>

            {{-- Project Settings Tab (PM or Admin) --}}
            @if($project->canManage(Auth::user()))
            <button @click="activeTab = 'settings'" 
                    :class="activeTab === 'settings' ? 'border-b-2 border-violet-600 text-violet-600' : 'text-gray-600 hover:text-gray-800'"
                    class="px-6 py-3 font-medium text-sm whitespace-nowrap transition-all duration-200">
                <div class="flex items-center gap-2">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span>Kelola Proyek</span>
                </div>
            </button>
            @endif

            {{-- Create Ticket Tab (PM or Admin) --}}
            @if($project->canManage(Auth::user()))
            <button @click="activeTab = 'create-ticket'" 
                    :class="activeTab === 'create-ticket' ? 'border-b-2 border-violet-600 text-violet-600' : 'text-gray-600 hover:text-gray-800'"
                    class="px-6 py-3 font-medium text-sm whitespace-nowrap transition-all duration-200">
                <div class="flex items-center gap-2">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    <span>Buat Tiket Baru</span>
                </div>
            </button>
            @endif
        </div>
    </div>

    {{-- Tab Content --}}
    <div>
        {{-- OVERVIEW TAB --}}
        <div x-show="activeTab === 'overview'" x-transition>
        
        {{-- Grid Layout: Main Content (Left 2/3) + Sidebar (Right 1/3) --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Left Column: Tiket, Kanban, Event (2 columns = 2/3 width) --}}
            <div class="lg:col-span-2 space-y-6">
                
                {{-- Tiket Tersedia Section --}}
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-600 to-cyan-600 px-4 py-3">
                        <div class="flex items-center gap-2">
                            <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            <h3 class="font-semibold text-white">Tiket Tersedia untuk Anda</h3>
                            <span class="text-xs px-2 py-0.5 bg-white/20 rounded-full text-white">
                                @php
                                    $availableTickets = $project->tickets->filter(function($ticket) {
                                        // Tampilkan tiket yang:
                                        // 1. Belum di-claim dan bisa di-claim oleh user, ATAU
                                        // 2. Sudah di-claim oleh current user (untuk mulai/selesai)
                                        return (!$ticket->isClaimed() && $ticket->canBeClaimedBy(auth()->user())) 
                                            || ($ticket->claimed_by === auth()->id());
                                    });
                                @endphp
                                {{ $availableTickets->count() }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="p-4">
                        @if($availableTickets->count() > 0)
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 max-h-[400px] overflow-y-auto">
                                @foreach($availableTickets as $ticket)
                                <div class="p-3 bg-gradient-to-br from-gray-50 to-blue-50 rounded-lg border border-gray-200 hover:border-blue-400 hover:shadow-md transition-all">
                                    <div class="font-medium text-sm text-gray-900 mb-1">{{ $ticket->title }}</div>
                                    @if($ticket->description)
                                        <div class="text-xs text-gray-600 mb-2 line-clamp-2">{{ Str::limit($ticket->description, 80) }}</div>
                                    @endif
                                    <div class="flex items-center justify-between flex-wrap gap-2">
                                        <div class="flex flex-wrap gap-1">
                                            <span class="text-[10px] px-2 py-0.5 bg-blue-100 text-blue-700 rounded-full">
                                                {{ ucfirst($ticket->status) }}
                                            </span>
                                            @if($ticket->target_role)
                                                <span class="text-[10px] px-2 py-0.5 bg-purple-100 text-purple-700 rounded-full">
                                                    {{ \App\Models\Ticket::getAvailableRoles()[$ticket->target_role] ?? $ticket->target_role }}
                                                </span>
                                            @endif
                                        </div>
                                        <div class="flex gap-1">
                                            {{-- Detail Button --}}
                                            <button 
                                                @click="showTicket({
                                                    id: {{ $ticket->id }},
                                                    title: {{ \Illuminate\Support\Js::from($ticket->title) }},
                                                    description: {{ \Illuminate\Support\Js::from($ticket->description) }},
                                                    status: '{{ $ticket->status }}',
                                                    context: '{{ $ticket->context }}',
                                                    target_role: {{ \Illuminate\Support\Js::from($ticket->target_role ? (\App\Models\Ticket::getAvailableRoles()[$ticket->target_role] ?? $ticket->target_role) : null) }},
                                                    claimed_by: {{ $ticket->claimed_by ?? 'null' }},
                                                    claimed_by_name: {{ \Illuminate\Support\Js::from($ticket->claimedBy?->name) }},
                                                    created_by_name: {{ \Illuminate\Support\Js::from($ticket->creator?->name) }},
                                                    due_date: {{ \Illuminate\Support\Js::from($ticket->due_date ? $ticket->due_date->format('d M Y') : null) }},
                                                    created_at: '{{ $ticket->created_at->format('d M Y H:i') }}',
                                                    event_title: {{ \Illuminate\Support\Js::from($ticket->projectEvent?->title) }}
                                                })"
                                                class="text-xs px-2 py-1 bg-gray-100 text-gray-700 rounded hover:bg-gray-200 transition">
                                                Detail
                                            </button>

                                            {{-- Action Button Based on Status --}}
                                            @if(!$ticket->isClaimed())
                                                {{-- Unclaimed: Show Ambil button --}}
                                                <form method="POST" action="{{ route('tickets.claim', $ticket) }}">
                                                    @csrf
                                                    <button type="submit" class="text-xs px-3 py-1 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                                                        🎯 Ambil
                                                    </button>
                                                </form>
                                            @elseif($ticket->claimed_by === auth()->id())
                                                {{-- Claimed by current user --}}
                                                @if($ticket->status === 'todo')
                                                    <form method="POST" action="{{ route('tickets.start', $ticket) }}">
                                                        @csrf
                                                        <button type="submit" class="text-xs px-3 py-1 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium">
                                                            ▶️ Mulai
                                                        </button>
                                                    </form>
                                                @elseif($ticket->status === 'doing')
                                                    <form method="POST" action="{{ route('tickets.complete', $ticket) }}">
                                                        @csrf
                                                        <button type="submit" class="text-xs px-3 py-1 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium">
                                                            ✅ Selesai
                                                        </button>
                                                    </form>
                                                @elseif($ticket->status === 'done')
                                                    <span class="text-xs px-3 py-1 bg-green-100 text-green-700 rounded-lg font-medium">
                                                        🎉 Selesai
                                                    </span>
                                                @endif
                                            @else
                                                {{-- Claimed by other user --}}
                                                <span class="text-[10px] text-gray-500">
                                                    Diambil: {{ $ticket->claimedBy?->name }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8 text-gray-500">
                                <svg class="h-12 w-12 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <p class="text-xs text-gray-400 font-medium">Tidak ada tiket tersedia</p>
                                <p class="text-[10px] text-gray-400 mt-1">untuk role Anda saat ini</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Kanban Board untuk Member (Tiket Saya) --}}
                @cannot('update', $project)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    <div class="bg-gradient-to-r from-purple-600 to-pink-600 px-4 py-3">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                <h3 class="font-semibold text-white">Tiket Saya</h3>
                            </div>
                            @php
                                $myTickets = $project->tickets->where('claimed_by', auth()->id());
                            @endphp
                            <span class="text-xs px-2 py-0.5 bg-white/20 rounded-full text-white">{{ $myTickets->count() }} tiket</span>
                        </div>
                    </div>

                    <div class="p-4">
                        {{-- Kanban Columns --}}
                        <div class="grid grid-cols-1 lg:grid-cols-4 gap-4">
                @foreach([
                    'blackout' => ['label' => 'Blackout', 'color' => 'gray', 'icon' => 'M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 715.636 5.636m12.728 12.728L5.636 5.636'],
                    'todo' => ['label' => 'To Do', 'color' => 'yellow', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
                    'doing' => ['label' => 'Doing', 'color' => 'blue', 'icon' => 'M13 10V3L4 14h7v7l9-11h-7z'],
                    'done' => ['label' => 'Done', 'color' => 'green', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z']
                ] as $key => $config)
                    @php
                        $colorClasses = [
                            'yellow' => 'bg-yellow-50 border-yellow-200',
                            'blue' => 'bg-blue-50 border-blue-200',
                            'green' => 'bg-green-50 border-green-200',
                            'gray' => 'bg-gray-50 border-gray-300',
                        ];
                        $headerColors = [
                            'yellow' => 'bg-yellow-100 text-yellow-800',
                            'blue' => 'bg-blue-100 text-blue-800',
                            'green' => 'bg-green-100 text-green-800',
                            'gray' => 'bg-gray-600 text-white',
                        ];
                    @endphp
                    <div class="rounded-lg border-2 {{ $colorClasses[$config['color']] }}">
                        <div class="px-3 py-2 {{ $headerColors[$config['color']] }} rounded-t-lg">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $config['icon'] }}"/>
                                    </svg>
                                    <h3 class="font-semibold text-sm">{{ $config['label'] }}</h3>
                                </div>
                                <span class="text-xs px-2 py-0.5 bg-white/50 rounded-full">
                                    {{ $myTickets->where('status', $key)->count() }}
                                </span>
                            </div>
                        </div>
                        <div class="p-3 space-y-2 max-h-[60vh] overflow-y-auto">
                            @forelse($myTickets->where('status', $key) as $ticket)
                                <div class="bg-white p-3 rounded-lg shadow-sm border border-gray-200 hover:shadow-md hover:border-{{ $config['color'] }}-400 transition-all">
                                    <div class="space-y-2">
                                        <div class="font-medium text-sm">{{ $ticket->title }}</div>
                                        <div class="text-xs text-gray-500">{{ Str::limit($ticket->description, 80) }}</div>
                                        
                                        <div class="flex flex-wrap gap-1">
                                            {{-- Context Badge --}}
                                            @php
                                                $contextColor = \App\Models\Ticket::getContextColor($ticket->context);
                                                $contextColorClasses = [
                                                    'gray' => 'bg-gray-100 text-gray-700',
                                                    'indigo' => 'bg-indigo-100 text-indigo-700',
                                                    'blue' => 'bg-blue-100 text-blue-700',
                                                ];
                                            @endphp
                                            <span class="text-xs px-2 py-0.5 rounded {{ $contextColorClasses[$contextColor] ?? 'bg-gray-100 text-gray-700' }}">
                                                {{ \App\Models\Ticket::getContextLabel($ticket->context) }}
                                            </span>
                                            
                                            {{-- Event Name if context is event --}}
                                            @if($ticket->context === 'event' && $ticket->projectEvent)
                                                <span class="text-xs px-2 py-0.5 bg-indigo-50 text-indigo-600 rounded border border-indigo-200">
                                                    {{ $ticket->projectEvent->title }}
                                                </span>
                                            @endif
                                        </div>

                                        {{-- Action Buttons --}}
                                        <div class="flex items-center gap-2 pt-2 border-t border-gray-100">
                                            {{-- Detail Button --}}
                                            <button 
                                                @click="showTicket({
                                                    id: {{ $ticket->id }},
                                                    title: {{ \Illuminate\Support\Js::from($ticket->title) }},
                                                    description: {{ \Illuminate\Support\Js::from($ticket->description) }},
                                                    status: '{{ $ticket->status }}',
                                                    context: '{{ $ticket->context }}',
                                                    target_role: {{ \Illuminate\Support\Js::from($ticket->target_role ? (\App\Models\Ticket::getAvailableRoles()[$ticket->target_role] ?? $ticket->target_role) : null) }},
                                                    claimed_by: {{ $ticket->claimed_by ?? 'null' }},
                                                    claimed_by_name: {{ \Illuminate\Support\Js::from($ticket->claimedBy?->name) }},
                                                    created_by_name: {{ \Illuminate\Support\Js::from($ticket->creator?->name) }},
                                                    due_date: {{ \Illuminate\Support\Js::from($ticket->due_date ? $ticket->due_date->format('d M Y') : null) }},
                                                    created_at: '{{ $ticket->created_at->format('d M Y H:i') }}',
                                                    event_title: {{ \Illuminate\Support\Js::from($ticket->projectEvent?->title) }}
                                                })"
                                                class="text-xs px-3 py-1.5 bg-gray-100 text-gray-700 rounded hover:bg-gray-200 transition font-medium">
                                                <svg class="h-3 w-3 inline mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                                Detail
                                            </button>

                                            {{-- Status-based buttons --}}
                                            @if($ticket->status === 'todo')
                                                <form method="POST" action="{{ route('tickets.start', $ticket) }}" class="flex-1">
                                                    @csrf
                                                    <button type="submit" class="w-full text-xs px-3 py-1.5 bg-green-600 text-white rounded hover:bg-green-700 transition font-medium">
                                                        ▶️ Mulai Kerja
                                                    </button>
                                                </form>
                                            @elseif($ticket->status === 'doing')
                                                <form method="POST" action="{{ route('tickets.complete', $ticket) }}" class="flex-1">
                                                    @csrf
                                                    <button type="submit" class="w-full text-xs px-3 py-1.5 bg-green-600 text-white rounded hover:bg-green-700 transition font-medium">
                                                        ✅ Selesai
                                                    </button>
                                                </form>
                                            @elseif($ticket->status === 'done')
                                                <span class="flex-1 text-center text-xs px-3 py-1.5 bg-green-100 text-green-700 rounded font-medium">
                                                    🎉 Selesai
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-8 text-gray-400">
                                    <svg class="h-8 w-8 mx-auto text-gray-300 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                    </svg>
                                    <p class="text-xs">Tidak ada tiket</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                @endforeach
                        </div>
                    </div>
                </div>{{-- End Kanban Board untuk Member --}}
                @endcannot

                {{-- Kanban Section untuk PM/Admin --}}
                @can('update', $project)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    <div class="bg-gradient-to-r from-green-600 to-teal-600 px-4 py-3">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                <h3 class="font-semibold text-white">Kanban Board - Semua Tiket</h3>
                            </div>
                            <span class="text-xs px-2 py-0.5 bg-white/20 rounded-full text-white">{{ $project->tickets->count() }} tiket</span>
                        </div>
                    </div>

                    <div class="p-4">
                        {{-- Kanban Columns --}}
                        <div class="grid grid-cols-1 lg:grid-cols-4 gap-4">
                @foreach([
                    'blackout' => ['label' => 'Blackout', 'color' => 'gray', 'icon' => 'M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 715.636 5.636m12.728 12.728L5.636 5.636'],
                    'todo' => ['label' => 'To Do', 'color' => 'yellow', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
                    'doing' => ['label' => 'Doing', 'color' => 'blue', 'icon' => 'M13 10V3L4 14h7v7l9-11h-7z'],
                    'done' => ['label' => 'Done', 'color' => 'green', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z']
                ] as $key => $config)
                    @php
                        $colorClasses = [
                            'yellow' => 'bg-yellow-50 border-yellow-200',
                            'blue' => 'bg-blue-50 border-blue-200',
                            'green' => 'bg-green-50 border-green-200',
                            'gray' => 'bg-gray-50 border-gray-300',
                        ];
                        $headerColors = [
                            'yellow' => 'bg-yellow-100 text-yellow-800',
                            'blue' => 'bg-blue-100 text-blue-800',
                            'green' => 'bg-green-100 text-green-800',
                            'gray' => 'bg-gray-600 text-white',
                        ];
                    @endphp
                    <div class="rounded-lg border-2 {{ $colorClasses[$config['color']] }}">
                        <div class="px-3 py-2 {{ $headerColors[$config['color']] }} rounded-t-lg">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $config['icon'] }}"/>
                                    </svg>
                                    <h3 class="font-semibold text-sm">{{ $config['label'] }}</h3>
                                </div>
                                <span class="text-xs px-2 py-0.5 bg-white/50 rounded-full">
                                    {{ $project->tickets->where('status', $key)->count() }}
                                </span>
                            </div>
                        </div>
                        <div class="p-3 space-y-2 max-h-[60vh] overflow-y-auto">
                            @forelse($project->tickets->where('status', $key) as $ticket)
                                <div class="bg-white p-3 rounded-lg shadow-sm border border-gray-200 hover:shadow-md hover:border-{{ $config['color'] }}-400 transition-all">
                                    <div class="space-y-2">
                                        <div class="font-medium text-sm">{{ $ticket->title }}</div>
                                        <div class="text-xs text-gray-500">{{ Str::limit($ticket->description, 80) }}</div>
                                        
                                        <div class="flex flex-wrap gap-1">
                                            {{-- Context Badge --}}
                                            @php
                                                $contextColor = \App\Models\Ticket::getContextColor($ticket->context);
                                                $contextColorClasses = [
                                                    'gray' => 'bg-gray-100 text-gray-700',
                                                    'indigo' => 'bg-indigo-100 text-indigo-700',
                                                    'blue' => 'bg-blue-100 text-blue-700',
                                                ];
                                            @endphp
                                            <span class="text-xs px-2 py-0.5 rounded {{ $contextColorClasses[$contextColor] ?? 'bg-gray-100 text-gray-700' }}">
                                                {{ \App\Models\Ticket::getContextLabel($ticket->context) }}
                                            </span>
                                            
                                            {{-- Event Name if context is event --}}
                                            @if($ticket->context === 'event' && $ticket->projectEvent)
                                                <span class="text-xs px-2 py-0.5 bg-indigo-50 text-indigo-600 rounded border border-indigo-200">
                                                    {{ $ticket->projectEvent->title }}
                                                </span>
                                            @endif
                                        </div>
                                        
                                        {{-- Target Role Badge --}}
                                        @if($ticket->target_role)
                                            <div class="flex items-center gap-1">
                                                <svg class="h-3 w-3 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                                </svg>
                                                <span class="text-xs px-2 py-0.5 bg-purple-100 text-purple-700 rounded">
                                                    {{ \App\Models\Ticket::getAvailableRoles()[$ticket->target_role] ?? $ticket->target_role }}
                                                </span>
                                            </div>
                                        @endif

                                        {{-- Claimed By Info --}}
                                        @if($ticket->isClaimed())
                                            <div class="flex items-center gap-1">
                                                <svg class="h-3 w-3 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                </svg>
                                                <span class="text-xs text-green-700">
                                                    {{ $ticket->claimedBy->name }}
                                                </span>
                                            </div>
                                        @endif

                                        {{-- Action Buttons --}}
                                        <div class="flex items-center gap-2 pt-2 border-t border-gray-100">
                                            {{-- Detail Button --}}
                                            <button 
                                                @click="showTicket({
                                                    id: {{ $ticket->id }},
                                                    title: {{ \Illuminate\Support\Js::from($ticket->title) }},
                                                    description: {{ \Illuminate\Support\Js::from($ticket->description) }},
                                                    status: '{{ $ticket->status }}',
                                                    context: '{{ $ticket->context }}',
                                                    target_role: {{ \Illuminate\Support\Js::from($ticket->target_role ? (\App\Models\Ticket::getAvailableRoles()[$ticket->target_role] ?? $ticket->target_role) : null) }},
                                                    claimed_by: {{ $ticket->claimed_by ?? 'null' }},
                                                    claimed_by_name: {{ \Illuminate\Support\Js::from($ticket->claimedBy?->name) }},
                                                    created_by_name: {{ \Illuminate\Support\Js::from($ticket->creator?->name) }},
                                                    due_date: {{ \Illuminate\Support\Js::from($ticket->due_date ? $ticket->due_date->format('d M Y') : null) }},
                                                    created_at: '{{ $ticket->created_at->format('d M Y H:i') }}',
                                                    event_title: {{ \Illuminate\Support\Js::from($ticket->projectEvent?->title) }}
                                                })"
                                                class="text-xs px-3 py-1.5 bg-gray-100 text-gray-700 rounded hover:bg-gray-200 transition font-medium">
                                                <svg class="h-3 w-3 inline mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                                Detail
                                            </button>

                                            {{-- Status = BLACKOUT --}}
                                            @if($ticket->status === 'blackout')
                                                @if($ticket->claimed_by === auth()->id())
                                                    <form method="POST" action="{{ route('tickets.setTodo', $ticket) }}" class="flex-1">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="w-full text-xs px-3 py-1.5 bg-gradient-to-r from-yellow-500 to-yellow-600 text-white rounded hover:from-yellow-600 hover:to-yellow-700 transition font-medium shadow-sm">
                                                            🚀 Set Todo
                                                        </button>
                                                    </form>
                                                @endif

                                            {{-- Status = TODO --}}
                                            @elseif($ticket->status === 'todo')
                                                {{-- Not Claimed: Show "Ambil" button --}}
                                                @if(!$ticket->isClaimed())
                                                    @if($ticket->canBeClaimedBy(auth()->user()))
                                                        <form method="POST" action="{{ route('tickets.claim', $ticket) }}" class="flex-1">
                                                            @csrf
                                                            <button type="submit" class="w-full text-xs px-3 py-1.5 bg-blue-600 text-white rounded hover:bg-blue-700 transition font-medium">
                                                                🎯 Ambil Tiket
                                                            </button>
                                                        </form>
                                                    @else
                                                        <span class="text-xs text-gray-400 flex-1 text-center">
                                                            Tersedia untuk role tertentu
                                                        </span>
                                                    @endif
                                                @else
                                                    {{-- Claimed by current user: Show "Mulai" button --}}
                                                    @if($ticket->claimed_by === auth()->id())
                                                        <form method="POST" action="{{ route('tickets.start', $ticket) }}" class="flex-1">
                                                            @csrf
                                                            <button type="submit" class="w-full text-xs px-3 py-1.5 bg-green-600 text-white rounded hover:bg-green-700 transition font-medium">
                                                                ▶️ Mulai Kerja
                                                            </button>
                                                        </form>
                                                    @endif
                                                @endif

                                            {{-- Status = DOING --}}
                                            @elseif($ticket->status === 'doing')
                                                @if($ticket->claimed_by === auth()->id())
                                                    <form method="POST" action="{{ route('tickets.complete', $ticket) }}" class="flex-1">
                                                        @csrf
                                                        <button type="submit" class="w-full text-xs px-3 py-1.5 bg-green-600 text-white rounded hover:bg-green-700 transition font-medium">
                                                            ✅ Selesai
                                                        </button>
                                                    </form>
                                                @endif

                                            {{-- Status = DONE --}}
                                            @elseif($ticket->status === 'done')
                                                <span class="flex-1 text-center text-xs px-3 py-1.5 bg-green-100 text-green-700 rounded font-medium">
                                                    🎉 Selesai
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-8 text-gray-400">
                                    <svg class="h-8 w-8 mx-auto text-gray-300 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                    </svg>
                                    <p class="text-xs">Tidak ada tiket</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                @endforeach
                        </div>
                    </div>
                </div>{{-- End Kanban Board Card --}}
                @endcan
                
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

            </div>{{-- End Left Column --}}
            
            {{-- Right Sidebar: Kalender (1 column = 1/3 width) - Sticky --}}
            <div class="lg:col-span-1">
                <div class="lg:sticky lg:top-6">
                
                {{-- Kalender Proyek Widget --}}
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-4 py-3">
                        <div class="flex items-center gap-2">
                            <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <h3 class="font-semibold text-white">Kalender Proyek</h3>
                        </div>
                    </div>
                    
                    <div class="p-4">
                        {{-- Info Box --}}
                        <div class="text-xs space-y-1 mb-4 p-3 bg-blue-50 border-l-4 border-blue-500 rounded">
                            <p class="font-medium text-blue-900">📅 Kalender Proyek Ini:</p>
                            <ul class="text-[11px] text-blue-800 space-y-0.5 ml-2">
                                <li>• <strong>Timeline Proyek</strong> - Rentang waktu proyek (highlight biru muda)</li>
                                <li>• <strong>Event Proyek</strong> - Acara dan kegiatan proyek ini</li>
                                <li>• <strong>Tiket Proyek</strong> - Deadline tiket proyek ini</li>
                            </ul>
                            <p class="text-[11px] text-blue-700 mt-2 italic">
                                * Hanya menampilkan data dari proyek "{{ $project->name }}"
                            </p>
                        </div>
                        
                        {{-- Simple PHP Calendar (No JS Library!) --}}
                        <div class="bg-white rounded-lg border border-gray-200">
                            {{-- Calendar Header --}}
                            <div class="flex items-center justify-between p-4 border-b">
                                <button class="p-2 hover:bg-gray-100 rounded">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                    </svg>
                                </button>
                                <h3 class="font-semibold text-lg">{{ $calendar['month'] }} {{ $calendar['year'] }}</h3>
                                <button class="p-2 hover:bg-gray-100 rounded">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </button>
                            </div>
                            
                            {{-- Calendar Grid --}}
                            <div class="p-4">
                                {{-- Day Names --}}
                                <div class="grid grid-cols-7 gap-1 mb-2">
                                    @foreach(['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'] as $dayName)
                                        <div class="text-center font-semibold text-sm text-gray-600 py-2">
                                            {{ $dayName }}
                                        </div>
                                    @endforeach
                                </div>
                                
                                {{-- Calendar Weeks --}}
                                @foreach($calendar['weeks'] as $week)
                                    <div class="grid grid-cols-7 gap-1">
                                        @foreach($week as $day)
                                            @php
                                                // Check if this day is within project timeline
                                                $isInTimeline = false;
                                                $isTimelineStart = false;
                                                $isTimelineEnd = false;
                                                
                                                if ($day['day'] && $project->start_date && $project->end_date && $day['date']) {
                                                    $currentDate = $day['date']->format('Y-m-d');
                                                    $startDate = $project->start_date->format('Y-m-d');
                                                    $endDate = $project->end_date->format('Y-m-d');
                                                    
                                                    $isInTimeline = $currentDate >= $startDate && $currentDate <= $endDate;
                                                    $isTimelineStart = $currentDate === $startDate;
                                                    $isTimelineEnd = $currentDate === $endDate;
                                                }
                                            @endphp
                                            
                                            <div class="min-h-24 border rounded p-1 relative
                                                {{ $day['day'] ? ($isInTimeline ? 'bg-indigo-50 border-indigo-300' : 'bg-white') : 'bg-gray-50' }} 
                                                {{ isset($day['isToday']) && $day['isToday'] ? 'ring-2 ring-blue-500' : '' }}
                                                {{ $isTimelineStart ? 'border-l-4 border-l-indigo-600' : '' }}
                                                {{ $isTimelineEnd ? 'border-r-4 border-r-indigo-600' : '' }}">
                                                
                                                @if($day['day'])
                                                    {{-- Timeline indicator --}}
                                                    @if($isInTimeline)
                                                        <div class="absolute top-0 left-0 right-0 h-1 bg-indigo-400"></div>
                                                    @endif
                                                    
                                                    {{-- Day Number --}}
                                                    <div class="text-right">
                                                        <span class="inline-flex items-center justify-center w-6 h-6 text-sm font-medium 
                                                            {{ isset($day['isToday']) && $day['isToday'] ? 'bg-blue-500 text-white rounded-full' : ($isInTimeline ? 'text-indigo-900 font-bold' : 'text-gray-700') }}">
                                                            {{ $day['day'] }}
                                                        </span>
                                                    </div>
                                                    
                                                    {{-- Timeline labels --}}
                                                    @if($isTimelineStart)
                                                        <div class="text-[10px] font-bold text-indigo-700 px-1 mb-1">
                                                            📊 Mulai
                                                        </div>
                                                    @endif
                                                    @if($isTimelineEnd)
                                                        <div class="text-[10px] font-bold text-indigo-700 px-1 mb-1">
                                                            🏁 Selesai
                                                        </div>
                                                    @endif
                                                    
                                                    {{-- Events for this day --}}
                                                    <div class="mt-1 space-y-1">
                                                        @foreach(array_slice($day['events'], 0, 2) as $event)
                                                            <div class="text-xs px-1 py-0.5 rounded {{ \App\Helpers\CalendarHelper::getEventColorClass($event['type'], $event['status'] ?? null) }} text-white truncate" title="{{ $event['title'] }}">
                                                                {{ $event['title'] }}
                                                            </div>
                                                        @endforeach
                                                        @if(count($day['events']) > 2)
                                                            <div class="text-xs text-gray-500 px-1">
                                                                +{{ count($day['events']) - 2 }} lagi
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        
                        {{-- Legend --}}
                        <div class="mt-4 p-3 bg-gray-50 rounded-lg">
                            <p class="text-xs font-semibold text-gray-900 mb-2">Legenda Kalender</p>
                            
                            {{-- Timeline Highlight --}}
                            @if($project->start_date && $project->end_date)
                            <div class="mb-3 pb-3 border-b border-gray-200">
                                <p class="text-xs font-semibold text-indigo-900 mb-2">📊 Rentang Timeline Proyek:</p>
                                <div class="space-y-1.5 text-xs">
                                    <div class="flex items-center gap-2">
                                        <div class="w-12 h-6 rounded bg-indigo-50 border border-indigo-300 relative">
                                            <div class="absolute top-0 left-0 right-0 h-1 bg-indigo-400"></div>
                                        </div>
                                        <span class="text-gray-700">Background biru muda + garis atas</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <div class="px-2 py-0.5 bg-white rounded text-[10px] font-bold text-indigo-700 border-l-4 border-indigo-600">
                                            📊 Mulai
                                        </div>
                                        <span class="text-gray-700">Tanggal mulai proyek</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <div class="px-2 py-0.5 bg-white rounded text-[10px] font-bold text-indigo-700 border-r-4 border-indigo-600">
                                            🏁 Selesai
                                        </div>
                                        <span class="text-gray-700">Tanggal selesai proyek</span>
                                    </div>
                                </div>
                            </div>
                            @endif
                            
                            {{-- Event & Ticket Colors --}}
                            <div class="mb-3">
                                <p class="text-xs font-semibold text-gray-900 mb-2">Event & Tiket:</p>
                                <div class="grid grid-cols-2 gap-2 text-xs">
                                    <div class="flex items-center gap-2">
                                        <div class="w-3 h-3 rounded bg-purple-500"></div>
                                        <span class="text-gray-700">Event</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <div class="w-3 h-3 rounded bg-gray-500"></div>
                                        <span class="text-gray-700">To Do</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <div class="w-3 h-3 rounded bg-blue-500"></div>
                                        <span class="text-gray-700">Doing</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <div class="w-3 h-3 rounded bg-green-500"></div>
                                        <span class="text-gray-700">Done</span>
                                    </div>
                                </div>
                            </div>
                            
                            {{-- Project Status Colors (in timeline) --}}
                            <div class="pt-3 border-t border-gray-200">
                                <p class="text-xs font-semibold text-gray-900 mb-2">Warna Status Timeline:</p>
                                <div class="grid grid-cols-2 gap-2 text-xs">
                                    <div class="flex items-center gap-2">
                                        <div class="w-3 h-3 rounded bg-gray-600"></div>
                                        <span class="text-gray-700">Perencanaan</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <div class="w-3 h-3 rounded bg-indigo-600"></div>
                                        <span class="text-gray-700">Aktif</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <div class="w-3 h-3 rounded bg-yellow-600"></div>
                                        <span class="text-gray-700">Ditunda</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <div class="w-3 h-3 rounded bg-green-600"></div>
                                        <span class="text-gray-700">Selesai</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                </div>{{-- End Sticky Container --}}
            </div>{{-- End Right Sidebar --}}
        </div>{{-- End Grid Layout --}}

        {{-- Bottom Section: Evaluasi (Full Width) --}}
        <div class="mt-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-violet-600 to-purple-600 px-4 py-3">
                    <div class="flex items-center gap-2">
                        <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <h3 class="font-semibold text-white">Evaluasi Proyek</h3>
                    </div>
                </div>
                
                <div class="p-6">
                    @php
                        $evaluations = $project->evaluations()->orderBy('created_at', 'desc')->get();
                    @endphp
                    
                    @if($evaluations->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($evaluations as $evaluation)
                            <div class="border-l-4 border-violet-500 pl-4 py-3 bg-violet-50 rounded-r hover:shadow-md transition">
                                <p class="text-sm text-gray-800 leading-relaxed mb-2">{{ $evaluation->notes }}</p>
                                <div class="flex items-center gap-3 text-xs text-gray-500">
                                    <span class="font-medium">{{ $evaluation->user->name }}</span>
                                    <span>•</span>
                                    <span>{{ $evaluation->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12 text-gray-500">
                            <svg class="h-16 w-16 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <p class="text-sm font-medium text-gray-400">Belum ada evaluasi</p>
                            <p class="text-xs text-gray-400 mt-1">Catatan evaluasi dari Researcher akan muncul di sini</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>{{-- End Evaluasi Section --}}

        </div>{{-- End Overview Tab --}}

        {{-- PROJECT SETTINGS TAB --}}
        @if($project->canManage(Auth::user()))
        <div x-show="activeTab === 'settings'" x-transition>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-violet-600 to-purple-600 px-6 py-4">
                    <div class="flex items-center gap-3">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <div>
                            <h2 class="text-lg font-semibold text-white">Kelola Proyek</h2>
                            <p class="text-sm text-white/90">Ubah status, rentang waktu, dan pengaturan proyek lainnya</p>
                        </div>
                    </div>
                </div>
                
                <div class="p-6">
                    <form action="{{ route('projects.update', $project) }}" method="POST" class="space-y-6">
                        @csrf
                        @method('PUT')
                        
                        {{-- Informasi Dasar --}}
                        <div class="bg-gradient-to-br from-indigo-50 to-purple-50 rounded-xl p-6 border-2 border-indigo-200">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                                <svg class="h-5 w-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Informasi Dasar
                            </h3>
                            
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        Nama Proyek <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" 
                                           name="name" 
                                           value="{{ old('name', $project->name) }}"
                                           required
                                           class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition">
                                    @error('name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        Deskripsi <span class="text-red-500">*</span>
                                    </label>
                                    <textarea name="description" 
                                              rows="3"
                                              required
                                              class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition">{{ old('description', $project->description) }}</textarea>
                                    @error('description')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        {{-- Status & Visibilitas --}}
                        <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl p-6 border-2 border-blue-200">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                                <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Status & Visibilitas
                            </h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Status -->
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        Status Proyek <span class="text-red-500">*</span>
                                    </label>
                                    <select name="status" 
                                            required
                                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition">
                                        <option value="planning" {{ old('status', $project->status) === 'planning' ? 'selected' : '' }}>
                                            📋 Perencanaan
                                        </option>
                                        <option value="active" {{ old('status', $project->status) === 'active' ? 'selected' : '' }}>
                                            🚀 Aktif
                                        </option>
                                        <option value="on_hold" {{ old('status', $project->status) === 'on_hold' ? 'selected' : '' }}>
                                            ⏸️ Ditunda
                                        </option>
                                        <option value="completed" {{ old('status', $project->status) === 'completed' ? 'selected' : '' }}>
                                            ✅ Selesai
                                        </option>
                                    </select>
                                    @error('status')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Visibility -->
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        Visibilitas
                                    </label>
                                    <div class="flex items-center h-12 px-4 border-2 border-gray-300 rounded-xl bg-white">
                                        <input type="checkbox" 
                                               name="is_public" 
                                               value="1" 
                                               {{ old('is_public', $project->is_public) ? 'checked' : '' }}
                                               class="w-5 h-5 text-violet-600 border-gray-300 rounded focus:ring-violet-500">
                                        <label class="ml-3 text-sm font-medium text-gray-700">Proyek Publik (dapat dilihat semua)</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        {{-- Rentang Waktu --}}
                        <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-xl p-6 border-2 border-green-200">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                                <svg class="h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                Rentang Waktu Proyek (Opsional)
                            </h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Start Date -->
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Mulai</label>
                                    <input type="date" 
                                           name="start_date"
                                           value="{{ old('start_date', $project->start_date ? $project->start_date->format('Y-m-d') : '') }}"
                                           class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition">
                                    @error('start_date')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- End Date -->
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Selesai</label>
                                    <input type="date" 
                                           name="end_date"
                                           value="{{ old('end_date', $project->end_date ? $project->end_date->format('Y-m-d') : '') }}"
                                           class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition">
                                    @error('end_date')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            <p class="text-sm text-gray-600 mt-2">
                                💡 Kosongkan jika proyek tidak memiliki batas waktu tertentu
                            </p>
                        </div>
                        
                        {{-- Submit Button --}}
                        <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                            <a href="{{ route('projects.index') }}" 
                               class="px-6 py-3 bg-gray-100 text-gray-700 font-semibold rounded-xl hover:bg-gray-200 transition-all duration-300">
                                ← Kembali
                            </a>
                            <button type="submit" 
                                    class="px-8 py-3 bg-gradient-to-r from-violet-600 to-purple-600 text-white font-semibold rounded-xl hover:from-violet-700 hover:to-purple-700 hover:scale-105 active:scale-95 transition-all duration-300 shadow-lg hover:shadow-xl">
                                <span class="flex items-center gap-2">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Simpan Perubahan
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endif

        {{-- CREATE TICKET TAB --}}
        @if($project->canManage(Auth::user()))
        <div x-show="activeTab === 'create-ticket'" x-transition>
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200" x-data="{ context: 'proyek' }">
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-gray-900 flex items-center gap-3">
                    <svg class="h-8 w-8 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Buat Tiket Baru
                </h2>
                <p class="text-gray-600 mt-1">Tambahkan tiket untuk mengorganisir pekerjaan di project ini</p>
            </div>
            <form action="{{ route('projects.tickets.store', $project) }}" method="POST">
                @csrf
                <div class="space-y-4">
                    {{-- Context Selection (Hanya Proyek/Event) --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tipe Tiket <span class="text-red-500">*</span></label>
                        <div class="flex gap-4">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="context" value="event" x-model="context" class="text-purple-600" />
                                <span class="text-sm">Event</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="context" value="proyek" x-model="context" checked class="text-green-600" />
                                <span class="text-sm">Proyek</span>
                            </label>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">
                            <span x-show="context === 'event'">Tiket terikat pada event tertentu</span>
                            <span x-show="context === 'proyek'">Tiket terikat pada proyek ini</span>
                        </p>
                    </div>

                    {{-- Event Selection (only show if context is 'event') --}}
                    <div x-show="context === 'event'" x-collapse>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Event <span class="text-red-500">*</span></label>
                        <select name="project_event_id" class="w-full border border-gray-300 p-2 rounded focus:ring-2 focus:ring-green-500">
                            <option value="">-- Pilih Event --</option>
                            @foreach($project->events as $event)
                                <option value="{{ $event->id }}">{{ $event->title }} ({{ $event->start_date->format('d M Y') }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Judul Tiket <span class="text-red-500">*</span></label>
                            <input name="title" placeholder="Judul Tiket" required class="w-full border border-gray-300 p-2 rounded focus:ring-2 focus:ring-green-500 focus:border-transparent" />
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
                            <select name="status" class="w-full border border-gray-300 p-2 rounded focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                <option value="todo">To Do</option>
                                <option value="doing">Doing</option>
                                <option value="done">Done</option>
                                <option value="blackout">Blackout</option>
                            </select>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi (Opsional)</label>
                        <textarea name="description" placeholder="Jelaskan detail pekerjaan..." rows="3" class="w-full border border-gray-300 p-2 rounded focus:ring-2 focus:ring-green-500 focus:border-transparent"></textarea>
                    </div>
                    
                    {{-- Priority & Weight --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Prioritas <span class="text-red-500">*</span>
                            </label>
                            <select name="priority" required class="w-full border border-gray-300 p-2 rounded focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                <option value="low">🟢 Rendah</option>
                                <option value="medium" selected>🔵 Sedang</option>
                                <option value="high">🟠 Tinggi</option>
                                <option value="urgent">🔴 Mendesak</option>
                            </select>
                            <p class="text-xs text-gray-500 mt-1">Tingkat urgensi pekerjaan</p>
                        </div>
                        
                        <div x-data="{ 
                            weight: 5,
                            getLabel() {
                                if (this.weight <= 3) return { text: 'Ringan', color: 'text-green-600', bg: 'bg-green-100', border: 'border-green-300' };
                                if (this.weight <= 6) return { text: 'Sedang', color: 'text-yellow-600', bg: 'bg-yellow-100', border: 'border-yellow-300' };
                                return { text: 'Berat', color: 'text-red-600', bg: 'bg-red-100', border: 'border-red-300' };
                            }
                        }">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Bobot Kesulitan <span class="text-red-500">*</span>
                            </label>
                            
                            {{-- Weight Display Card --}}
                            <div class="mb-3 p-3 rounded-lg border-2 transition-all duration-200"
                                 :class="getLabel().bg + ' ' + getLabel().border">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-medium text-gray-700">Tingkat Kesulitan:</span>
                                    <div class="flex items-center gap-2">
                                        <span x-text="weight" 
                                              class="text-2xl font-bold"
                                              :class="getLabel().color"></span>
                                        <span class="text-lg font-semibold"
                                              :class="getLabel().color"
                                              x-text="'- ' + getLabel().text"></span>
                                    </div>
                                </div>
                            </div>
                            
                            {{-- Slider --}}
                            <div class="flex items-center gap-3">
                                <span class="text-sm text-gray-500 font-medium">1</span>
                                <input type="range" 
                                       name="weight" 
                                       min="1" 
                                       max="10" 
                                       value="5" 
                                       step="1"
                                       x-model="weight"
                                       class="flex-1 h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-blue-600">
                                <span class="text-sm text-gray-500 font-medium">10</span>
                            </div>
                            
                            <p class="text-xs text-gray-500 mt-2">
                                <span class="font-medium text-green-600">1-3:</span> Ringan • 
                                <span class="font-medium text-yellow-600">4-6:</span> Sedang • 
                                <span class="font-medium text-red-600">7-10:</span> Berat
                            </p>
                        </div>
                    </div>
                    
                    {{-- Target User Selection --}}
                    <div class="border-t pt-4">
                        <label class="block text-sm font-semibold text-gray-900 mb-3">
                            <svg class="h-5 w-5 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            Target User (Opsional)
                        </label>
                        <p class="text-xs text-gray-500 mb-3">Pilih user spesifik atau biarkan kosong untuk semua member</p>
                        
                        <div class="max-h-60 overflow-y-auto border border-gray-200 rounded-lg divide-y divide-gray-100">
                            @php
                                $projectMembers = $project->members->sortBy('name');
                                $allRoles = \App\Models\Ticket::getAllRoles();
                            @endphp
                            
                            @forelse($projectMembers as $member)
                                @php
                                    // Get member's permanent role from Spatie roles
                                    $permanentRole = $member->roles->first()?->name ?? null;
                                    
                                    // Get member's event role for this project
                                    $eventRolesArray = $member->pivot->event_roles ? json_decode($member->pivot->event_roles, true) : [];
                                    $eventRole = !empty($eventRolesArray) ? $eventRolesArray[0] : null;
                                @endphp
                                
                                <label class="flex items-center p-3 hover:bg-blue-50 cursor-pointer transition">
                                    <input type="radio" name="target_user_id" value="{{ $member->id }}" class="mr-3 text-green-600">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2">
                                            <span class="font-medium text-gray-900">{{ $member->name }}</span>
                                            <span class="text-xs text-gray-500">{{ $member->email }}</span>
                                        </div>
                                        <div class="flex flex-wrap gap-1 mt-1">
                                            {{-- Permanent Role Badge --}}
                                            @if($permanentRole)
                                                <span class="text-xs px-2 py-0.5 bg-blue-100 text-blue-700 rounded-full border border-blue-300">
                                                    🔒 {{ $allRoles[$permanentRole] ?? ucfirst($permanentRole) }}
                                                </span>
                                            @endif
                                            
                                            {{-- Event Role Badge --}}
                                            @if($eventRole)
                                                <span class="text-xs px-2 py-0.5 bg-amber-100 text-amber-700 rounded-full border border-amber-300">
                                                    ⏱️ {{ $allRoles[$eventRole] ?? ucfirst($eventRole) }}
                                                </span>
                                            @endif
                                            
                                            {{-- Project Role Badge --}}
                                            @if($member->pivot->role === 'admin')
                                                <span class="text-xs px-2 py-0.5 bg-green-100 text-green-700 rounded-full border border-green-300">
                                                    ⚡ Admin Project
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </label>
                            @empty
                                <div class="p-4 text-center text-gray-500 text-sm">
                                    Belum ada member di project ini
                                </div>
                            @endforelse
                        </div>
                        
                        <div class="mt-2 flex items-center gap-2">
                            <input type="radio" name="target_user_id" value="" checked class="text-green-600">
                            <label class="text-sm text-gray-600">Tidak ada target spesifik (semua member)</label>
                        </div>
                    </div>
                    
                    {{-- Target Role (as fallback) --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Target Role (Opsional)
                                <span class="text-xs text-gray-500">- jika tidak pilih user spesifik</span>
                            </label>
                            <select name="target_role" class="w-full border border-gray-300 p-2 rounded focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                <option value="">-- Semua Role --</option>
                                <optgroup label="Role Permanent">
                                    <option value="pm">🔒 Project Manager</option>
                                    <option value="hr">🔒 Human Resources</option>
                                    <option value="sekretaris">🔒 Sekretaris</option>
                                    <option value="bendahara">🔒 Bendahara</option>
                                </optgroup>
                                <optgroup label="Role Event (Project)">
                                    @php
                                        $eventRoles = \App\Models\Ticket::getEventRoles();
                                        $membersWithEventRoles = $project->members->filter(function($m) {
                                            $roles = $m->pivot->event_roles ? json_decode($m->pivot->event_roles, true) : [];
                                            return !empty($roles);
                                        });
                                    @endphp
                                    @foreach($eventRoles as $key => $label)
                                        @php
                                            $hasThisRole = $membersWithEventRoles->filter(function($m) use ($key) {
                                                $roles = json_decode($m->pivot->event_roles, true);
                                                return in_array($key, $roles);
                                            })->count();
                                        @endphp
                                        <option value="{{ $key }}">⏱️ {{ $label }} @if($hasThisRole > 0)({{ $hasThisRole }} member)@endif</option>
                                    @endforeach
                                </optgroup>
                            </select>
                            <p class="text-xs text-gray-500 mt-1">Filter berdasarkan role jika tidak pilih user spesifik</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Due Date (Opsional)</label>
                            <input type="date" name="due_date" class="w-full border border-gray-300 p-2 rounded focus:ring-2 focus:ring-green-500 focus:border-transparent" />
                        </div>
                    </div>
                </div>
                <div class="mt-3">
                    <button type="submit" class="w-full sm:w-auto bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded transition">
                        Buat Tiket
                    </button>
                </div>
            </form>
        </div>
        </div>{{-- End Create Ticket Tab --}}
        @endif
    
        {{-- MEMBERS TAB (Visible to All, but only PM can manage) --}}
        <div x-show="activeTab === 'members'" x-transition>
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-2 border-violet-100" x-data="memberManagement()">
        <div class="bg-gradient-to-r from-violet-600 to-blue-600 px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <div>
                        <h2 class="text-lg font-semibold text-white">
                            @if($project->canManageMembers(Auth::user()))
                                Kelola Tim Proyek
                            @else
                                Anggota Tim Proyek
                            @endif
                        </h2>
                        <p class="text-sm text-white/90">
                            @if($project->canManageMembers(Auth::user()))
                                Atur anggota, role, dan izin akses
                            @else
                                Lihat daftar anggota dan role mereka
                            @endif
                        </p>
                    </div>
                </div>
                @if($project->canManageMembers(Auth::user()))
                <button @click="showAddMember = !showAddMember" 
                        class="px-4 py-2 bg-white text-violet-600 font-semibold rounded-lg hover:bg-violet-50 hover:scale-105 active:scale-95 transition-all duration-300 shadow-lg">
                    <span x-show="!showAddMember" class="flex items-center gap-2">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Tambah Member
                    </span>
                    <span x-show="showAddMember" class="flex items-center gap-2">
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
            @if($project->canManageMembers(Auth::user()))
            {{-- Add Member Form (PM/HR/Admin Only) --}}
            <div x-show="showAddMember" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform -translate-y-2"
                 x-transition:enter-end="opacity-100 transform translate-y-0"
                 class="mb-6 p-6 bg-gradient-to-br from-violet-50 to-blue-50 rounded-xl border-2 border-violet-200 shadow-sm">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="h-5 w-5 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                    </svg>
                    Tambah Member Baru
                </h3>
                
                <form action="{{ route('projects.members.store', $project) }}" method="POST" class="space-y-4">
                    @csrf
                    
                    {{-- Info Box --}}
                    <div class="p-4 bg-blue-100 border-l-4 border-blue-500 rounded">
                        <div class="flex items-start gap-2">
                            <svg class="h-5 w-5 text-blue-600 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div class="flex-1 text-sm text-blue-900">
                                <p class="font-semibold mb-2">Tentang Role:</p>
                                <ul class="space-y-1 list-disc list-inside">
                                    <li><strong>Admin Project:</strong> Dapat membuat tiket dan event</li>
                                    <li><strong>Member:</strong> Dapat melihat dan mengambil tiket</li>
                                    <li><strong>Event Role:</strong> Bisa pilih role permanent atau event role untuk setiap member</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    @php
                        $availableUsers = \App\Models\User::where('id', '!=', $project->owner_id)
                            ->whereNotIn('id', $project->members->pluck('id'))
                            ->orderBy('name')
                            ->get();
                    @endphp

                    {{-- User List with Individual Role Selection --}}
                    <div class="space-y-3" x-data="{ checkAll: false, selectedUsers: [] }">
                        <div class="flex items-center justify-between mb-2">
                            <label class="block text-sm font-semibold text-gray-700">
                                Pilih User & Atur Role <span class="text-red-500">*</span>
                            </label>
                            <label class="flex items-center gap-2 text-sm font-medium text-indigo-600 cursor-pointer hover:text-indigo-800">
                                <input type="checkbox" 
                                       x-model="checkAll"
                                       @change="selectedUsers = checkAll ? {{ $availableUsers->pluck('id')->toJson() }} : []"
                                       class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                <span>Pilih Semua</span>
                            </label>
                        </div>

                        @if($availableUsers->count() > 0)
                        <div class="max-h-96 overflow-y-auto border-2 border-gray-200 rounded-xl divide-y divide-gray-100">
                            @foreach($availableUsers as $user)
                            <div class="p-4 hover:bg-gray-50 transition" 
                                 x-data="{ projectRole: 'member', eventRole: '' }"
                                 x-init="$watch('selectedUsers', value => { if (!value.includes({{ $user->id }})) { projectRole = 'member'; eventRole = ''; } })">
                                <div class="flex items-center gap-4">
                                    {{-- Checkbox --}}
                                    <div class="flex items-center">
                                        <input type="checkbox" 
                                               x-model="selectedUsers"
                                               name="user_ids[]" 
                                               value="{{ $user->id }}"
                                               class="w-5 h-5 text-violet-600 border-gray-300 rounded focus:ring-violet-500">
                                    </div>

                                    {{-- Avatar & Name --}}
                                    <div class="flex items-center gap-3 flex-1">
                                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-violet-600 to-blue-600 flex items-center justify-center text-white font-semibold">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                        <div class="flex-1">
                                            <p class="font-semibold text-gray-900">{{ $user->name }}</p>
                                            <p class="text-xs text-gray-500">{{ $user->email }}</p>
                                        </div>
                                    </div>

                                    {{-- Role Selection (shown when checked) --}}
                                    <div x-show="selectedUsers.includes({{ $user->id }})" 
                                         x-transition
                                         class="flex items-center gap-2 min-w-[400px]">
                                        {{-- Project Role --}}
                                        <div class="flex-1">
                                            <select x-model="projectRole"
                                                    :name="'project_role_' + {{ $user->id }}"
                                                    class="w-full text-sm border border-gray-300 p-2 rounded-lg focus:ring-2 focus:ring-violet-500">
                                                <option value="member">Member</option>
                                                <option value="admin">Admin</option>
                                            </select>
                                        </div>

                                        {{-- Event Role --}}
                                        <div class="flex-1">
                                            <select x-model="eventRole"
                                                    :name="'event_role_' + {{ $user->id }}"
                                                    class="w-full text-sm border border-gray-300 p-2 rounded-lg focus:ring-2 focus:ring-violet-500">
                                                <option value="">-- Pilih Event Role --</option>
                                                {{-- Only Event Roles, NOT Permanent Roles --}}
                                                <optgroup label="Role Event (Temporary)">
                                                    @foreach(\App\Models\Ticket::getEventRoles() as $key => $label)
                                                        <option value="{{ $key }}">{{ $label }}</option>
                                                    @endforeach
                                                </optgroup>
                                            </select>
                                            <p class="text-[10px] text-gray-500 mt-1">
                                                ℹ️ Role permanent (HR, PM, dll) tidak dapat diatur di project
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <div class="p-6 text-center text-gray-500 border-2 border-dashed border-gray-300 rounded-xl">
                            Semua user sudah menjadi member
                        </div>
                        @endif
                    </div>
                    
                    <div class="flex items-center gap-3 pt-2">
                        <button type="submit" 
                                class="px-6 py-3 bg-gradient-to-r from-violet-600 to-blue-600 text-white font-semibold rounded-xl hover:shadow-lg hover:scale-105 active:scale-95 transition-all duration-300">
                            <span class="flex items-center gap-2">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                Tambahkan Member
                            </span>
                        </button>
                        <button type="button" 
                                @click="showAddMember = false"
                                class="px-6 py-3 border-2 border-gray-300 text-gray-700 font-medium rounded-xl hover:bg-gray-50 transition">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
            @endif
            {{-- End Add Member Form (PM/HR/Admin Only) --}}

            {{-- Member List Header (Visible to All) --}}
            <div class="mb-4">
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide">
                    Daftar Anggota Tim ({{ $project->members->count() + 1 }})
                </h3>
            </div>

            {{-- Member Cards --}}
            <div class="space-y-3">
                {{-- Project Manager (Owner) --}}
                <div class="group relative overflow-hidden rounded-xl border-2 border-purple-200 bg-gradient-to-r from-purple-50 to-violet-50 p-4 hover:shadow-lg transition-all duration-300">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4 flex-1">
                            <div class="relative">
                                <div class="w-14 h-14 rounded-full bg-gradient-to-br from-purple-600 to-violet-600 text-white flex items-center justify-center font-bold text-xl shadow-lg">
                                    {{ strtoupper(substr($project->owner->name, 0, 1)) }}
                                </div>
                                <div class="absolute -bottom-1 -right-1 w-6 h-6 bg-yellow-400 rounded-full border-2 border-white flex items-center justify-center">
                                    <svg class="h-3 w-3 text-yellow-900" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-1">
                                <div class="font-bold text-gray-900 text-lg">{{ $project->owner->name }}</div>
                                <div class="text-sm text-gray-600">{{ $project->owner->email }}</div>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="px-4 py-2 text-sm font-bold rounded-full bg-gradient-to-r from-purple-600 to-violet-600 text-white shadow-md">
                                👑 Project Manager
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Other Members --}}
                @foreach($project->members->sortByDesc(function($member) { return $member->pivot->role === 'admin'; }) as $member)
                @if($member->id !== $project->owner_id)
                @php
                    $eventRolesArray = $member->pivot->event_roles ? json_decode($member->pivot->event_roles, true) : [];
                    $eventRole = !empty($eventRolesArray) ? $eventRolesArray[0] : null;
                    $allRoles = \App\Models\Ticket::getAllRoles();
                    
                    // Check if member has permanent role
                    $permanentRoleKeys = array_keys(\App\Models\Ticket::getAvailableRoles());
                    $hasPermanentRole = !empty($eventRole) && in_array($eventRole, $permanentRoleKeys);
                @endphp
                <div class="group relative overflow-hidden rounded-xl border-2 border-gray-200 bg-white p-4 hover:border-violet-300 hover:shadow-md transition-all duration-300"
                     x-data="{ editMode: false, projectRole: '{{ $member->pivot->role }}', eventRole: '{{ $eventRole ?? '' }}' }">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4 flex-1">
                            <div class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-600 to-indigo-600 text-white flex items-center justify-center font-semibold text-lg shadow">
                                {{ strtoupper(substr($member->name, 0, 1)) }}
                            </div>
                            <div class="flex-1">
                                <div class="font-semibold text-gray-900">{{ $member->name }}</div>
                                <div class="text-sm text-gray-500">{{ $member->email }}</div>
                                
                                {{-- Show event role if exists --}}
                                @if($eventRole)
                                <div class="mt-2">
                                    <span class="text-xs px-2 py-1 {{ $hasPermanentRole ? 'bg-blue-100 text-blue-700 border-blue-300' : 'bg-amber-100 text-amber-700 border-amber-300' }} rounded-full border">
                                        {{ $hasPermanentRole ? '🔒' : '⏱️' }} {{ $allRoles[$eventRole] ?? $eventRole }}
                                        @if($hasPermanentRole)
                                            <span class="text-[10px] opacity-75">(Permanent)</span>
                                        @endif
                                    </span>
                                </div>
                                @endif
                            </div>
                        </div>
                        
                        <div class="flex items-center gap-2">
                            {{-- Role Badge --}}
                            <span class="px-3 py-1.5 text-xs font-semibold rounded-full {{ $member->pivot->role === 'admin' ? 'bg-emerald-100 text-emerald-700 border border-emerald-300' : 'bg-gray-100 text-gray-700 border border-gray-300' }}">
                                {{ $member->pivot->role === 'admin' ? '⚡ Admin Project' : '👤 Member' }}
                            </span>
                            
                            @if($project->canManageMembers(Auth::user()))
                            {{-- Edit Button (Disabled if permanent role) (PM/HR/Admin Only) --}}
                            @if(!$hasPermanentRole)
                            <button @click="editMode = !editMode" 
                                    class="p-2 hover:bg-blue-50 rounded-lg transition"
                                    title="Edit Role">
                                <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </button>
                            @else
                            <div class="p-2 opacity-40 cursor-not-allowed" title="Role permanent tidak dapat diubah">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </div>
                            @endif

                            {{-- Remove Button (Disabled if permanent role) (PM/HR/Admin Only) --}}
                            @if(!$hasPermanentRole)
                            <form action="{{ route('projects.members.destroy', [$project, $member]) }}" 
                                  method="POST" 
                                  class="inline" 
                                  onsubmit="return confirm('Hapus {{ $member->name }} dari project ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="p-2 hover:bg-red-50 rounded-lg transition"
                                        title="Hapus dari Project">
                                    <svg class="h-5 w-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </form>
                            @else
                            <div class="p-2 opacity-40 cursor-not-allowed" title="Member dengan role permanent tidak dapat dihapus dari project">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </div>
                            @endif
                            @endif
                            {{-- End PM/HR/Admin Only Actions --}}
                        </div>
                    </div>
                    
                    @if($project->canManageMembers(Auth::user()))
                    {{-- Edit Role Form (PM/HR/Admin Only) --}}
                    <div x-show="editMode" 
                         x-transition
                         class="mt-4 pt-4 border-t-2 border-gray-100">
                        <form action="{{ route('projects.members.updateRole', [$project, $member]) }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                {{-- Project Role --}}
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 mb-2">Project Role</label>
                                    <select name="role" 
                                            x-model="projectRole"
                                            class="w-full text-sm border border-gray-300 p-2 rounded-lg focus:ring-2 focus:ring-violet-500 focus:border-violet-500">
                                        <option value="member">Member</option>
                                        <option value="admin">Admin</option>
                                    </select>
                                </div>
                                
                                {{-- Event Role (Single Select) --}}
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 mb-2">Event Role (Temporary)</label>
                                    <select name="event_role"
                                            x-model="eventRole"
                                            class="w-full text-sm border border-gray-300 p-2 rounded-lg focus:ring-2 focus:ring-violet-500 focus:border-violet-500">
                                        <option value="">-- Tidak Ada --</option>
                                        {{-- Only Event Roles --}}
                                        <optgroup label="Role Event">
                                            @foreach(\App\Models\Ticket::getEventRoles() as $key => $label)
                                                <option value="{{ $key }}">{{ $label }}</option>
                                            @endforeach
                                        </optgroup>
                                    </select>
                                    <p class="text-[10px] text-gray-500 mt-1">
                                        ℹ️ Role permanent tidak dapat diubah di project
                                    </p>
                                </div>
                            </div>
                            
                            <div class="flex items-center gap-2 mt-4">
                                <button type="submit" 
                                        class="px-4 py-2 text-sm bg-violet-600 text-white font-medium rounded-lg hover:bg-violet-700 transition">
                                    💾 Simpan
                                </button>
                                <button type="button" 
                                        @click="editMode = false"
                                        class="px-4 py-2 text-sm border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                                    Batal
                                </button>
                            </div>
                        </form>
                    </div>
                    @endif
                    {{-- End Edit Role Form (PM/HR/Admin Only) --}}
                </div>
                @endif
                @endforeach

                @if($project->members->where('id', '!==', $project->owner_id)->count() === 0)
                <div class="text-center py-12 px-6 bg-gradient-to-br from-gray-50 to-blue-50 rounded-xl border-2 border-dashed border-gray-300">
                    <svg class="h-16 w-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <p class="text-gray-600 font-medium mb-2">Belum ada member lain di project ini</p>
                    <p class="text-sm text-gray-500">Klik tombol "Tambah Member" untuk mengundang anggota tim</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <script>
    function memberManagement() {
        return {
            showAddMember: false
        }
    }
    </script>
    </div>
    </div>{{-- End Members Tab --}}
    
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
    
    {{-- Modal Detail Tiket - MOVED INSIDE x-data scope --}}
    <template x-if="showTicketModal && selectedTicket">
    <div @click.self="showTicketModal = false"
         class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4">
        <div @click.stop
         class="bg-white rounded-xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        
        {{-- Modal Header --}}
        <div class="sticky top-0 bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4 flex items-center justify-between border-b border-blue-700">
            <div class="flex items-center gap-3">
                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <h3 class="text-xl font-bold text-white">Detail Tiket</h3>
            </div>
            <button @click="showTicketModal = false" 
                    class="text-white hover:text-gray-200 transition">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        {{-- Modal Body --}}
        <div class="p-6 space-y-6">
            {{-- Title --}}
            <div>
                <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2 block">Judul Tiket</label>
                <h4 class="text-2xl font-bold text-gray-900" x-text="selectedTicket.title"></h4>
            </div>

            {{-- Status Badge --}}
            <div class="flex flex-wrap gap-2">
                <div>
                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</span>
                    <div class="mt-1">
                        <span x-show="selectedTicket.status === 'todo'" 
                              class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                            📋 To Do
                        </span>
                        <span x-show="selectedTicket.status === 'doing'" 
                              class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                            ⚡ Sedang Dikerjakan
                        </span>
                        <span x-show="selectedTicket.status === 'done'" 
                              class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                            ✅ Selesai
                        </span>
                        <span x-show="selectedTicket.status === 'blackout'" 
                              class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                            💡 Bank Ide
                        </span>
                    </div>
                </div>

                <div x-show="selectedTicket.target_role">
                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Target Role</span>
                    <div class="mt-1">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-purple-100 text-purple-800">
                            👥 <span x-text="selectedTicket.target_role"></span>
                        </span>
                    </div>
                </div>

                <div x-show="selectedTicket.context">
                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Konteks</span>
                    <div class="mt-1">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800"
                              x-text="selectedTicket.context === 'event' ? '📅 Event' : selectedTicket.context === 'proyek' ? '🏢 Proyek' : '🌐 Umum'">
                        </span>
                    </div>
                </div>
            </div>

            {{-- Event Title (if event context) --}}
            <div x-show="selectedTicket.event_title">
                <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2 block">Event</label>
                <div class="p-3 bg-indigo-50 rounded-lg border border-indigo-200">
                    <span class="text-sm font-medium text-indigo-800" x-text="selectedTicket.event_title"></span>
                </div>
            </div>

            {{-- Description --}}
            <div x-show="selectedTicket.description">
                <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2 block">Deskripsi</label>
                <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                    <p class="text-sm text-gray-700 whitespace-pre-wrap" x-text="selectedTicket.description"></p>
                </div>
            </div>

            {{-- Info Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Created By --}}
                <div class="p-4 bg-blue-50 rounded-lg border border-blue-200">
                    <div class="flex items-center gap-2 mb-2">
                        <svg class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <span class="text-xs font-semibold text-blue-600 uppercase tracking-wide">Dibuat Oleh</span>
                    </div>
                    <p class="text-sm font-medium text-blue-900" x-text="selectedTicket.created_by_name || 'N/A'"></p>
                    <p class="text-xs text-blue-600 mt-1" x-text="selectedTicket.created_at"></p>
                </div>

                {{-- Claimed By --}}
                <div class="p-4 bg-green-50 rounded-lg border border-green-200">
                    <div class="flex items-center gap-2 mb-2">
                        <svg class="h-5 w-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="text-xs font-semibold text-green-600 uppercase tracking-wide">Dikerjakan Oleh</span>
                    </div>
                    <p class="text-sm font-medium text-green-900" x-text="selectedTicket.claimed_by_name || 'Belum diambil'"></p>
                </div>

                {{-- Due Date --}}
                <div x-show="selectedTicket.due_date" class="p-4 bg-red-50 rounded-lg border border-red-200">
                    <div class="flex items-center gap-2 mb-2">
                        <svg class="h-5 w-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span class="text-xs font-semibold text-red-600 uppercase tracking-wide">Deadline</span>
                    </div>
                    <p class="text-sm font-medium text-red-900" x-text="selectedTicket.due_date"></p>
                </div>
            </div>
        </div>

        {{-- Modal Footer --}}
        <div class="sticky bottom-0 bg-gray-50 px-6 py-4 border-t border-gray-200 flex justify-between items-center">
            {{-- Set Todo Button (only for blackout status and claimed by current user) --}}
            <div>
                <template x-if="selectedTicket.status === 'blackout' && selectedTicket.claimed_by === {{ Auth::id() }}">
                    <form :action="`/tickets/${selectedTicket.id}/set-todo`" method="POST" class="inline">
                        @csrf
                        @method('PATCH')
                        <button type="submit" 
                                class="px-6 py-2 bg-gradient-to-r from-yellow-500 to-yellow-600 text-white rounded-lg hover:from-yellow-600 hover:to-yellow-700 transition font-medium shadow-md hover:shadow-lg">
                            🚀 Set Todo - Mulai Mengerjakan
                        </button>
                    </form>
                </template>
            </div>
            
            <button @click="showTicketModal = false" 
                    class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition font-medium">
                Tutup
            </button>
        </div>
    </div>
    </template>{{-- End Modal Detail Tiket --}}
    
    </div>{{-- End Tab Content --}}

</div>{{-- End Alpine x-data --}}

@endsection

{{-- Calendar rendered server-side with PHP - No JavaScript needed! --}}


