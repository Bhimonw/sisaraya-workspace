@extends('layouts.app')

@push('styles')
<style>
    /* Modern Range Slider Styling */
    .slider-modern::-webkit-slider-thumb {
        appearance: none;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        cursor: pointer;
        box-shadow: 0 2px 8px rgba(102, 126, 234, 0.4);
        transition: all 0.2s ease;
    }
    
    .slider-modern::-webkit-slider-thumb:hover {
        transform: scale(1.2);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.6);
    }
    
    .slider-modern::-moz-range-thumb {
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        cursor: pointer;
        border: none;
        box-shadow: 0 2px 8px rgba(102, 126, 234, 0.4);
        transition: all 0.2s ease;
    }
    
    .slider-modern::-moz-range-thumb:hover {
        transform: scale(1.2);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.6);
    }
    
    .slider-modern::-webkit-slider-runnable-track {
        background: linear-gradient(to right, #10b981 0%, #fbbf24 50%, #ef4444 100%);
        border-radius: 999px;
        height: 6px;
    }
    
    .slider-modern::-moz-range-track {
        background: linear-gradient(to right, #10b981 0%, #fbbf24 50%, #ef4444 100%);
        border-radius: 999px;
        height: 6px;
    }
</style>
@endpush

@section('content')
<div class="relative" x-data="{ 
    showTicketModal: false,
    selectedTicket: null,
    showCreateModal: false,
    targetType: 'all'
}">
    {{-- Page Header --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
            <div class="flex-1">
                <div class="flex items-center gap-3 mb-2">
                    <svg class="h-8 w-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <h1 class="text-3xl font-bold text-gray-900">Manajemen Tiket</h1>
                </div>
                <p class="text-gray-600 text-base leading-relaxed">Kelola semua tiket dari berbagai proyek dan tiket umum</p>
            </div>
            
            <div class="flex items-center gap-3">
                <button @click="showCreateModal = true" 
                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-150">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Buat Tiket Umum
                </button>
            </div>
        </div>
        
        {{-- Statistics Component --}}
        @include('components.tickets.statistics', [
            'totalTickets' => $allTickets->count(),
            'unclaimedTickets' => $allTickets->where('status', 'todo')->count(),
            'activeTickets' => $allTickets->where('status', 'doing')->count(),
            'completedTickets' => $allTickets->where('status', 'done')->count(),
            'blackoutTickets' => $allTickets->where('status', 'blackout')->count()
        ])
    </div>

    {{-- Table View - Semua Tiket --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Tiket
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Tag
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Prioritas
                        </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Bobot
                            </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Context
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Proyek/Event
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Target Role
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Diambil Oleh
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($allTickets as $ticket)
                        <tr class="hover:bg-gray-50 transition-colors">
                            {{-- Tiket --}}
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $ticket->title }}</div>
                                @if($ticket->description)
                                    <div class="text-xs text-gray-500 mt-1 line-clamp-1">{{ Str::limit($ticket->description, 60) }}</div>
                                @endif
                            </td>
                            
                            {{-- Tag --}}
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-1.5">
                                    {{-- Status Badge --}}
                                    @if($ticket->status === 'todo')
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-xs font-bold bg-gradient-to-r from-amber-400 to-orange-500 text-white shadow-sm">
                                            <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            To Do
                                        </span>
                                    @elseif($ticket->status === 'doing')
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-xs font-bold bg-gradient-to-r from-purple-500 to-indigo-600 text-white shadow-sm">
                                            <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                            </svg>
                                            Doing
                                        </span>
                                    @elseif($ticket->status === 'done')
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-xs font-bold bg-gradient-to-r from-green-500 to-teal-600 text-white shadow-sm">
                                            <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                            Done
                                        </span>
                                    @elseif($ticket->status === 'blackout')
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-xs font-bold bg-gradient-to-r from-gray-700 to-gray-900 text-white shadow-sm">
                                            <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                            </svg>
                                            Blackout
                                        </span>
                                    @endif

                                    {{-- Context Badge --}}
                                    @if($ticket->context)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-xs font-bold
                                            {{ $ticket->context === 'umum' ? 'bg-gray-200 text-gray-800' : '' }}
                                            {{ $ticket->context === 'event' ? 'bg-indigo-200 text-indigo-800' : '' }}
                                            {{ $ticket->context === 'proyek' ? 'bg-blue-200 text-blue-800' : '' }}">
                                            @if($ticket->context === 'umum')
                                                <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                                                </svg>
                                                Umum
                                            @elseif($ticket->context === 'event')
                                                <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                </svg>
                                                Event
                                            @else
                                                <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                                                </svg>
                                                Proyek
                                            @endif
                                        </span>
                                    @endif

                                    {{-- Due Date Badge (if exists and within 7 days or overdue) --}}
                                    @if($ticket->due_date && ($ticket->due_date->isPast() || $ticket->due_date->diffInDays(now()) <= 7))
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-xs font-bold
                                            {{ $ticket->due_date->isPast() ? 'bg-red-200 text-red-800' : 'bg-yellow-200 text-yellow-800' }}">
                                            <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            {{ $ticket->due_date->format('d M') }}
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
                                {{-- Bobot --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($ticket->weight)
                                        @php
                                            $weightLabel = \App\Models\Ticket::getWeightLabel($ticket->weight);
                                            $weightColor = $ticket->weight <= 3 ? 'bg-green-100 text-green-700' : ($ticket->weight <= 6 ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700');
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $weightColor }}">
                                            {{ $ticket->weight }} - {{ $weightLabel }}
                                        </span>
                                    @else
                                        <span class="text-sm text-gray-400">—</span>
                                    @endif
                                </td>
                            
                            {{-- Context --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($ticket->project_id)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700">
                                        Proyek
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                                        Umum
                                    </span>
                                @endif
                            </td>
                            
                            {{-- Proyek/Event --}}
                            <td class="px-6 py-4">
                                @if($ticket->project_id)
                                    <div class="text-sm text-blue-600 hover:text-blue-800">
                                        {{ $ticket->project->name ?? 'Unknown Project' }}
                                    </div>
                                @else
                                    <span class="text-sm text-gray-400">—</span>
                                @endif
                            </td>
                            
                            {{-- Target Role --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($ticket->target_role)
                                    <span class="text-sm text-gray-700">{{ $ticket->target_role }}</span>
                                @elseif($ticket->target_user_id)
                                    <span class="text-sm text-gray-700">{{ $ticket->targetUser->name ?? 'User tertentu' }}</span>
                                @else
                                    <span class="text-sm text-gray-500">Semua</span>
                                @endif
                            </td>
                            
                            {{-- Diambil Oleh --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($ticket->claimed_by)
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 text-white text-xs font-semibold flex items-center justify-center">
                                            {{ strtoupper(substr($ticket->claimedBy->name ?? '?', 0, 1)) }}
                                        </div>
                                        <span class="text-sm text-gray-900">{{ $ticket->claimedBy->name ?? 'Unknown' }}</span>
                                        @if($ticket->completed_at)
                                            <span class="text-xs text-gray-500">{{ $ticket->completed_at->diffForHumans() }}</span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-sm text-orange-600">Belum diambil</span>
                                @endif
                            </td>
                            
                            {{-- Status --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($ticket->status === 'todo')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-600 text-white">
                                        Todo
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
                            
                            {{-- Aksi --}}
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @if($ticket->status === 'todo' && !$ticket->isClaimed() && $ticket->canBeClaimedBy(auth()->user()))
                                    <form method="POST" action="{{ route('tickets.claim', $ticket) }}" class="inline-block">
                                        @csrf
                                        <button type="submit" class="px-3 py-1 bg-indigo-600 text-white rounded hover:bg-indigo-700 transition-colors text-xs font-medium">
                                            Ambil
                                        </button>
                                    </form>
                                @elseif($ticket->status === 'done')
                                    <button type="button" @click="selectedTicket = {{ \Illuminate\Support\Js::from($ticket) }}; showTicketModal = true" 
                                            class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700 transition-colors text-xs font-medium">
                                        Lepas
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center">
                                <div class="text-gray-400">
                                    <svg class="mx-auto h-12 w-12 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                    <p class="text-sm">Belum ada tiket</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Modal Detail Tiket --}}
    <template x-if="showTicketModal && selectedTicket">
        <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4" 
             @click.self="showTicketModal = false">
            <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto"
                 @click.stop>
                {{-- Modal Header --}}
                <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-4 flex items-center justify-between">
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
                                  'bg-amber-100 text-amber-700': selectedTicket.status === 'todo',
                                  'bg-purple-100 text-purple-700': selectedTicket.status === 'doing',
                                  'bg-green-100 text-green-700': selectedTicket.status === 'done',
                                  'bg-gray-600 text-white': selectedTicket.status === 'blackout'
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

                        {{-- Project/Umum Badge --}}
                        <span x-show="selectedTicket.project" 
                              class="inline-flex items-center gap-1 px-3 py-1 text-sm font-medium rounded-full bg-blue-100 text-blue-700">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                            </svg>
                            <span x-text="selectedTicket.project?.name || 'Unknown Project'"></span>
                        </span>
                        
                        <span x-show="!selectedTicket.project" 
                              class="inline-flex items-center gap-1 px-3 py-1 text-sm font-medium rounded-full bg-gray-100 text-gray-700">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                            </svg>
                            Tiket Umum
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
                                    <p class="font-semibold text-gray-900" x-text="new Date(selectedTicket.due_date).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })"></p>
                                </div>
                            </div>
                        </div>

                            {{-- Bobot --}}
                            <div x-show="selectedTicket.weight" class="bg-gray-50 rounded-lg p-3">
                                <div class="flex items-center gap-2 text-sm">
                                    <svg class="h-5 w-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" fill="none"/>
                                        <text x="12" y="16" text-anchor="middle" font-size="12" fill="currentColor">B</text>
                                    </svg>
                                    <div>
                                        <p class="text-xs text-gray-500">Bobot</p>
                                        <p class="font-semibold text-gray-900" x-text="selectedTicket.weight + ' - ' + (selectedTicket.weight <= 3 ? 'Ringan' : (selectedTicket.weight <= 6 ? 'Sedang' : 'Berat'))"></p>
                                    </div>
                                </div>
                            </div>
                        {{-- Claimed By --}}
                        <div x-show="selectedTicket.claimed_by" class="bg-gray-50 rounded-lg p-3">
                            <div class="flex items-center gap-2 text-sm">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 text-white text-xs font-semibold flex items-center justify-center">
                                    <span x-text="selectedTicket.claimed_by_user?.name?.charAt(0).toUpperCase() || '?'"></span>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Diambil oleh</p>
                                    <p class="font-semibold text-gray-900" x-text="selectedTicket.claimed_by_user?.name || 'Unknown'"></p>
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
                            <span class="text-xs text-gray-500" x-text="'• ' + new Date(selectedTicket.created_at).toLocaleDateString('id-ID')"></span>
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

    {{-- Modal Create Tiket Umum Component --}}
    @include('components.tickets.create-modal')

</div>
@endsection

