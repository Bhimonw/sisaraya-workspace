{{-- Tiket Umum Section - Enhanced Design --}}
@props(['project'])

@php
    $generalTickets = $project->tickets->filter(function($ticket) {
        return $ticket->context === 'umum' || !$ticket->target_role;
    });
@endphp

<div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden hover:shadow-xl transition-shadow duration-300">
    <div class="bg-gradient-to-r from-gray-700 via-gray-800 to-gray-900 px-6 py-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-white/20 backdrop-blur-sm rounded-lg">
                    <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-bold text-white text-lg">Tiket Umum</h3>
                    <p class="text-xs text-gray-300">Tiket umum yang tidak terikat role tertentu</p>
                </div>
            </div>
            <span class="flex items-center gap-2 px-3 py-1.5 bg-white/25 backdrop-blur-sm rounded-full text-white font-semibold">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/>
                </svg>
                {{ $generalTickets->count() }} tiket
            </span>
        </div>
    </div>
    
    <div class="p-6 bg-gradient-to-br from-gray-50 to-slate-50/50">
        @if($generalTickets->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 max-h-[500px] overflow-y-auto pr-2 custom-scrollbar">
                @foreach($generalTickets as $ticket)
                <div class="group flex flex-col p-4 bg-white rounded-xl border-2 border-gray-200 hover:border-gray-400 hover:shadow-lg transition-all duration-300 min-h-[180px]">
                    {{-- Header: Title + Priority Badge --}}
                    <div class="flex items-start justify-between gap-2 mb-3">
                        <h4 class="font-semibold text-gray-900 text-sm flex-1 group-hover:text-gray-700 transition-colors line-clamp-2">
                            {{ $ticket->title }}
                        </h4>
                        @if($ticket->priority)
                            @php
                                $priorityClasses = [
                                    'urgent' => 'bg-gradient-to-r from-red-100 to-pink-100 text-red-700 border-red-200',
                                    'high' => 'bg-gradient-to-r from-orange-100 to-amber-100 text-orange-700 border-orange-200',
                                    'medium' => 'bg-gradient-to-r from-yellow-100 to-amber-100 text-yellow-700 border-yellow-200',
                                    'low' => 'bg-gradient-to-r from-gray-100 to-slate-100 text-gray-700 border-gray-200',
                                ];
                            @endphp
                            <span class="flex-shrink-0 text-[10px] px-2 py-1 {{ $priorityClasses[$ticket->priority] ?? $priorityClasses['low'] }} rounded-full font-semibold border">
                                {{ ucfirst($ticket->priority) }}
                            </span>
                        @endif
                    </div>
                    
                    {{-- Description: Flexible space --}}
                    <div class="flex-1 mb-3">
                        @if($ticket->description)
                            <p class="text-xs text-gray-600 line-clamp-2 leading-relaxed">
                                {{ Str::limit($ticket->description, 100) }}
                            </p>
                        @else
                            <p class="text-xs text-gray-400 italic">Tidak ada deskripsi</p>
                        @endif
                    </div>
                    
                    {{-- Footer: Status + Claimed + Action Buttons (forced to bottom) --}}
                    <div class="flex items-center justify-between gap-3 pt-3 border-t border-gray-100 mt-auto">
                        <div class="flex items-center gap-2 min-w-0 flex-shrink">
                            {{-- Status Badge --}}
                            @if($ticket->status === 'todo')
                                <span class="inline-flex items-center justify-center gap-1 text-[10px] px-2 py-1 bg-gradient-to-r from-amber-100 to-orange-100 text-amber-700 rounded-full font-bold border border-amber-200 min-w-[70px]">
                                    <svg class="h-3 w-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    To Do
                                </span>
                            @elseif($ticket->status === 'doing')
                                <span class="inline-flex items-center justify-center gap-1 text-[10px] px-2 py-1 bg-gradient-to-r from-purple-100 to-indigo-100 text-purple-700 rounded-full font-bold border border-purple-200 min-w-[70px]">
                                    <svg class="h-3 w-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                    </svg>
                                    Doing
                                </span>
                            @elseif($ticket->status === 'done')
                                <span class="inline-flex items-center justify-center gap-1 text-[10px] px-2 py-1 bg-gradient-to-r from-green-100 to-teal-100 text-green-700 rounded-full font-bold border border-green-200 min-w-[70px]">
                                    <svg class="h-3 w-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Done
                                </span>
                            @else
                                <span class="inline-flex items-center justify-center gap-1 text-[10px] px-2 py-1 bg-gray-100 text-gray-700 rounded-full font-bold border border-gray-200 min-w-[70px]">
                                    {{ ucfirst($ticket->status) }}
                                </span>
                            @endif
                            
                            @if($ticket->claimed_by)
                                <span class="inline-flex items-center justify-center gap-1 text-[10px] px-2 py-1 bg-blue-100 text-blue-700 rounded-lg border border-blue-200 font-medium whitespace-nowrap">
                                    <svg class="h-3 w-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    {{ $ticket->claimedBy?->name }}
                                </span>
                            @endif
                        </div>
                        
                        <div class="flex items-center gap-2 flex-shrink-0">
                            {{-- Detail Button - CONSISTENT SIZE --}}
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
                                class="inline-flex items-center justify-center gap-1 text-xs px-3 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 hover:shadow-sm transition-all font-medium whitespace-nowrap">
                                <svg class="h-3.5 w-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                Detail
                            </button>

                            {{-- Action Buttons - CONSISTENT SIZE --}}
                            @if(!$ticket->isClaimed())
                                <form method="POST" action="{{ route('tickets.claim', $ticket) }}">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center justify-center gap-1 text-xs px-3 py-2 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-lg hover:from-green-700 hover:to-emerald-700 hover:shadow-md transition-all font-semibold whitespace-nowrap">
                                        <svg class="h-3.5 w-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        Ambil
                                    </button>
                                </form>
                            @elseif($ticket->claimed_by === auth()->id())
                                @if($ticket->status === 'todo')
                                    <form method="POST" action="{{ route('tickets.start', $ticket) }}">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center justify-center gap-1 text-xs px-3 py-2 bg-gradient-to-r from-purple-600 to-indigo-600 text-white rounded-lg hover:from-purple-700 hover:to-indigo-700 hover:shadow-md transition-all font-semibold whitespace-nowrap">
                                            <svg class="h-3.5 w-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            Mulai
                                        </button>
                                    </form>
                                @elseif($ticket->status === 'doing')
                                    <form method="POST" action="{{ route('tickets.complete', $ticket) }}">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center justify-center gap-1 text-xs px-3 py-2 bg-gradient-to-r from-green-600 to-teal-600 text-white rounded-lg hover:from-green-700 hover:to-teal-700 hover:shadow-md transition-all font-semibold whitespace-nowrap">
                                            <svg class="h-3.5 w-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            Selesai
                                        </button>
                                    </form>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-12">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-gray-100 to-slate-100 rounded-full mb-4">
                    <svg class="h-8 w-8 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                    </svg>
                </div>
                <p class="text-sm text-gray-600 font-semibold mb-1">Tidak ada tiket umum</p>
                <p class="text-xs text-gray-400">Belum ada tiket umum untuk proyek ini</p>
            </div>
        @endif
    </div>
</div>
