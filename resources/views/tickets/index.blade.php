@extends('layouts.app')

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
        
        {{-- Statistics --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-6">
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg p-4 border border-blue-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-blue-600">Total Tiket</p>
                        <p class="text-2xl font-bold text-blue-900">{{ $allTickets->count() }}</p>
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
                        <p class="text-2xl font-bold text-amber-900">{{ $allTickets->where('status', 'todo')->count() }}</p>
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
                        <p class="text-2xl font-bold text-purple-900">{{ $allTickets->where('status', 'doing')->count() }}</p>
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
                        <p class="text-2xl font-bold text-green-900">{{ $allTickets->where('status', 'done')->count() }}</p>
                    </div>
                    <div class="w-12 h-12 bg-green-200 rounded-full flex items-center justify-center">
                        <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
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
                            Prioritas
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
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $ticket->title }}</div>
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

    {{-- Modal Create Tiket Umum --}}
    <template x-if="showCreateModal">
        <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4" 
             @click.self="showCreateModal = false">
            <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto"
                 @click.stop>
                {{-- Modal Header --}}
                <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-4 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-white">Buat Tiket Umum</h3>
                    <button @click="showCreateModal = false" 
                            class="text-white hover:text-gray-200 transition-colors">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- Form --}}
                <form method="POST" action="{{ route('tickets.store') }}" class="p-6 space-y-4">
                    @csrf
                    <input type="hidden" name="project_id" value="">
                    <input type="hidden" name="context" value="umum">

                    {{-- Title --}}
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Judul Tiket *</label>
                        <input type="text" 
                               name="title" 
                               id="title" 
                               required
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                               placeholder="Masukkan judul tiket">
                    </div>

                    {{-- Description --}}
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                        <textarea name="description" 
                                  id="description" 
                                  rows="4"
                                  class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                  placeholder="Deskripsi detail tiket..."></textarea>
                    </div>

                    {{-- Priority & Due Date --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="priority" class="block text-sm font-medium text-gray-700 mb-1">Prioritas</label>
                            <select name="priority" 
                                    id="priority"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="low">Rendah</option>
                                <option value="medium" selected>Sedang</option>
                                <option value="high">Tinggi</option>
                                <option value="urgent">Mendesak</option>
                            </select>
                        </div>

                        <div>
                            <label for="due_date" class="block text-sm font-medium text-gray-700 mb-1">Deadline</label>
                            <input type="date" 
                                   name="due_date" 
                                   id="due_date"
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                    </div>

                    {{-- Target Selection --}}
                    <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                        <label class="block text-sm font-medium text-gray-700 mb-3">Target Tiket (Opsional)</label>
                        
                        <div class="space-y-3">
                            {{-- Option 1: Semua Orang --}}
                            <label class="flex items-start cursor-pointer">
                                <input type="radio" name="target_type" value="all" checked
                                       class="mt-1 text-indigo-600 focus:ring-indigo-500"
                                       x-model="targetType">
                                <div class="ml-3">
                                    <span class="text-sm font-medium text-gray-900">Semua Orang</span>
                                    <p class="text-xs text-gray-500">Tiket bisa diambil siapa saja</p>
                                </div>
                            </label>

                            {{-- Option 2: Role Tetap --}}
                            <label class="flex items-start cursor-pointer">
                                <input type="radio" name="target_type" value="role"
                                       class="mt-1 text-indigo-600 focus:ring-indigo-500"
                                       x-model="targetType">
                                <div class="ml-3 flex-1">
                                    <span class="text-sm font-medium text-gray-900">Role Tetap</span>
                                    <p class="text-xs text-gray-500 mb-2">Targetkan ke semua user dengan role tertentu</p>
                                    <select name="target_role" 
                                            id="target_role"
                                            x-bind:disabled="targetType !== 'role'"
                                            x-bind:required="targetType === 'role'"
                                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm disabled:bg-gray-100 disabled:cursor-not-allowed">
                                        <option value="">Pilih Role...</option>
                                        @foreach(\App\Models\Ticket::getAvailableRoles() as $roleKey => $roleName)
                                            <option value="{{ $roleKey }}">{{ $roleName }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </label>

                            {{-- Option 3: User Spesifik (Multiple) --}}
                            <label class="flex items-start cursor-pointer">
                                <input type="radio" name="target_type" value="user"
                                       class="mt-1 text-indigo-600 focus:ring-indigo-500"
                                       x-model="targetType">
                                <div class="ml-3 flex-1">
                                    <span class="text-sm font-medium text-gray-900">User Spesifik (Bisa Multiple)</span>
                                    <p class="text-xs text-gray-500 mb-2">Targetkan ke beberapa user sekaligus - akan membuat tiket sebanyak user yang dipilih</p>
                                    
                                    {{-- Checkbox List Container --}}
                                    <div class="mt-2 border border-gray-300 rounded-lg p-3 bg-white max-h-48 overflow-y-auto"
                                         x-bind:class="targetType !== 'user' ? 'bg-gray-50 opacity-50' : ''">
                                        <div class="space-y-2">
                                            @foreach(\App\Models\User::orderBy('name')->get() as $user)
                                                <label class="flex items-center gap-2 cursor-pointer hover:bg-gray-50 px-2 py-1 rounded">
                                                    <input type="checkbox" 
                                                           name="target_user_id[]" 
                                                           value="{{ $user->id }}"
                                                           x-bind:disabled="targetType !== 'user'"
                                                           class="rounded text-indigo-600 focus:ring-indigo-500 disabled:cursor-not-allowed">
                                                    <span class="text-sm text-gray-700">{{ $user->name }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                    <p class="text-xs text-gray-400 mt-1">✓ Centang satu atau beberapa user yang ingin ditargetkan</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    {{-- Form Footer --}}
                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
                        <button type="button" 
                                @click="showCreateModal = false"
                                class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors font-medium">
                            Batal
                        </button>
                        <button type="submit"
                                class="px-4 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-lg hover:from-indigo-700 hover:to-purple-700 transition-colors font-medium">
                            Buat Tiket
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </template>

</div>
@endsection
