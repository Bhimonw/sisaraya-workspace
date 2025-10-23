{{-- Kanban Board untuk Member (Tiket Saya) - Enhanced Design --}}
@props(['project'])

@php
    $myTickets = $project->tickets->where('claimed_by', auth()->id());
@endphp

<div class="bg-white rounded-xl shadow-lg border border-purple-100 overflow-hidden hover:shadow-xl transition-shadow duration-300">
    <div class="bg-gradient-to-r from-purple-600 via-pink-600 to-purple-700 px-6 py-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-white/20 backdrop-blur-sm rounded-lg">
                    <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-bold text-white text-lg">Tiket Saya</h3>
                    <p class="text-xs text-purple-100">Tiket yang sedang Anda kerjakan</p>
                </div>
            </div>
            <span class="flex items-center gap-2 px-3 py-1.5 bg-white/25 backdrop-blur-sm rounded-full text-white font-semibold">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/>
                </svg>
                {{ $myTickets->count() }} tiket
            </span>
        </div>
    </div>

    <div class="p-6 bg-gradient-to-br from-gray-50 to-purple-50/30">
        {{-- Kanban Columns --}}
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-4">
@foreach([
    'blackout' => ['label' => 'Blackout', 'color' => 'gray', 'icon' => 'M18.364 18.364A9 9 0 0 0 5.636 5.636m12.728 12.728A9 9 0 0 0 5.636 5.636m12.728 12.728L5.636 5.636'],
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
                                        Mulai Kerja
                                    </button>
                                </form>
                            @elseif($ticket->status === 'doing')
                                <form method="POST" action="{{ route('tickets.complete', $ticket) }}" class="flex-1">
                                    @csrf
                                    <button type="submit" class="w-full text-xs px-3 py-1.5 bg-green-600 text-white rounded hover:bg-green-700 transition font-medium">
                                        Selesai
                                    </button>
                                </form>
                            @elseif($ticket->status === 'done')
                                <span class="flex-1 text-center text-xs px-3 py-1.5 bg-green-100 text-green-700 rounded font-medium">
                                    Selesai
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
</div>
