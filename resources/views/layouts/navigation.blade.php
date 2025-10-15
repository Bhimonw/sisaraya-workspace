<nav x-data="{ open: false }" class="bg-white border-b border-gray-100 shadow-sm">
    <!-- Primary Navigation Menu -->
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center gap-4">
                <!-- Desktop sidebar toggle -->
                <div class="hidden md:flex items-center">
                    <button @click="$dispatch('toggle-sidebar')" class="p-2 rounded-md text-gray-500 hover:bg-gray-100 hover:text-gray-700 transition">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>

                <!-- Logo (Mobile) -->
                <div class="shrink-0 flex items-center md:hidden">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                    </a>
                </div>

                <!-- Mobile sidebar toggle -->
                <div class="flex items-center md:hidden">
                    <button @click="$dispatch('toggle-sidebar')" class="p-2 rounded-md text-gray-500 hover:bg-gray-100">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>

                <!-- Page Title / Breadcrumb -->
                <div class="hidden md:flex items-center">
                    @php
                        $currentRoute = Route::currentRouteName();
                        $segments = Request::segments();
                        
                        // Mapping route ke nama dan icon dengan breadcrumb links
                        $routeData = [
                            'dashboard' => ['name' => 'Dashboard', 'icon' => 'home', 'breadcrumbs' => []],
                            'calendar.personal' => ['name' => 'Ruang Pribadi > Kalender Pribadi', 'icon' => 'calendar', 'breadcrumbs' => [
                                ['name' => 'Ruang Pribadi', 'route' => null],
                                ['name' => 'Kalender Pribadi', 'route' => 'calendar.personal']
                            ]],
                            'workspace' => ['name' => 'Meja Kerja > Projectku', 'icon' => 'clipboard', 'breadcrumbs' => [
                                ['name' => 'Meja Kerja', 'route' => null],
                                ['name' => 'Projectku', 'route' => 'workspace']
                            ]],
                            'projects.allMine' => ['name' => 'Meja Kerja > Semua Projectku', 'icon' => 'folder', 'breadcrumbs' => [
                                ['name' => 'Meja Kerja', 'route' => null],
                                ['name' => 'Semua Projectku', 'route' => 'projects.allMine']
                            ]],
                            'projects.index' => ['name' => 'Ruang Management > Manajemen Proyek', 'icon' => 'folder', 'breadcrumbs' => [
                                ['name' => 'Ruang Management', 'route' => null],
                                ['name' => 'Manajemen Proyek', 'route' => 'projects.index']
                            ]],
                            'projects.show' => ['name' => 'Proyek > Detail', 'icon' => 'folder', 'breadcrumbs' => [
                                ['name' => 'Proyek', 'route' => 'projects.allMine'],
                                ['name' => 'Detail', 'route' => null]
                            ]],
                            'projects.create' => ['name' => 'Proyek > Buat Proyek', 'icon' => 'folder', 'breadcrumbs' => [
                                ['name' => 'Proyek', 'route' => 'projects.allMine'],
                                ['name' => 'Buat Proyek', 'route' => null]
                            ]],
                            'projects.edit' => ['name' => 'Proyek > Edit', 'icon' => 'folder', 'breadcrumbs' => [
                                ['name' => 'Proyek', 'route' => 'projects.allMine'],
                                ['name' => 'Edit', 'route' => null]
                            ]],
                            'projects.mine' => ['name' => 'Proyekku', 'icon' => 'folder', 'breadcrumbs' => []],
                            'tickets.index' => ['name' => 'Ruang Management > Manajemen Tiket', 'icon' => 'ticket', 'breadcrumbs' => [
                                ['name' => 'Ruang Management', 'route' => null],
                                ['name' => 'Manajemen Tiket', 'route' => 'tickets.index']
                            ]],
                            'tickets.mine' => ['name' => 'Meja Kerja > Tiketku', 'icon' => 'ticket', 'breadcrumbs' => [
                                ['name' => 'Meja Kerja', 'route' => null],
                                ['name' => 'Tiketku', 'route' => 'tickets.mine']
                            ]],
                            'tickets.overview' => ['name' => 'Meja Kerja > Semua Tiketku', 'icon' => 'ticket', 'breadcrumbs' => [
                                ['name' => 'Meja Kerja', 'route' => null],
                                ['name' => 'Semua Tiketku', 'route' => 'tickets.overview']
                            ]],
                            'tickets.show' => ['name' => 'Tiket > Detail', 'icon' => 'ticket', 'breadcrumbs' => [
                                ['name' => 'Tiket', 'route' => 'tickets.overview'],
                                ['name' => 'Detail', 'route' => null]
                            ]],
                            'rabs.index' => ['name' => 'RAB & Laporan', 'icon' => 'currency', 'breadcrumbs' => []],
                            'rabs.create' => ['name' => 'RAB & Laporan > Pengajuan RAB', 'icon' => 'currency', 'breadcrumbs' => [
                                ['name' => 'RAB & Laporan', 'route' => 'rabs.index'],
                                ['name' => 'Pengajuan RAB', 'route' => null]
                            ]],
                            'rabs.show' => ['name' => 'RAB & Laporan > Detail RAB', 'icon' => 'currency', 'breadcrumbs' => [
                                ['name' => 'RAB & Laporan', 'route' => 'rabs.index'],
                                ['name' => 'Detail RAB', 'route' => null]
                            ]],
                            'documents.index' => ['name' => 'Ruang Dokumen', 'icon' => 'document', 'breadcrumbs' => []],
                            'admin.users.index' => ['name' => 'Ruang Management > Manajemen Anggota', 'icon' => 'users', 'breadcrumbs' => [
                                ['name' => 'Ruang Management', 'route' => null],
                                ['name' => 'Manajemen Anggota', 'route' => 'admin.users.index']
                            ]],
                            'admin.users.create' => ['name' => 'Ruang Management > Tambah User', 'icon' => 'users', 'breadcrumbs' => [
                                ['name' => 'Ruang Management', 'route' => null],
                                ['name' => 'Tambah User', 'route' => null]
                            ]],
                            'admin.users.edit' => ['name' => 'Ruang Management > Edit User', 'icon' => 'users', 'breadcrumbs' => [
                                ['name' => 'Ruang Management', 'route' => null],
                                ['name' => 'Edit User', 'route' => null]
                            ]],
                            'businesses.index' => ['name' => 'Ruang Management > Usaha Aktif', 'icon' => 'shop', 'breadcrumbs' => [
                                ['name' => 'Ruang Management', 'route' => null],
                                ['name' => 'Usaha Aktif', 'route' => 'businesses.index']
                            ]],
                            'profile.edit' => ['name' => 'Akun & Pengaturan', 'icon' => 'settings', 'breadcrumbs' => []],
                        ];
                        
                        $pageData = $routeData[$currentRoute] ?? ['name' => ucfirst(str_replace(['-', '_'], ' ', $segments[0] ?? 'Dashboard')), 'icon' => 'home', 'breadcrumbs' => []];
                        $pageName = $pageData['name'];
                        $pageIcon = $pageData['icon'];
                        $breadcrumbs = $pageData['breadcrumbs'] ?? [];
                        
                        // Icon SVG paths
                        $icons = [
                            'home' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
                            'calendar' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
                            'clipboard' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',
                            'folder' => 'M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z',
                            'ticket' => 'M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z',
                            'currency' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                            'document' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
                            'users' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z',
                            'shop' => 'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z',
                            'settings' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z',
                        ];
                    @endphp
                    
                    <div class="flex items-center gap-2 text-sm">
                        <!-- Icon -->
                        <svg class="h-5 w-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icons[$pageIcon] ?? $icons['home'] }}" />
                        </svg>
                        
                        @if(count($breadcrumbs) > 0)
                            {{-- Use breadcrumbs with clickable links --}}
                            @foreach($breadcrumbs as $index => $crumb)
                                @if($index > 0)
                                    <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                @endif
                                
                                @if($crumb['route'] && Route::has($crumb['route']))
                                    {{-- Clickable link --}}
                                    <a href="{{ route($crumb['route']) }}" class="text-gray-600 hover:text-indigo-600 transition-colors">
                                        {{ $crumb['name'] }}
                                    </a>
                                @else
                                    {{-- Current page or non-linkable item --}}
                                    <span class="{{ $index === count($breadcrumbs) - 1 ? 'font-semibold text-gray-800' : 'text-gray-600' }}">
                                        {{ $crumb['name'] }}
                                    </span>
                                @endif
                            @endforeach
                        @elseif(strpos($pageName, '>') !== false)
                            {{-- Fallback to old behavior if no breadcrumbs defined --}}
                            @php $parts = explode(' > ', $pageName); @endphp
                            @foreach($parts as $index => $part)
                                @if($index > 0)
                                    <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                @endif
                                <span class="{{ $index === count($parts) - 1 ? 'font-semibold text-gray-800' : 'text-gray-600' }}">
                                    {{ $part }}
                                </span>
                            @endforeach
                        @else
                            {{-- Single page name --}}
                            <span class="font-semibold text-gray-800">{{ $pageName }}</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Right Side: Notifications & User -->
            @auth
            <div class="flex items-center gap-3">
                <!-- Notifications Dropdown -->
                <x-dropdown align="right" width="96">
                    <x-slot name="trigger">
                        <button class="hidden sm:flex p-2 rounded-md text-gray-500 hover:bg-gray-100 hover:text-gray-700 transition relative">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                            <!-- Notification badge -->
                            @php
                                $unreadCount = Auth::user()->unreadNotifications->count();
                            @endphp
                            @if($unreadCount > 0)
                            <span class="absolute top-0.5 right-0.5 flex items-center justify-center h-5 w-5 bg-red-500 text-white text-[10px] font-bold rounded-full">
                                {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                            </span>
                            @endif
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div class="py-2 w-96">
                            {{-- Header --}}
                            <div class="px-4 py-3 border-b border-gray-200">
                                <div class="flex items-center justify-between">
                                    <h3 class="text-sm font-semibold text-gray-800">Notifikasi</h3>
                                    @if($unreadCount > 0)
                                    <form method="POST" action="{{ route('notifications.markAllAsRead') }}" class="inline">
                                        @csrf
                                        <button type="submit" class="text-xs text-indigo-600 hover:text-indigo-800">
                                            Tandai semua dibaca
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </div>

                            {{-- Notifications List --}}
                            <div class="max-h-96 overflow-y-auto">
                                @forelse(Auth::user()->notifications()->take(10)->get() as $notification)
                                @php
                                    // Ensure action_url is absolute path
                                    $actionUrl = $notification->data['action_url'] ?? '#';
                                    // If it's a relative path (starts with /), convert to absolute URL
                                    if ($actionUrl !== '#' && !filter_var($actionUrl, FILTER_VALIDATE_URL)) {
                                        $actionUrl = url($actionUrl);
                                    }
                                @endphp
                                <a href="{{ $actionUrl }}" 
                                   onclick="event.preventDefault(); window.location.href='{{ $actionUrl }}';"
                                   class="block px-4 py-3 hover:bg-gray-50 transition {{ $notification->read_at ? 'bg-white' : 'bg-indigo-50' }} cursor-pointer">
                                    <div class="flex gap-3">
                                        {{-- Icon - Different for Project and Ticket --}}
                                        <div class="flex-shrink-0">
                                            @if(str_contains($notification->type, 'TicketAssigned'))
                                                {{-- Ticket Icon --}}
                                                <div class="w-10 h-10 rounded-full {{ $notification->data['is_specific'] ?? false ? 'bg-purple-100' : 'bg-blue-100' }} flex items-center justify-center">
                                                    <svg class="h-5 w-5 {{ $notification->data['is_specific'] ?? false ? 'text-purple-600' : 'text-blue-600' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                                                    </svg>
                                                </div>
                                            @else
                                                {{-- Project Icon --}}
                                                <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center">
                                                    <svg class="h-5 w-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                                                    </svg>
                                                </div>
                                            @endif
                                        </div>
                                        
                                        {{-- Content --}}
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900">
                                                {{ $notification->data['message'] ?? 'Notifikasi baru' }}
                                            </p>
                                            
                                            {{-- Additional Info --}}
                                            @if(isset($notification->data['ticket_title']))
                                                {{-- Ticket Notification --}}
                                                <p class="text-xs text-gray-600 mt-1">
                                                    @if($notification->data['is_specific'] ?? false)
                                                        <span class="px-1.5 py-0.5 bg-purple-100 text-purple-700 rounded text-[10px] font-medium">Khusus Anda</span>
                                                    @elseif(isset($notification->data['target_role_label']))
                                                        Role: {{ $notification->data['target_role_label'] }}
                                                    @else
                                                        <span class="px-1.5 py-0.5 bg-green-100 text-green-700 rounded text-[10px] font-medium">Umum</span>
                                                    @endif
                                                </p>
                                            @elseif(isset($notification->data['role']))
                                                {{-- Project Member Notification --}}
                                                <p class="text-xs text-gray-600 mt-1">
                                                    Role: {{ $notification->data['role'] === 'admin' ? 'Admin Project' : 'Member' }}
                                                    @if(isset($notification->data['event_role']) && $notification->data['event_role'])
                                                        | {{ \App\Models\Ticket::getAllRoles()[$notification->data['event_role']] ?? $notification->data['event_role'] }}
                                                    @endif
                                                </p>
                                            @endif
                                            
                                            <p class="text-xs text-gray-500 mt-1">
                                                {{ $notification->created_at->diffForHumans() }}
                                            </p>
                                        </div>
                                        
                                        {{-- Unread indicator --}}
                                        @if(!$notification->read_at)
                                        <div class="flex-shrink-0">
                                            <span class="inline-block w-2 h-2 bg-indigo-600 rounded-full"></span>
                                        </div>
                                        @endif
                                    </div>
                                </a>
                                @empty
                                <div class="px-4 py-8 text-center text-gray-500">
                                    <svg class="h-12 w-12 mx-auto text-gray-300 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                    </svg>
                                    <p class="text-sm">Tidak ada notifikasi</p>
                                </div>
                                @endforelse
                            </div>

                            {{-- Footer --}}
                            @if(Auth::user()->notifications()->count() > 0)
                            <div class="px-4 py-3 border-t border-gray-200">
                                <a href="{{ route('notifications.index') }}" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">
                                    Lihat semua notifikasi →
                                </a>
                            </div>
                            @endif
                        </div>
                    </x-slot>
                </x-dropdown>

                <!-- User Dropdown -->
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center gap-2 px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none transition ease-in-out duration-150">
                            <div class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center">
                                <span class="text-sm font-semibold text-indigo-600">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </span>
                            </div>
                            <div class="hidden sm:block text-left">
                                <div class="text-sm font-medium text-gray-800">{{ Str::limit(Auth::user()->name, 15) }}</div>
                                <div class="text-xs text-gray-500">
                                    @if(method_exists(Auth::user(), 'getRoleNames'))
                                        {{ ucfirst(Auth::user()->getRoleNames()->first() ?? 'User') }}
                                    @endif
                                </div>
                            </div>
                            <svg class="fill-current h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div class="px-4 py-3 border-b border-gray-100">
                            <div class="font-medium text-sm text-gray-800">{{ Auth::user()->name }}</div>
                            <div class="text-xs text-gray-500">{{ Auth::user()->email }}</div>
                            <div class="mt-1">
                                @if(method_exists(Auth::user(), 'getRoleNames'))
                                    @foreach(Auth::user()->getRoleNames() as $role)
                                        <span class="inline-block px-2 py-0.5 text-xs bg-indigo-100 text-indigo-700 rounded mr-1">
                                            {{ ucfirst($role) }}
                                        </span>
                                    @endforeach
                                @endif
                            </div>
                        </div>

                        <x-dropdown-link :href="route('profile.edit')">
                            <div class="flex items-center gap-2">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                {{ __('Profile') }}
                            </div>
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                <div class="flex items-center gap-2 text-red-600">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                    </svg>
                                    {{ __('Log Out') }}
                                </div>
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>
            @else
            <div class="hidden sm:flex sm:items-center sm:ml-6">
                <a href="{{ route('login') }}" class="text-sm text-gray-700 underline">{{ __('Log in') }}</a>
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="ml-4 text-sm text-gray-700 underline">{{ __('Register') }}</a>
                @endif
            </div>
            @endauth

            <!-- Hamburger -->
            <div class="-mr-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            @include('layouts._menu')
        </div>

        <!-- Responsive Settings Options -->
        @auth
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
        @else
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <a href="{{ route('login') }}" class="block text-sm text-gray-700">{{ __('Log in') }}</a>
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="mt-2 block text-sm text-gray-700">{{ __('Register') }}</a>
                @endif
            </div>
        </div>
        @endauth
    </div>
</nav>
