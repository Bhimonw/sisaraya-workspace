@extends('layouts.app')

@section('content')
<div class="relative" x-data="{ 
    showTicketModal: false,
    selectedTicket: null
}">
    {{-- Page Header --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
            <div class="flex-1">
                <div class="flex items-center gap-3 mb-2">
                    <svg class="h-8 w-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <h1 class="text-3xl font-bold text-gray-900">Semua Tiketku</h1>
                    <span class="text-sm px-3 py-1 bg-purple-100 text-purple-700 rounded-full font-medium">Riwayat Lengkap</span>
                </div>
                <p class="text-gray-600 text-base leading-relaxed">Riwayat lengkap semua tiket Anda - termasuk yang sudah selesai</p>
            </div>
            
            <a href="{{ route('tickets.mine') }}" 
               class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-150">
                <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                Tiket Aktif Saya
            </a>
        </div>
        
        {{-- Statistics --}}
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mt-6">
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg p-4 border border-blue-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-blue-600">Total Tiket</p>
                        <p class="text-2xl font-bold text-blue-900">{{ $tickets->count() }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-200 rounded-full flex items-center justify-center">
                        <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                </div>
            </div>
            
            <div class="bg-gradient-to-br from-amber-50 to-amber-100 rounded-lg p-4 border border-amber-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-amber-600">To Do</p>
                        <p class="text-2xl font-bold text-amber-900">{{ $tickets->where('status', 'todo')->count() }}</p>
                    </div>
                    <div class="w-12 h-12 bg-amber-200 rounded-full flex items-center justify-center">
                        <svg class="h-6 w-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>
            
            <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg p-4 border border-purple-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-purple-600">Doing</p>
                        <p class="text-2xl font-bold text-purple-900">{{ $tickets->where('status', 'doing')->count() }}</p>
                    </div>
                    <div class="w-12 h-12 bg-purple-200 rounded-full flex items-center justify-center">
                        <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                </div>
            </div>
            
            <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-lg p-4 border border-green-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-green-600">Done</p>
                        <p class="text-2xl font-bold text-green-900">{{ $tickets->where('status', 'done')->count() }}</p>
                    </div>
                    <div class="w-12 h-12 bg-green-200 rounded-full flex items-center justify-center">
                        <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                </div>
            </div>
            
            <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-lg p-4 border border-gray-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Blackout</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $tickets->where('status', 'blackout')->count() }}</p>
                    </div>
                    <div class="w-12 h-12 bg-gray-300 rounded-full flex items-center justify-center">
                        <svg class="h-6 w-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($tickets->isEmpty())
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
            <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <h3 class="mt-4 text-lg font-medium text-gray-900">Belum ada riwayat tiket</h3>
            <p class="mt-2 text-gray-500">Anda belum memiliki tiket apapun saat ini.</p>
        </div>
    @else
        {{-- Tabs untuk status --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden" x-data="{ activeStatus: 'all' }">
            {{-- Tab Header --}}
            <div class="border-b border-gray-200 bg-gray-50">
                <nav class="flex space-x-4 px-6 py-3" aria-label="Tabs">
                    <button @click="activeStatus = 'all'"
                            :class="activeStatus === 'all' ? 'border-purple-600 text-purple-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm transition-colors">
                        Semua
                        <span class="ml-2 px-2 py-0.5 rounded-full text-xs font-medium" 
                              :class="activeStatus === 'all' ? 'bg-purple-100 text-purple-700' : 'bg-gray-200 text-gray-600'">
                            {{ $tickets->count() }}
                        </span>
                    </button>
                    <button @click="activeStatus = 'todo'"
                            :class="activeStatus === 'todo' ? 'border-amber-600 text-amber-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm transition-colors">
                        To Do
                        <span class="ml-2 px-2 py-0.5 rounded-full text-xs font-medium"
                              :class="activeStatus === 'todo' ? 'bg-amber-100 text-amber-700' : 'bg-gray-200 text-gray-600'">
                            {{ $tickets->where('status', 'todo')->count() }}
                        </span>
                    </button>
                    <button @click="activeStatus = 'doing'"
                            :class="activeStatus === 'doing' ? 'border-purple-600 text-purple-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm transition-colors">
                        Doing
                        <span class="ml-2 px-2 py-0.5 rounded-full text-xs font-medium"
                              :class="activeStatus === 'doing' ? 'bg-purple-100 text-purple-700' : 'bg-gray-200 text-gray-600'">
                            {{ $tickets->where('status', 'doing')->count() }}
                        </span>
                    </button>
                    <button @click="activeStatus = 'done'"
                            :class="activeStatus === 'done' ? 'border-green-600 text-green-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm transition-colors">
                        Done
                        <span class="ml-2 px-2 py-0.5 rounded-full text-xs font-medium"
                              :class="activeStatus === 'done' ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-600'">
                            {{ $tickets->where('status', 'done')->count() }}
                        </span>
                    </button>
                    <button @click="activeStatus = 'blackout'"
                            :class="activeStatus === 'blackout' ? 'border-gray-700 text-gray-700' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm transition-colors">
                        Blackout
                        <span class="ml-2 px-2 py-0.5 rounded-full text-xs font-medium"
                              :class="activeStatus === 'blackout' ? 'bg-gray-700 text-white' : 'bg-gray-200 text-gray-600'">
                            {{ $tickets->where('status', 'blackout')->count() }}
                        </span>
                    </button>
                </nav>
            </div>

            {{-- Tab Content --}}
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Tiket
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Prioritas
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Context
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Proyek/Event
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Diambil/Selesai
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($tickets as $ticket)
                            <tr class="hover:bg-gray-50 transition-colors"
                                x-show="activeStatus === 'all' || activeStatus === '{{ $ticket->status }}'">
                                {{-- Tiket --}}
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $ticket->title }}</div>
                                    @if($ticket->description)
                                        <div class="text-sm text-gray-500 line-clamp-2 mt-1">{{ Str::limit($ticket->description, 100) }}</div>
                                    @endif
                                    
                                    {{-- Special Badges --}}
                                    <div class="flex flex-wrap gap-1 mt-2">
                                        @if($ticket->type === 'permintaan_dana')
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 text-xs font-medium rounded-full bg-yellow-100 text-yellow-700">
                                                💰 Permintaan Dana
                                            </span>
                                        @endif
                                        @if($ticket->type === 'broadcast')
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 text-xs font-medium rounded-full bg-blue-100 text-blue-700">
                                                📢 Broadcast
                                            </span>
                                        @endif
                                        @if($ticket->claimed_by === auth()->id())
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 text-xs font-medium rounded-full bg-green-100 text-green-700">
                                                <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                </svg>
                                                Saya ambil
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                
                                {{-- Prioritas --}}
                                <td class="px-6 py-4 whitespace-nowrap">
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
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $priorityBadgeClasses[$priorityColor] ?? 'bg-gray-100 text-gray-700' }}">
                                            {{ \App\Models\Ticket::getPriorityLabel($ticket->priority) }}
                                        </span>
                                    @else
                                        <span class="text-sm text-gray-400">—</span>
                                    @endif
                                </td>
                                
                                {{-- Context --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $contextColor = \App\Models\Ticket::getContextColor($ticket->context);
                                        $contextColorClasses = [
                                            'gray' => 'bg-gray-100 text-gray-700',
                                            'indigo' => 'bg-indigo-100 text-indigo-700',
                                            'blue' => 'bg-blue-100 text-blue-700',
                                        ];
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $contextColorClasses[$contextColor] ?? 'bg-gray-100 text-gray-700' }}">
                                        {{ \App\Models\Ticket::getContextLabel($ticket->context) }}
                                    </span>
                                </td>
                                
                                {{-- Proyek/Event --}}
                                <td class="px-6 py-4">
                                    @if($ticket->context === 'event' && $ticket->projectEvent)
                                        <div>
                                            <div class="text-sm font-medium text-indigo-700">{{ $ticket->projectEvent->title }}</div>
                                            @if($ticket->projectEvent->project)
                                                <a href="{{ route('projects.show', $ticket->projectEvent->project) }}" class="text-xs text-blue-600 hover:text-blue-800">
                                                    → {{ $ticket->projectEvent->project->name }}
                                                </a>
                                            @endif
                                        </div>
                                    @elseif($ticket->project)
                                        <a href="{{ route('projects.show', $ticket->project) }}" class="text-sm text-blue-600 hover:text-blue-800">
                                            {{ $ticket->project->name }}
                                        </a>
                                    @else
                                        <span class="text-sm text-gray-400">—</span>
                                    @endif
                                </td>
                                
                                {{-- Status --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($ticket->status === 'todo')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-600 text-white">
                                            To Do
                                        </span>
                                    @elseif($ticket->status === 'doing')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-600 text-white">
                                            Doing
                                        </span>
                                    @elseif($ticket->status === 'done')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-600 text-white">
                                            Done
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-700 text-white">
                                            Blackout
                                        </span>
                                    @endif
                                </td>
                                
                                {{-- Diambil/Selesai --}}
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    @if($ticket->isClaimed())
                                        <div class="flex items-center gap-1">
                                            <svg class="h-3 w-3 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <span class="text-green-700 font-medium">{{ $ticket->claimedBy->name }}</span>
                                        </div>
                                        @if($ticket->completed_at)
                                            <div class="text-xs text-gray-500 mt-0.5">
                                                ✅ {{ $ticket->completed_at->diffForHumans() }}
                                            </div>
                                        @elseif($ticket->claimed_at)
                                            <div class="text-xs text-gray-500 mt-0.5">
                                                📌 {{ $ticket->claimed_at->diffForHumans() }}
                                            </div>
                                        @endif
                                    @else
                                        <span class="text-xs text-orange-600 font-medium">Belum diambil</span>
                                    @endif
                                </td>
                                
                                {{-- Aksi --}}
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <div class="flex items-center gap-2">
                                        {{-- Detail Button --}}
                                        <button @click="selectedTicket = {{ \Illuminate\Support\Js::from($ticket) }}; showTicketModal = true" 
                                                class="text-purple-600 hover:text-purple-800 font-medium">
                                            Detail
                                        </button>
                                        
                                        {{-- Action Button Based on Status --}}
                                        @if($ticket->status !== 'done')
                                            @if(!$ticket->isClaimed() && $ticket->canBeClaimedBy(auth()->user()))
                                                {{-- Unclaimed: Show Ambil button --}}
                                                <form method="POST" action="{{ route('tickets.claim', $ticket) }}" class="inline-block">
                                                    @csrf
                                                    <button type="submit" class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors text-xs font-medium">
                                                        🎯 Ambil
                                                    </button>
                                                </form>
                                            @elseif($ticket->claimed_by === auth()->id())
                                                {{-- Claimed by current user --}}
                                                @if($ticket->status === 'todo')
                                                    <form method="POST" action="{{ route('tickets.start', $ticket) }}" class="inline-block">
                                                        @csrf
                                                        <button type="submit" class="px-3 py-1 bg-green-600 text-white rounded hover:bg-green-700 transition-colors text-xs font-medium">
                                                            ▶️ Mulai
                                                        </button>
                                                    </form>
                                                @elseif($ticket->status === 'doing')
                                                    <form method="POST" action="{{ route('tickets.complete', $ticket) }}" class="inline-block">
                                                        @csrf
                                                        <button type="submit" class="px-3 py-1 bg-green-600 text-white rounded hover:bg-green-700 transition-colors text-xs font-medium">
                                                            ✅ Selesai
                                                        </button>
                                                    </form>
                                                @endif
                                            @endif
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- Modal Detail Tiket - sama seperti di tickets.mine --}}
    <template x-if="showTicketModal && selectedTicket">
        <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4" 
             @click.self="showTicketModal = false">
            <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto"
                 @click.stop>
                {{-- Modal Header --}}
                <div class="bg-gradient-to-r from-purple-600 to-pink-600 px-6 py-4 flex items-center justify-between">
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
                                  'bg-gray-700 text-white': selectedTicket.status === 'blackout'
                              }"
                              x-text="selectedTicket.status === 'todo' ? 'To Do' : (selectedTicket.status === 'doing' ? 'Doing' : (selectedTicket.status === 'done' ? 'Done' : 'Blackout'))">
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

                        {{-- Claimed/Completed Info --}}
                        <div x-show="selectedTicket.claimed_by || selectedTicket.completed_at" class="bg-gray-50 rounded-lg p-3">
                            <div class="flex items-center gap-2 text-sm">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-purple-500 to-pink-600 text-white text-xs font-semibold flex items-center justify-center">
                                    <span x-text="selectedTicket.claimed_by_user?.name?.charAt(0).toUpperCase() || '?'"></span>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500" x-text="selectedTicket.completed_at ? 'Diselesaikan' : 'Diambil oleh'"></p>
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
                <div class="bg-gray-50 px-6 py-4 flex justify-end">
                    <button @click="showTicketModal = false" 
                            class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors font-medium">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </template>

</div>
@endsection
