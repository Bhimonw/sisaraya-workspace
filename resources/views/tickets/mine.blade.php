@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="{
        activeTab: 'all',
        showTicketModal: false,
        selectedTicket: null,
        showTicket(ticket) {
            this.selectedTicket = ticket;
            this.showTicketModal = true;
        },
        startTicket(ticketId) {
            if (confirm('Mulai mengerjakan tiket ini?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/tickets/${ticketId}/start`;
                form.innerHTML = '<input type=\'hidden\' name=\'_token\' value=\'{{ csrf_token() }}\'>';
                document.body.appendChild(form);
                form.submit();
            }
        },
        completeTicket(ticketId) {
            if (confirm('Tandai tiket ini sebagai selesai?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/tickets/${ticketId}/complete`;
                form.innerHTML = '<input type=\'hidden\' name=\'_token\' value=\'{{ csrf_token() }}\'>';
                document.body.appendChild(form);
                form.submit();
            }
        },
        unclaimTicket(ticketId) {
            if (confirm('Lepas tiket ini?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/tickets/${ticketId}/unclaim`;
                form.innerHTML = '<input type=\'hidden\' name=\'_token\' value=\'{{ csrf_token() }}\'>';
                document.body.appendChild(form);
                form.submit();
            }
        },
        claimTicket(ticketId) {
            if (confirm('Ambil tiket ini?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/tickets/${ticketId}/claim`;
                form.innerHTML = '<input type=\'hidden\' name=\'_token\' value=\'{{ csrf_token() }}\'>';
                document.body.appendChild(form);
                form.submit();
            }
        }
    }">
        {{-- Header --}}
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-gray-900 mb-2">Tiketku</h1>
            <p class="text-lg text-gray-600">Kelola tiket yang sedang aktif</p>
        </div>

        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-800 rounded-r-lg shadow-sm">
                <div class="flex items-center">
                    <svg class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-800 rounded-r-lg shadow-sm">
                <div class="flex items-center">
                    <svg class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    <span class="font-medium">{{ session('error') }}</span>
                </div>
            </div>
        @endif

        {{-- Main Layout: Content + Sidebar --}}
        <div class="flex flex-col lg:flex-row gap-6">
            {{-- Left Content Area --}}
            <div class="flex-1 min-w-0">
                {{-- Statistics Cards --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    {{-- Total Tiket --}}
                    <div class="bg-white rounded-xl shadow-md border border-gray-200 p-6 hover:shadow-lg transition-shadow">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-2">Total Tiketku</p>
                                <p class="text-5xl font-bold text-blue-600">{{ $myTickets->count() }}</p>
                            </div>
                            <div class="bg-blue-100 rounded-full p-4">
                                <svg class="h-10 w-10 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                    {{-- To Do --}}
                    <div class="bg-white rounded-xl shadow-md border border-gray-200 p-6 hover:shadow-lg transition-shadow">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-2">To Do</p>
                                <p class="text-5xl font-bold text-amber-600">{{ $myTickets->where('status', 'todo')->count() }}</p>
                            </div>
                            <div class="bg-amber-100 rounded-full p-4">
                                <svg class="h-10 w-10 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                    {{-- Doing --}}
                    <div class="bg-white rounded-xl shadow-md border border-gray-200 p-6 hover:shadow-lg transition-shadow">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-2">Doing</p>
                                <p class="text-5xl font-bold text-purple-600">{{ $myTickets->where('status', 'doing')->count() }}</p>
                            </div>
                            <div class="bg-purple-100 rounded-full p-4">
                                <svg class="h-10 w-10 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Tiket Saya - 3 Kolom: Blackout | To Do | Doing --}}
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Tiket Saya
                    </h2>
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- BLACKOUT COLUMN --}}
            <div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden">
                {{-- Header --}}
                <div class="bg-gradient-to-r from-gray-600 to-gray-700 px-5 py-4 border-b-4 border-gray-800">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="bg-gray-800 bg-opacity-30 rounded-lg p-2 border border-gray-900 border-opacity-30">
                                <svg class="h-6 w-6 text-white drop-shadow-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                </svg>
                            </div>
                            <h2 class="text-xl font-bold text-white drop-shadow-sm">Blackout</h2>
                        </div>
                        <div class="bg-white px-4 py-1.5 rounded-full shadow-sm">
                            <span class="text-lg font-bold text-gray-600">{{ $myTickets->where('status', 'blackout')->count() }}</span>
                        </div>
                    </div>
                </div>

                {{-- Blackout Tickets List --}}
                <div class="bg-gray-50 p-4 space-y-3 max-h-[600px] overflow-y-auto">
                    @forelse($myTickets->where('status', 'blackout') as $ticket)
                        <div class="bg-white rounded-lg shadow-sm border border-gray-300 p-4 hover:shadow-md hover:border-gray-400 transition-all opacity-60">
                            <div class="mb-3 cursor-pointer" @click="showTicket({
                                     id: {{ $ticket->id }},
                                     title: '{{ addslashes($ticket->title) }}',
                                     description: '{{ addslashes($ticket->description ?? '') }}',
                                     status: '{{ $ticket->status }}',
                                     priority: '{{ $ticket->priority ?? '' }}',
                                     context: '{{ $ticket->context }}',
                                     due_date: '{{ $ticket->due_date ? $ticket->due_date->format('d M Y') : '' }}',
                                     created_at: '{{ $ticket->created_at->format('d M Y') }}',
                                     claimed_by: {{ $ticket->claimed_by ?? 'null' }},
                                     claimed_by_user: {{ $ticket->claimedBy ? json_encode(['name' => $ticket->claimedBy->name]) : 'null' }},
                                     creator: {{ $ticket->creator ? json_encode(['name' => $ticket->creator->name]) : 'null' }}
                                 })">
                                <h3 class="text-base font-bold text-gray-700 mb-3 leading-tight line-through">{{ $ticket->title }}</h3>
                                
                                <div class="flex flex-wrap gap-2 mb-3">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-bold rounded-md bg-gray-600 text-white">
                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                        Bank Ide
                                    </span>

                                    @if($ticket->context)
                                        <span class="inline-flex items-center px-2.5 py-1 text-xs font-bold rounded-md
                                            {{ $ticket->context === 'umum' ? 'bg-gray-200 text-gray-800' : '' }}
                                            {{ $ticket->context === 'event' ? 'bg-indigo-200 text-indigo-800' : '' }}
                                            {{ $ticket->context === 'proyek' ? 'bg-blue-200 text-blue-800' : '' }}">
                                            @if($ticket->context === 'umum') 📝 Umum
                                            @elseif($ticket->context === 'event') 📅 Event
                                            @else 📁 Proyek
                                            @endif
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12">
                            <div class="bg-gray-200 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-3">
                                <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                </svg>
                            </div>
                            <p class="text-sm font-semibold text-gray-500">Tidak ada tiket di bank ide</p>
                        </div>
                    @endforelse
                </div>
            </div>
            
            {{-- TO DO COLUMN --}}
            <div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden">
                {{-- Header --}}
                <div class="bg-gradient-to-r from-amber-500 to-orange-500 px-5 py-4 border-b-4 border-amber-600">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="bg-amber-600 bg-opacity-30 rounded-lg p-2 border border-amber-700 border-opacity-30">
                                <svg class="h-6 w-6 text-white drop-shadow-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <h2 class="text-xl font-bold text-white drop-shadow-sm">To Do</h2>
                        </div>
                        <div class="bg-white px-4 py-1.5 rounded-full shadow-sm">
                            <span class="text-lg font-bold text-amber-600">{{ $myTickets->where('status', 'todo')->count() }}</span>
                        </div>
                    </div>
                </div>

                {{-- To Do Tickets List --}}
                <div class="bg-gray-50 p-4 space-y-3 max-h-[600px] overflow-y-auto">
                    @forelse($myTickets->where('status', 'todo') as $ticket)
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 hover:shadow-md hover:border-amber-300 transition-all">
                            <div class="mb-3 cursor-pointer" @click="showTicket({
                                     id: {{ $ticket->id }},
                                     title: '{{ addslashes($ticket->title) }}',
                                     description: '{{ addslashes($ticket->description ?? '') }}',
                                     status: '{{ $ticket->status }}',
                                     priority: '{{ $ticket->priority ?? '' }}',
                                     context: '{{ $ticket->context }}',
                                     due_date: '{{ $ticket->due_date ? $ticket->due_date->format('d M Y') : '' }}',
                                     created_at: '{{ $ticket->created_at->format('d M Y') }}',
                                     claimed_by: {{ $ticket->claimed_by ?? 'null' }},
                                     claimed_by_user: {{ $ticket->claimedBy ? json_encode(['name' => $ticket->claimedBy->name]) : 'null' }},
                                     creator: {{ $ticket->creator ? json_encode(['name' => $ticket->creator->name]) : 'null' }}
                                 })">
                                <h3 class="text-base font-bold text-gray-900 mb-3 leading-tight">{{ $ticket->title }}</h3>
                                
                                <div class="flex flex-wrap gap-2 mb-3">
                                    @if($ticket->priority)
                                        <span class="inline-flex items-center px-2.5 py-1 text-xs font-bold rounded-md
                                            {{ $ticket->priority === 'low' ? 'bg-gray-200 text-gray-800' : '' }}
                                            {{ $ticket->priority === 'medium' ? 'bg-blue-200 text-blue-800' : '' }}
                                            {{ $ticket->priority === 'high' ? 'bg-orange-200 text-orange-800' : '' }}
                                            {{ $ticket->priority === 'urgent' ? 'bg-red-200 text-red-800' : '' }}">
                                            @if($ticket->priority === 'low') Rendah
                                            @elseif($ticket->priority === 'medium') Sedang
                                            @elseif($ticket->priority === 'high') Tinggi
                                            @else Mendesak
                                            @endif
                                        </span>
                                    @endif

                                    <span class="inline-flex items-center px-2.5 py-1 text-xs font-bold rounded-md
                                        {{ $ticket->context === 'umum' ? 'bg-gray-200 text-gray-800' : '' }}
                                        {{ $ticket->context === 'event' ? 'bg-indigo-200 text-indigo-800' : '' }}
                                        {{ $ticket->context === 'proyek' ? 'bg-blue-200 text-blue-800' : '' }}">
                                        @if($ticket->context === 'umum') 📝 Umum
                                        @elseif($ticket->context === 'event') 📅 Event
                                        @else 📁 Proyek
                                        @endif
                                    </span>

                                    @if($ticket->due_date)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-bold rounded-md
                                            {{ $ticket->due_date->isPast() ? 'bg-red-200 text-red-800' : 'bg-green-200 text-green-800' }}">
                                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            {{ $ticket->due_date->format('d M') }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="flex gap-2" @click.stop>
                                <button @click="startTicket({{ $ticket->id }})" 
                                        class="flex-1 px-3 py-2 bg-blue-600 text-white text-sm font-bold rounded-lg hover:bg-blue-700 transition-colors shadow-sm">
                                    ▶️ Mulai
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12">
                            <div class="bg-gray-200 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-3">
                                <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                            </div>
                            <p class="text-sm font-semibold text-gray-500">Tidak ada tiket</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- DOING COLUMN --}}
            <div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden">
                {{-- Header --}}
                <div class="bg-gradient-to-r from-purple-500 to-pink-500 px-5 py-4 border-b-4 border-purple-600">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="bg-purple-600 bg-opacity-30 rounded-lg p-2 border border-purple-700 border-opacity-30">
                                <svg class="h-6 w-6 text-white drop-shadow-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                            </div>
                            <h2 class="text-xl font-bold text-white drop-shadow-sm">Doing</h2>
                        </div>
                        <div class="bg-white px-4 py-1.5 rounded-full shadow-sm">
                            <span class="text-lg font-bold text-purple-600">{{ $myTickets->where('status', 'doing')->count() }}</span>
                        </div>
                    </div>
                </div>

                {{-- Doing Tickets List --}}
                <div class="bg-gray-50 p-4 space-y-3 max-h-[600px] overflow-y-auto">
                    @forelse($myTickets->where('status', 'doing') as $ticket)
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 hover:shadow-md hover:border-purple-300 transition-all">
                            <div class="mb-3 cursor-pointer" @click="showTicket({
                                     id: {{ $ticket->id }},
                                     title: '{{ addslashes($ticket->title) }}',
                                     description: '{{ addslashes($ticket->description ?? '') }}',
                                     status: '{{ $ticket->status }}',
                                     priority: '{{ $ticket->priority ?? '' }}',
                                     context: '{{ $ticket->context }}',
                                     due_date: '{{ $ticket->due_date ? $ticket->due_date->format('d M Y') : '' }}',
                                     created_at: '{{ $ticket->created_at->format('d M Y') }}',
                                     claimed_by: {{ $ticket->claimed_by ?? 'null' }},
                                     claimed_by_user: {{ $ticket->claimedBy ? json_encode(['name' => $ticket->claimedBy->name]) : 'null' }},
                                     creator: {{ $ticket->creator ? json_encode(['name' => $ticket->creator->name]) : 'null' }}
                                 })">
                                <h3 class="text-base font-bold text-gray-900 mb-3 leading-tight">{{ $ticket->title }}</h3>
                                
                                <div class="flex flex-wrap gap-2 mb-3">
                                    @if($ticket->priority)
                                        <span class="inline-flex items-center px-2.5 py-1 text-xs font-bold rounded-md
                                            {{ $ticket->priority === 'low' ? 'bg-gray-200 text-gray-800' : '' }}
                                            {{ $ticket->priority === 'medium' ? 'bg-blue-200 text-blue-800' : '' }}
                                            {{ $ticket->priority === 'high' ? 'bg-orange-200 text-orange-800' : '' }}
                                            {{ $ticket->priority === 'urgent' ? 'bg-red-200 text-red-800' : '' }}">
                                            @if($ticket->priority === 'low') Rendah
                                            @elseif($ticket->priority === 'medium') Sedang
                                            @elseif($ticket->priority === 'high') Tinggi
                                            @else Mendesak
                                            @endif
                                        </span>
                                    @endif

                                    <span class="inline-flex items-center px-2.5 py-1 text-xs font-bold rounded-md
                                        {{ $ticket->context === 'umum' ? 'bg-gray-200 text-gray-800' : '' }}
                                        {{ $ticket->context === 'event' ? 'bg-indigo-200 text-indigo-800' : '' }}
                                        {{ $ticket->context === 'proyek' ? 'bg-blue-200 text-blue-800' : '' }}">
                                        @if($ticket->context === 'umum') 📝 Umum
                                        @elseif($ticket->context === 'event') 📅 Event
                                        @else 📁 Proyek
                                        @endif
                                    </span>

                                    @if($ticket->due_date)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-bold rounded-md
                                            {{ $ticket->due_date->isPast() ? 'bg-red-200 text-red-800' : 'bg-green-200 text-green-800' }}">
                                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            {{ $ticket->due_date->format('d M') }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="flex gap-2" @click.stop>
                                <button @click="completeTicket({{ $ticket->id }})" 
                                        class="flex-1 px-3 py-2 bg-green-600 text-white text-sm font-bold rounded-lg hover:bg-green-700 transition-colors shadow-sm">
                                    ✓ Selesai
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12">
                            <div class="bg-gray-200 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-3">
                                <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                            </div>
                            <p class="text-sm font-semibold text-gray-500">Tidak ada tiket</p>
                        </div>
                    @endforelse
                </div>
            </div>

            </div>{{-- End 3-column grid --}}
                </div>{{-- End Tiket Saya --}}
            <div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden">
                {{-- Header --}}
                <div class="bg-gradient-to-r from-gray-600 to-gray-700 px-5 py-4 border-b-4 border-gray-800">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="bg-gray-800 bg-opacity-30 rounded-lg p-2 border border-gray-900 border-opacity-30">
                                <svg class="h-6 w-6 text-white drop-shadow-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                </svg>
                            </div>
                            <h2 class="text-xl font-bold text-white drop-shadow-sm">Blackout</h2>
                        </div>
                        <div class="bg-white px-4 py-1.5 rounded-full shadow-sm">
                            <span class="text-lg font-bold text-gray-600">{{ $myTickets->where('status', 'blackout')->count() }}</span>
                        </div>
                    </div>
                </div>

                {{-- Blackout Tickets List --}}
                <div class="bg-gray-50 p-4 space-y-3 max-h-[600px] overflow-y-auto">
                    @forelse($myTickets->where('status', 'blackout') as $ticket)
                        <div class="bg-white rounded-lg shadow-sm border border-gray-300 p-4 hover:shadow-md hover:border-gray-400 transition-all opacity-60">
                            <div class="mb-3 cursor-pointer" @click="showTicket({
                                     id: {{ $ticket->id }},
                                     title: '{{ addslashes($ticket->title) }}',
                                     description: '{{ addslashes($ticket->description ?? '') }}',
                                     status: '{{ $ticket->status }}',
                                     priority: '{{ $ticket->priority ?? '' }}',
                                     context: '{{ $ticket->context }}',
                                     due_date: '{{ $ticket->due_date ? $ticket->due_date->format('d M Y') : '' }}',
                                     created_at: '{{ $ticket->created_at->format('d M Y') }}',
                                     claimed_by: {{ $ticket->claimed_by ?? 'null' }},
                                     claimed_by_user: {{ $ticket->claimedBy ? json_encode(['name' => $ticket->claimedBy->name]) : 'null' }},
                                     creator: {{ $ticket->creator ? json_encode(['name' => $ticket->creator->name]) : 'null' }}
                                 })">
                                <h3 class="text-base font-bold text-gray-700 mb-3 leading-tight line-through">{{ $ticket->title }}</h3>
                                
                                <div class="flex flex-wrap gap-2 mb-3">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-bold rounded-md bg-gray-600 text-white">
                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                        Dibatalkan
                                    </span>

                                    @if($ticket->context)
                                        <span class="inline-flex items-center px-2.5 py-1 text-xs font-bold rounded-md
                                            {{ $ticket->context === 'umum' ? 'bg-gray-200 text-gray-800' : '' }}
                                            {{ $ticket->context === 'event' ? 'bg-indigo-200 text-indigo-800' : '' }}
                                            {{ $ticket->context === 'proyek' ? 'bg-blue-200 text-blue-800' : '' }}">
                                            @if($ticket->context === 'umum') 📝 Umum
                                            @elseif($ticket->context === 'event') 📅 Event
                                            @else 📁 Proyek
                                            @endif
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12">
                            <div class="bg-gray-200 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-3">
                                <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                </svg>
                            </div>
                            <p class="text-sm font-semibold text-gray-500">Tidak ada tiket di bank ide</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Right Sidebar: Tiket Tersedia --}}
            <div class="w-full lg:w-96 flex-shrink-0">
                <div class="lg:sticky lg:top-4">
                    <div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden">
                        {{-- Header --}}
                        <div class="bg-gradient-to-r from-green-500 to-teal-500 px-5 py-4">
                            <div class="flex items-center gap-3">
                                <div class="bg-green-600 bg-opacity-30 rounded-lg p-2 border border-green-700 border-opacity-30">
                                    <svg class="h-6 w-6 text-white drop-shadow-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-lg font-bold text-white drop-shadow-sm">Tiket Tersedia</h3>
                                    <p class="text-xs text-green-100">{{ $availableTickets->count() }} tiket bisa diambil</p>
                                </div>
                            </div>
                        </div>

                        {{-- Available Tickets List (Scrollable) --}}
                        <div class="bg-gray-50 p-4 space-y-3 h-[680px] overflow-y-auto">
                            @if($availableTickets->isEmpty())
                                <div class="text-center py-12">
                                    <div class="bg-gray-200 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-3">
                                        <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                    <p class="text-sm font-semibold text-gray-500">Tidak ada tiket tersedia</p>
                                </div>
                            @else
                                @foreach($availableTickets as $ticket)
                                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 hover:shadow-md hover:border-green-300 transition-all">
                                        <div class="mb-3 cursor-pointer" @click="showTicket({
                                                 id: {{ $ticket->id }},
                                                 title: '{{ addslashes($ticket->title) }}',
                                                 description: '{{ addslashes($ticket->description ?? '') }}',
                                                 status: 'tersedia',
                                                 priority: '{{ $ticket->priority ?? '' }}',
                                                 context: '{{ $ticket->context }}',
                                                 due_date: '{{ $ticket->due_date ? $ticket->due_date->format('d M Y') : '' }}',
                                                 created_at: '{{ $ticket->created_at->format('d M Y') }}',
                                                 claimed_by: null,
                                                 claimed_by_user: null,
                                                 creator: {{ $ticket->creator ? json_encode(['name' => $ticket->creator->name]) : 'null' }}
                                             })">
                                            <h3 class="text-sm font-bold text-gray-900 mb-2 leading-tight">{{ $ticket->title }}</h3>
                                            
                                            <div class="flex flex-wrap gap-2 mb-2">
                                                @if($ticket->priority)
                                                    <span class="inline-flex items-center px-2 py-0.5 text-xs font-bold rounded-md
                                                        {{ $ticket->priority === 'low' ? 'bg-gray-200 text-gray-800' : '' }}
                                                        {{ $ticket->priority === 'medium' ? 'bg-blue-200 text-blue-800' : '' }}
                                                        {{ $ticket->priority === 'high' ? 'bg-orange-200 text-orange-800' : '' }}
                                                        {{ $ticket->priority === 'urgent' ? 'bg-red-200 text-red-800' : '' }}">
                                                        @if($ticket->priority === 'low') Rendah
                                                        @elseif($ticket->priority === 'medium') Sedang
                                                        @elseif($ticket->priority === 'high') Tinggi
                                                        @else Mendesak
                                                        @endif
                                                    </span>
                                                @endif

                                                <span class="inline-flex items-center px-2 py-0.5 text-xs font-bold rounded-md
                                                    {{ $ticket->context === 'umum' ? 'bg-gray-200 text-gray-800' : '' }}
                                                    {{ $ticket->context === 'event' ? 'bg-indigo-200 text-indigo-800' : '' }}
                                                    {{ $ticket->context === 'proyek' ? 'bg-blue-200 text-blue-800' : '' }}">
                                                    @if($ticket->context === 'umum') 📝 Umum
                                                    @elseif($ticket->context === 'event') 📅 Event
                                                    @else 📁 Proyek
                                                    @endif
                                                </span>

                                                @if($ticket->due_date)
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 text-xs font-bold rounded-md
                                                        {{ $ticket->due_date->isPast() ? 'bg-red-200 text-red-800' : 'bg-green-200 text-green-800' }}">
                                                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                        </svg>
                                                        {{ $ticket->due_date->format('d M') }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>

                                        <button @click.stop="claimTicket({{ $ticket->id }})" 
                                                class="w-full px-3 py-2 bg-green-600 text-white text-xs font-bold rounded-lg hover:bg-green-700 transition-colors shadow-sm flex items-center justify-center gap-2">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                            </svg>
                                            Ambil Tiket
                                        </button>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>{{-- End Main Layout --}}

        {{-- Modal Detail Tiket --}}
        <template x-if="showTicketModal && selectedTicket">
        <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4" 
             @click.self="showTicketModal = false">
            <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto"
                 @click.stop>
                {{-- Modal Header --}}
                <div class="bg-gradient-to-r from-blue-600 to-cyan-600 px-6 py-4 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-white">Detail Tiket</h3>
                    <button @click="showTicketModal = false" 
                            class="text-white hover:text-gray-200 transition-colors">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- Modal Body --}}
                <div class="p-6 space-y-4">
                    {{-- Title --}}
                    <div>
                        <h4 class="text-2xl font-bold text-gray-900" x-text="selectedTicket.title"></h4>
                    </div>

                    {{-- Badges Row --}}
                    <div class="flex flex-wrap items-center gap-2">
                        {{-- Status Badge --}}
                        <span class="inline-flex items-center px-3 py-1 text-sm font-medium rounded-full"
                              :class="{
                                  'bg-amber-600 text-white': selectedTicket.status === 'todo',
                                  'bg-purple-600 text-white': selectedTicket.status === 'doing',
                                  'bg-green-600 text-white': selectedTicket.status === 'done',
                                  'bg-gray-700 text-white': selectedTicket.status === 'blackout',
                                  'bg-teal-600 text-white': selectedTicket.status === 'tersedia'
                              }"
                              x-text="selectedTicket.status === 'todo' ? 'To Do' : (selectedTicket.status === 'doing' ? 'Doing' : (selectedTicket.status === 'done' ? 'Done' : (selectedTicket.status === 'blackout' ? 'Blackout' : 'Tersedia')))">
                        </span>

                        {{-- Priority Badge --}}
                        <span x-show="selectedTicket.priority" 
                              class="inline-flex items-center px-3 py-1 text-sm font-medium rounded-full"
                              :class="{
                                  'bg-gray-100 text-gray-700': selectedTicket.priority === 'low',
                                  'bg-blue-100 text-blue-700': selectedTicket.priority === 'medium',
                                  'bg-orange-100 text-orange-700': selectedTicket.priority === 'high',
                                  'bg-red-100 text-red-700': selectedTicket.priority === 'urgent'
                              }">
                            <span x-text="selectedTicket.priority === 'low' ? 'Rendah' : (selectedTicket.priority === 'medium' ? 'Sedang' : (selectedTicket.priority === 'high' ? 'Tinggi' : 'Mendesak'))"></span>
                        </span>

                        {{-- Context Badge --}}
                        <span class="inline-flex items-center gap-1 px-3 py-1 text-sm font-medium rounded-full"
                              :class="{
                                  'bg-gray-100 text-gray-700': selectedTicket.context === 'umum',
                                  'bg-indigo-100 text-indigo-700': selectedTicket.context === 'event',
                                  'bg-blue-100 text-blue-700': selectedTicket.context === 'proyek'
                              }">
                            <span x-text="selectedTicket.context === 'umum' ? 'Umum' : (selectedTicket.context === 'event' ? 'Event' : 'Proyek')"></span>
                        </span>
                    </div>

                    {{-- Description --}}
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h5 class="text-sm font-semibold text-gray-700 mb-2">Deskripsi</h5>
                        <p class="text-gray-600 text-sm leading-relaxed whitespace-pre-wrap" 
                           x-text="selectedTicket.description || 'Tidak ada deskripsi'"></p>
                    </div>

                    {{-- Metadata Grid --}}
                    <div class="grid grid-cols-2 gap-4">
                        {{-- Due Date --}}
                        <div x-show="selectedTicket.due_date" class="bg-gray-50 rounded-lg p-3">
                            <div class="flex items-center gap-2 text-sm">
                                <svg class="h-5 w-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <div>
                                    <p class="text-xs text-gray-500">Deadline</p>
                                    <p class="font-semibold text-gray-900" x-text="selectedTicket.due_date"></p>
                                </div>
                            </div>
                        </div>

                        {{-- Claimed By --}}
                        <div x-show="selectedTicket.claimed_by" class="bg-gray-50 rounded-lg p-3">
                            <div class="flex items-center gap-2 text-sm">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-500 to-cyan-600 text-white text-xs font-semibold flex items-center justify-center">
                                    <span x-text="selectedTicket.claimed_by_user?.name?.charAt(0).toUpperCase() || '?'"></span>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Diambil oleh</p>
                                    <p class="font-semibold text-gray-900" x-text="selectedTicket.claimed_by_user?.name || 'Saya'"></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Creator --}}
                    <div class="border-t border-gray-200 pt-4">
                        <p class="text-xs text-gray-500">Dibuat oleh</p>
                        <div class="flex items-center gap-2 mt-1">
                            <div class="w-6 h-6 rounded-full bg-gradient-to-br from-gray-400 to-gray-600 text-white text-xs font-semibold flex items-center justify-center">
                                <span x-text="selectedTicket.creator?.name?.charAt(0).toUpperCase() || '?'"></span>
                            </div>
                            <span class="text-sm font-medium text-gray-900" x-text="selectedTicket.creator?.name || 'Unknown'"></span>
                            <span class="text-xs text-gray-500" x-text="'• ' + (selectedTicket.created_at || '')"></span>
                        </div>
                    </div>
                </div>

                {{-- Modal Footer --}}
                <div class="bg-gray-50 px-6 py-4 flex justify-between items-center">
                    {{-- Set Todo Button (only for blackout status and claimed by current user) --}}
                    <div>
                        <template x-if="selectedTicket && selectedTicket.status === 'blackout' && selectedTicket.claimed_by === {{ Auth::id() }}">
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
                            class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors font-medium">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </template>

    </div> {{-- End of x-data container --}}
</div>
@endsection
