{{-- Single Ticket Card Component --}}
@props(['ticket'])

<div class="group flex flex-col p-4 bg-white rounded-xl border-2 border-gray-200 hover:border-blue-400 hover:shadow-lg transition-all duration-300 min-h-[180px]">
    {{-- Header: Title + Badge --}}
    <div class="flex items-start justify-between gap-2 mb-3">
        <h4 class="font-semibold text-gray-900 text-sm leading-tight flex-1 group-hover:text-blue-600 transition-colors line-clamp-2">
            {{ $ticket->title }}
        </h4>
        @if($ticket->target_role)
            <span class="flex-shrink-0 text-[10px] px-2 py-1 bg-gradient-to-r from-purple-100 to-pink-100 text-purple-700 rounded-full font-semibold border border-purple-200 whitespace-nowrap">
                {{ \App\Models\Ticket::getAvailableRoles()[$ticket->target_role] ?? $ticket->target_role }}
            </span>
        @endif
    </div>
    
    {{-- Description --}}
    <div class="flex-1 mb-3">
        @if($ticket->description)
            <p class="text-xs text-gray-600 line-clamp-2 leading-relaxed">
                {{ Str::limit($ticket->description, 100) }}
            </p>
        @else
            <p class="text-xs text-gray-400 italic">Tidak ada deskripsi</p>
        @endif
    </div>
    
    {{-- Footer: Status + Actions --}}
    <div class="flex items-center justify-between gap-3 pt-3 border-t border-gray-100 mt-auto">
        {{-- Status Badge --}}
        <div class="flex-shrink-0">
            @if($ticket->status === 'todo')
                <span class="inline-flex items-center justify-center gap-1 text-[10px] px-2 py-1 bg-gradient-to-r from-amber-100 to-orange-100 text-amber-700 rounded-full font-bold border border-amber-200">
                    <svg class="h-3 w-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    To Do
                </span>
            @elseif($ticket->status === 'doing')
                <span class="inline-flex items-center justify-center gap-1 text-[10px] px-2 py-1 bg-gradient-to-r from-purple-100 to-indigo-100 text-purple-700 rounded-full font-bold border border-purple-200">
                    <svg class="h-3 w-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                    Doing
                </span>
            @elseif($ticket->status === 'done')
                <span class="inline-flex items-center justify-center gap-1 text-[10px] px-2 py-1 bg-blue-100 text-blue-700 rounded-full font-bold border border-blue-200">
                    <svg class="h-3 w-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Done
                </span>
            @else
                <span class="inline-flex items-center justify-center gap-1 text-[10px] px-2 py-1 bg-blue-100 text-blue-700 rounded-full font-bold border border-blue-200">
                    {{ ucfirst($ticket->status) }}
                </span>
            @endif
        </div>
        
        {{-- Action Buttons --}}
        <div class="flex items-center gap-2 ml-auto">
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
                class="inline-flex items-center justify-center gap-1.5 text-xs px-3 py-1.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 hover:shadow-sm transition-all font-medium min-w-[70px] h-[30px]">
                <svg class="h-3.5 w-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                <span>Detail</span>
            </button>

            {{-- Action Button --}}
            @if(!$ticket->isClaimed())
                {{-- Unclaimed: Show Ambil button --}}
                <form method="POST" action="{{ route('tickets.claim', $ticket) }}" class="inline-flex items-center">
                    @csrf
                    <button type="submit" class="inline-flex items-center justify-center gap-1.5 text-xs px-3 py-1.5 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-lg hover:from-green-700 hover:to-emerald-700 hover:shadow-md transition-all font-medium min-w-[70px] h-[30px]">
                        <svg class="h-3.5 w-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span>Ambil</span>
                    </button>
                </form>
            @elseif($ticket->claimed_by === auth()->id())
                {{-- Claimed by current user --}}
                @if($ticket->status === 'todo')
                    <form method="POST" action="{{ route('tickets.start', $ticket) }}" class="inline-flex items-center">
                        @csrf
                        <button type="submit" class="inline-flex items-center justify-center gap-1.5 text-xs px-3 py-1.5 bg-gradient-to-r from-purple-600 to-indigo-600 text-white rounded-lg hover:from-purple-700 hover:to-indigo-700 hover:shadow-md transition-all font-medium min-w-[70px] h-[30px]">
                            <svg class="h-3.5 w-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>Mulai</span>
                        </button>
                    </form>
                @elseif($ticket->status === 'doing')
                    <form method="POST" action="{{ route('tickets.complete', $ticket) }}" class="inline-flex items-center">
                        @csrf
                        <button type="submit" class="inline-flex items-center justify-center gap-1.5 text-xs px-3 py-1.5 bg-gradient-to-r from-green-600 to-teal-600 text-white rounded-lg hover:from-green-700 hover:to-teal-700 hover:shadow-md transition-all font-medium min-w-[80px] h-[30px]">
                            <svg class="h-3.5 w-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>Selesai</span>
                        </button>
                    </form>
                @elseif($ticket->status === 'done')
                    <span class="inline-flex items-center justify-center gap-1.5 text-xs px-3 py-1.5 bg-green-100 text-green-700 rounded-lg font-medium border border-green-300 min-w-[80px] h-[30px]">
                        <svg class="h-3.5 w-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span>Selesai</span>
                    </span>
                @endif
            @else
                {{-- Claimed by other user --}}
                <span class="inline-flex items-center justify-center gap-1.5 text-xs px-3 py-1.5 bg-gray-100 text-gray-600 rounded-lg border border-gray-200 min-w-[70px] max-w-[120px] h-[30px]" title="{{ $ticket->claimedBy?->name }}">
                    <svg class="h-3.5 w-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    <span class="truncate">{{ $ticket->claimedBy?->name }}</span>
                </span>
            @endif
        </div>
    </div>
</div>
