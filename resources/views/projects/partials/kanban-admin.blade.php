{{-- Kanban Board untuk PM/Admin - Enhanced Design --}}
@props(['project'])

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
                                            Set Todo
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
                                                Ambil Tiket
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
                                                Mulai Kerja
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
                                            Selesai
                                        </button>
                                    </form>
                                @endif

                            {{-- Status = DONE --}}
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
