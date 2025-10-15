@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto">
    {{-- Header --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-3">
                    <svg class="h-8 w-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    Notifikasi
                </h1>
                <p class="text-gray-600 mt-1">Kelola dan lihat semua notifikasi Anda</p>
            </div>
            
            @if(auth()->user()->unreadNotifications->count() > 0)
            <form method="POST" action="{{ route('notifications.markAllAsRead') }}">
                @csrf
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                    Tandai Semua Dibaca
                </button>
            </form>
            @endif
        </div>
    </div>

    {{-- Notifications List --}}
    <div class="space-y-3">
        @forelse($notifications as $notification)
        <div class="bg-white rounded-lg shadow-sm border {{ $notification->read_at ? 'border-gray-200' : 'border-indigo-300 bg-indigo-50' }} p-5 hover:shadow-md transition">
            <div class="flex gap-4">
                {{-- Icon - Different for Project and Ticket --}}
                <div class="flex-shrink-0">
                    @if(str_contains($notification->type, 'TicketAssigned'))
                        {{-- Ticket Icon --}}
                        <div class="w-12 h-12 rounded-full {{ $notification->data['is_specific'] ?? false ? 'bg-purple-100' : 'bg-blue-100' }} flex items-center justify-center">
                            <svg class="h-6 w-6 {{ $notification->data['is_specific'] ?? false ? 'text-purple-600' : 'text-blue-600' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                            </svg>
                        </div>
                    @else
                        {{-- Project Icon --}}
                        <div class="w-12 h-12 rounded-full bg-indigo-100 flex items-center justify-center">
                            <svg class="h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                            </svg>
                        </div>
                    @endif
                </div>
                
                {{-- Content --}}
                <div class="flex-1 min-w-0">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1">
                            <p class="text-base font-semibold text-gray-900">
                                {{ $notification->data['message'] ?? 'Notifikasi baru' }}
                            </p>
                            
                            {{-- Details --}}
                            @if(isset($notification->data['ticket_title']))
                                {{-- Ticket Notification Details --}}
                                <div class="mt-2 space-y-1">
                                    <p class="text-sm text-gray-700">
                                        <span class="font-medium">Tiket:</span> {{ $notification->data['ticket_title'] }}
                                    </p>
                                    @if(isset($notification->data['ticket_description']))
                                    <p class="text-sm text-gray-600">
                                        {{ Str::limit($notification->data['ticket_description'], 100) }}
                                    </p>
                                    @endif
                                    @if(isset($notification->data['project_name']))
                                    <p class="text-sm text-gray-700">
                                        <span class="font-medium">Proyek:</span> {{ $notification->data['project_name'] }}
                                    </p>
                                    @endif
                                    
                                    <div class="flex flex-wrap gap-2 mt-2">
                                        {{-- Ticket Type Badge --}}
                                        @if($notification->data['is_specific'] ?? false)
                                        <span class="px-2 py-1 text-xs rounded-full bg-purple-100 text-purple-700 font-medium border border-purple-300">
                                            🎯 Khusus untuk Anda
                                        </span>
                                        @elseif(isset($notification->data['target_role_label']))
                                        <span class="px-2 py-1 text-xs rounded-full bg-amber-100 text-amber-700 font-medium border border-amber-300">
                                            👥 {{ $notification->data['target_role_label'] }}
                                        </span>
                                        @else
                                        <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-700 font-medium border border-green-300">
                                            🌐 Tiket Umum
                                        </span>
                                        @endif
                                        
                                        {{-- Status Badge --}}
                                        @if(isset($notification->data['ticket_status']))
                                        <span class="px-2 py-1 text-xs rounded-full 
                                            {{ $notification->data['ticket_status'] === 'todo' ? 'bg-gray-100 text-gray-700' : '' }}
                                            {{ $notification->data['ticket_status'] === 'doing' ? 'bg-blue-100 text-blue-700' : '' }}
                                            {{ $notification->data['ticket_status'] === 'done' ? 'bg-green-100 text-green-700' : '' }}">
                                            {{ ucfirst($notification->data['ticket_status']) }}
                                        </span>
                                        @endif
                                        
                                        {{-- Due Date Badge --}}
                                        @if(isset($notification->data['due_date']) && $notification->data['due_date'])
                                        <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-700">
                                            📅 {{ \Carbon\Carbon::parse($notification->data['due_date'])->format('d M Y') }}
                                        </span>
                                        @endif
                                    </div>
                                </div>
                            @elseif(isset($notification->data['project_name']))
                                {{-- Project Member Notification Details --}}
                                <div class="mt-2 space-y-1">
                                    <p class="text-sm text-gray-700">
                                        <span class="font-medium">Proyek:</span> {{ $notification->data['project_name'] }}
                                    </p>
                                    @if(isset($notification->data['role']))
                                    <p class="text-sm text-gray-700">
                                        <span class="font-medium">Role Project:</span> 
                                        <span class="px-2 py-0.5 text-xs rounded-full {{ $notification->data['role'] === 'admin' ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-700' }}">
                                            {{ $notification->data['role'] === 'admin' ? 'Admin' : 'Member' }}
                                        </span>
                                    </p>
                                    @endif
                                    @if(isset($notification->data['event_role']) && $notification->data['event_role'])
                                    <p class="text-sm text-gray-700">
                                        <span class="font-medium">Event Role:</span> 
                                        <span class="px-2 py-0.5 text-xs rounded-full bg-amber-100 text-amber-700">
                                            {{ \App\Models\Ticket::getAllRoles()[$notification->data['event_role']] ?? $notification->data['event_role'] }}
                                        </span>
                                    </p>
                                    @endif
                                    @if(isset($notification->data['project_status']))
                                    <p class="text-sm text-gray-600 mt-1">
                                        Status Proyek: 
                                        <span class="px-2 py-0.5 text-xs rounded-full 
                                            {{ $notification->data['project_status'] === 'active' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-700' }}">
                                            {{ \App\Models\Project::getStatusLabel($notification->data['project_status']) }}
                                        </span>
                                    </p>
                                    @endif
                                </div>
                            @endif
                            
                            <p class="text-xs text-gray-500 mt-2 flex items-center gap-2">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ $notification->created_at->diffForHumans() }}
                            </p>
                        </div>
                        
                        {{-- Unread Badge --}}
                        @if(!$notification->read_at)
                        <div class="flex-shrink-0">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-600 text-white">
                                Baru
                            </span>
                        </div>
                        @endif
                    </div>
                    
                    {{-- Actions --}}
                    <div class="flex items-center gap-3 mt-4">
                        @if(isset($notification->data['action_url']))
                        <a href="{{ $notification->data['action_url'] }}" 
                           class="text-sm text-indigo-600 hover:text-indigo-800 font-medium flex items-center gap-1">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                            Lihat Detail
                        </a>
                        @endif
                        
                        @if(!$notification->read_at)
                        <form method="POST" action="{{ route('notifications.markAsRead', $notification->id) }}" class="inline">
                            @csrf
                            <button type="submit" class="text-sm text-gray-600 hover:text-gray-800 font-medium flex items-center gap-1">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Tandai Dibaca
                            </button>
                        </form>
                        @endif
                        
                        <form method="POST" action="{{ route('notifications.destroy', $notification->id) }}" 
                              class="inline" 
                              onsubmit="return confirm('Hapus notifikasi ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-sm text-red-600 hover:text-red-800 font-medium flex items-center gap-1">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                Hapus
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
            <svg class="h-20 w-20 mx-auto text-gray-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
            </svg>
            <p class="text-gray-600 font-medium mb-1">Tidak ada notifikasi</p>
            <p class="text-sm text-gray-500">Notifikasi Anda akan muncul di sini</p>
        </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($notifications->hasPages())
    <div class="mt-6">
        {{ $notifications->links() }}
    </div>
    @endif
</div>
@endsection
