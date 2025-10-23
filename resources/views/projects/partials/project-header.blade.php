{{-- Project Header --}}
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6" x-data="projectChatPopup({{ $project->id }})">
    <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
        <div class="flex-1">
            <div class="flex items-center flex-wrap gap-3 mb-2">
                <h1 class="text-3xl font-bold text-gray-900">{{ $project->name }}</h1>
                
                {{-- Status Badge --}}
                @php
                    $statusColor = \App\Models\Project::getStatusColor($project->status);
                    $colorClasses = [
                        'gray' => 'bg-gray-100 text-gray-700 border-gray-300',
                        'blue' => 'bg-blue-100 text-blue-700 border-blue-300',
                        'yellow' => 'bg-yellow-100 text-yellow-700 border-yellow-300',
                        'green' => 'bg-green-100 text-green-700 border-green-300',
                    ];
                @endphp
                <span class="inline-flex items-center px-3 py-1 text-sm font-medium rounded-full border {{ $colorClasses[$statusColor] ?? 'bg-gray-100 text-gray-700 border-gray-300' }}">
                    {{ \App\Models\Project::getStatusLabel($project->status) }}
                </span>
                
                {{-- Label/Tag Badge --}}
                <x-project-label-badge :label="$project->label" />
            </div>
            <p class="text-gray-600 text-base leading-relaxed mb-3">{{ $project->description }}</p>
            
            {{-- Project Timeline --}}
            @if($project->start_date || $project->end_date)
            <div class="flex items-center gap-2 text-sm">
                <svg class="h-5 w-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <span class="font-semibold text-gray-700">Timeline:</span>
                <span class="text-indigo-600 font-medium">
                    @if($project->start_date)
                        {{ $project->start_date->format('d M Y') }}
                    @else
                        <span class="text-gray-400">-</span>
                    @endif
                    <span class="mx-1 text-gray-400">→</span>
                    @if($project->end_date)
                        {{ $project->end_date->format('d M Y') }}
                    @else
                        <span class="text-gray-400">-</span>
                    @endif
                </span>
                @if($project->start_date && $project->end_date)
                    @php
                        $duration = $project->start_date->diffInDays($project->end_date);
                    @endphp
                    <span class="text-xs text-gray-500 bg-gray-100 px-2 py-0.5 rounded-full">
                        {{ $duration }} hari
                    </span>
                @endif
            </div>
            @else
            <div class="flex items-center gap-2 text-sm text-gray-400">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <span class="italic">Tanpa batas waktu tertentu</span>
            </div>
            @endif
        </div>
        
        {{-- Right Side: Chat Button & Members Preview --}}
        <div class="flex flex-col items-end gap-3 lg:ml-6">
            {{-- Chat Button --}}
            <button @click="toggleChat()" 
                    class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-blue-500 to-cyan-500 text-white rounded-lg hover:from-blue-600 hover:to-cyan-600 transition-all duration-300 shadow-md hover:shadow-lg relative">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
                <span class="text-sm font-medium">Chat Proyek</span>
                <template x-if="unreadCount > 0">
                    <span class="absolute -top-2 -right-2 px-2 py-0.5 bg-red-500 text-white text-xs font-bold rounded-full" x-text="unreadCount"></span>
                </template>
            </button>
            
            {{-- Members Preview --}}
            <div class="flex items-center gap-4">
                <div class="text-sm text-gray-500 font-medium">{{ $project->members->count() }} Members</div>
                <div class="flex -space-x-2">
                    @foreach($project->members->take(5) as $member)
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 text-white text-sm font-semibold flex items-center justify-center border-2 border-white shadow-sm" 
                             title="{{ $member->name }}">
                            {{ strtoupper(substr($member->name, 0, 1)) }}
                        </div>
                    @endforeach
                    @if($project->members->count() > 4)
                        <div class="w-10 h-10 rounded-full bg-gray-200 text-gray-600 text-xs font-semibold flex items-center justify-center border-2 border-white shadow-sm" 
                             title="{{ $project->members->count() - 4 }} more">
                            +{{ $project->members->count() - 4 }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    {{-- Chat Popup (Fixed Position, Bottom Right) --}}
    <div x-show="showChat" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform translate-y-4"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 transform translate-y-0"
         x-transition:leave-end="opacity-0 transform translate-y-4"
         @click.away="showChat = false"
         class="fixed bottom-6 right-6 w-96 bg-white rounded-lg shadow-2xl border-2 border-blue-300 overflow-hidden z-50"
         style="max-height: 600px;">
        
        {{-- Chat Header --}}
        <div class="bg-gradient-to-r from-blue-600 to-cyan-600 px-4 py-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                    <h3 class="font-semibold text-white">Chat Proyek</h3>
                </div>
                <button @click="showChat = false" class="text-white hover:bg-white/20 rounded p-1 transition">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
        
        {{-- Chat Messages --}}
        <div class="h-96 overflow-y-auto bg-gray-50 p-4 space-y-3"
             x-ref="messagesContainer"
             @scroll="handleScroll">

<template x-if="loading">
    <div class="flex items-center justify-center h-full">
        <div class="text-center">
            <svg class="animate-spin h-8 w-8 text-blue-600 mx-auto mb-2" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <p class="text-sm text-gray-600">Memuat pesan...</p>
        </div>
    </div>
</template>

<template x-if="!loading && messages.length === 0">
    <div class="flex items-center justify-center h-full">
        <div class="text-center">
            <svg class="h-16 w-16 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
            </svg>
            <p class="text-sm font-medium text-gray-500">Belum ada pesan</p>
            <p class="text-xs text-gray-400 mt-1">Mulai percakapan dengan tim</p>
        </div>
    </div>
</template>

{{-- Message List --}}
<template x-for="message in messages" :key="message.id">
    <div class="flex gap-2" 
         :class="message.user_id === {{ auth()->id() }} ? 'flex-row-reverse' : 'flex-row'">
        {{-- Avatar --}}
        <div class="flex-shrink-0">
            <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-sm font-semibold"
                 :class="message.user_id === {{ auth()->id() }} ? 'bg-gradient-to-br from-blue-600 to-cyan-600' : 'bg-gradient-to-br from-indigo-600 to-purple-600'"
                 x-text="message.user_name.charAt(0).toUpperCase()">
            </div>
        </div>
        
        {{-- Message Bubble --}}
        <div class="flex flex-col max-w-[70%]"
             :class="message.user_id === {{ auth()->id() }} ? 'items-end' : 'items-start'">
            <div class="text-xs text-gray-600 mb-1" x-text="message.user_name"></div>
            <div class="rounded-lg px-3 py-2 break-words text-sm"
                 :class="message.user_id === {{ auth()->id() }} ? 'bg-blue-600 text-white rounded-br-none' : 'bg-white border border-gray-200 text-gray-900 rounded-bl-none'"
                 x-text="message.message">
            </div>
            <div class="text-xs text-gray-500 mt-1" x-text="message.time_ago"></div>
        </div>
    </div>
</template>
</div>

{{-- Chat Input --}}
<div class="p-3 bg-white border-t border-gray-200">
    <form @submit.prevent="sendMessage()" class="flex gap-2">
        <input type="text" 
               x-model="newMessage"
               placeholder="Ketik pesan..."
               :disabled="sending"
               class="flex-1 px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-100"
               required>
        <button type="submit" 
                :disabled="sending || !newMessage.trim()"
                class="px-4 py-2 bg-gradient-to-r from-blue-600 to-cyan-600 text-white font-medium rounded-lg hover:from-blue-700 hover:to-cyan-700 disabled:opacity-50 disabled:cursor-not-allowed transition">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
            </svg>
        </button>
    </form>
</div>
    </div>{{-- End Chat Popup --}}
</div>{{-- End Project Header --}}
