{{-- Calendar Grid (loaded via AJAX) --}}
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
                    {{ $isTimelineEnd ? 'border-r-4 border-r-indigo-600' : '' }}"
                    @if(count($day['events']) > 2)
                        x-data="{ showAllEvents: false }"
                        @click.away="showAllEvents = false"
                    @endif>
                    
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
                                Mulai
                            </div>
                        @endif
                        @if($isTimelineEnd)
                            <div class="text-[10px] font-bold text-indigo-700 px-1 mb-1">
                                Selesai
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
                                <button @click="showAllEvents = !showAllEvents" 
                                        type="button"
                                        class="text-xs text-indigo-600 hover:text-indigo-800 font-semibold px-1 py-0.5 hover:bg-indigo-100 rounded w-full text-left transition-colors">
                                    +{{ count($day['events']) - 2 }} lagi...
                                </button>
                                
                                {{-- Popover with all events - Fixed positioning --}}
                                <div x-show="showAllEvents" 
                                     x-transition
                                     @click.away="showAllEvents = false"
                                     class="fixed z-[9999] bg-white border-2 border-indigo-300 rounded-lg shadow-2xl p-3 w-72"
                                     style="display: none;"
                                     x-bind:style="{
                                         top: $el.parentElement.getBoundingClientRect().bottom + 4 + 'px',
                                         left: Math.min($el.parentElement.getBoundingClientRect().left, window.innerWidth - 300) + 'px'
                                     }">
                                    <div class="flex items-center justify-between mb-2 pb-2 border-b border-gray-200">
                                        <h4 class="font-bold text-sm text-gray-900">
                                            {{ $day['day'] }} {{ $calendar['month'] }} {{ $calendar['year'] }}
                                        </h4>
                                        <button @click="showAllEvents = false" 
                                                type="button"
                                                class="text-gray-400 hover:text-gray-600 transition-colors">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>
                                    </div>
                                    <div class="space-y-1.5 max-h-64 overflow-y-auto">
                                        @foreach($day['events'] as $event)
                                            <div class="text-xs p-2 rounded {{ \App\Helpers\CalendarHelper::getEventColorClass($event['type'], $event['status'] ?? null) }} text-white">
                                                <div class="font-semibold mb-1">{{ $event['title'] }}</div>
                                                <div class="text-[10px] opacity-90">
                                                    @if($event['type'] === 'event')
                                                        📅 Event Proyek
                                                    @elseif($event['type'] === 'ticket')
                                                        🎫 Deadline Tiket
                                                        @if(isset($event['status']))
                                                            <span class="ml-1 px-1 py-0.5 bg-white bg-opacity-20 rounded">
                                                                {{ ucfirst($event['status']) }}
                                                            </span>
                                                        @endif
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @endforeach
</div>
