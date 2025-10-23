{{-- Ticket Detail Modal --}}
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
                        To Do
                    </span>
                    <span x-show="selectedTicket.status === 'doing'" 
                          class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                        Sedang Dikerjakan
                    </span>
                    <span x-show="selectedTicket.status === 'done'" 
                          class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                        Selesai
                    </span>
                    <span x-show="selectedTicket.status === 'blackout'" 
                          class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                        Bank Ide
                    </span>
                </div>
            </div>

            <div x-show="selectedTicket.target_role">
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Target Role</span>
                <div class="mt-1">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-purple-100 text-purple-800">
                        <span x-text="selectedTicket.target_role"></span>
                    </span>
                </div>
            </div>

            <div x-show="selectedTicket.context">
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Konteks</span>
                <div class="mt-1">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800"
                          x-text="selectedTicket.context === 'event' ? 'Event' : selectedTicket.context === 'proyek' ? 'Proyek' : 'Umum'">
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
                        Set Todo - Mulai Mengerjakan
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
</div>
</template>{{-- End Modal Detail Tiket --}}
