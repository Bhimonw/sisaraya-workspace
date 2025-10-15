<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'SISARAYA') }}</title>
        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <!-- Alpine.js cloak -->
        <style>
            [x-cloak] { display: none !important; }
        </style>
    </head>
    <body class="font-sans antialiased" x-data="{ 
        openSidebar: false, 
        sidebarCollapsed: localStorage.getItem('sidebarCollapsed') === 'true' || false 
    }" 
          @toggle-sidebar.window="
              if (window.innerWidth >= 768) { 
                  sidebarCollapsed = !sidebarCollapsed; 
                  localStorage.setItem('sidebarCollapsed', sidebarCollapsed);
              } else { 
                  openSidebar = !openSidebar; 
              }
          "
          x-init="$watch('sidebarCollapsed', value => localStorage.setItem('sidebarCollapsed', value))">
        <div class="min-h-screen bg-gray-100">
            <!-- Top navigation - FIXED -->
            <div class="fixed top-0 left-0 right-0 z-30">
                @include('layouts.navigation')
            </div>

            <!-- Main container with sidebar and content -->
            <div class="flex pt-16">
                <!-- Desktop sidebar (hidden on small screens) - FIXED with collapse -->
                <aside x-show="!sidebarCollapsed" 
                       x-transition:enter="transform transition ease-out duration-300"
                       x-transition:enter-start="-translate-x-full"
                       x-transition:enter-end="translate-x-0"
                       x-transition:leave="transform transition ease-in duration-300"
                       x-transition:leave-start="translate-x-0"
                       x-transition:leave-end="-translate-x-full"
                       class="hidden md:flex md:flex-col fixed left-0 top-16 bottom-0 z-20 w-64 bg-white border-r border-gray-200 overflow-hidden">
                    @include('layouts.sidebar')
                </aside>

                <!-- Main content area with dynamic left margin for sidebar on desktop -->
                <div class="flex-1 min-h-screen transition-all duration-300 ease-in-out"
                     :class="{ 'md:ml-64': !sidebarCollapsed, 'md:ml-0': sidebarCollapsed }">
                    <!-- Page Heading -->
                    @isset($header)
                        <header class="bg-white shadow">
                            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                                {{ $header }}
                            </div>
                        </header>
                    @endisset

                    <!-- Page Content -->
                    <main class="p-6">
                        @isset($slot)
                            {{ $slot }}
                        @else
                            @yield('content')
                        @endisset
                    </main>
                </div>
            </div>

            <!-- Mobile off-canvas sidebar (controlled by navigation's toggle) -->
            <div x-show="openSidebar" 
                 x-cloak 
                 @click.away="openSidebar = false"
                 class="fixed inset-0 z-40 flex md:hidden">
                <!-- Backdrop -->
                <div x-show="openSidebar"
                     x-transition:enter="transition-opacity ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition-opacity ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     @click="openSidebar = false" 
                     class="fixed inset-0 bg-black bg-opacity-50">
                </div>

                <!-- Sidebar -->
                <aside x-show="openSidebar"
                       x-transition:enter="transform transition ease-out duration-300"
                       x-transition:enter-start="-translate-x-full"
                       x-transition:enter-end="translate-x-0"
                       x-transition:leave="transform transition ease-in duration-200"
                       x-transition:leave-start="translate-x-0"
                       x-transition:leave-end="-translate-x-full"
                       class="relative w-64 bg-white border-r border-gray-200 h-full flex flex-col overflow-y-auto">
                    <!-- Mobile Sidebar Header -->
                    <div class="p-4 border-b border-gray-100 flex items-center justify-between">
                        <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                            <img src="{{ asset('logo-no-bg.png') }}" alt="SISARAYA" class="h-8 w-auto">
                            <span class="font-semibold text-lg">SISARAYA</span>
                        </a>
                        <button @click="openSidebar = false" class="p-2 text-gray-500 hover:text-gray-700 rounded-md hover:bg-gray-100">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Mobile Menu -->
                    <nav class="flex-1 overflow-y-auto p-4">
                        @include('layouts._menu')
                    </nav>

                    <!-- Mobile Footer -->
                    <div class="p-4 border-t border-gray-100">
                        @auth
                            <div class="text-sm text-gray-700">
                                <div class="font-medium truncate">{{ Auth::user()->name }}</div>
                                <div class="text-xs text-gray-500 truncate">{{ Auth::user()->email }}</div>
                            </div>
                        @endauth
                    </div>
                </aside>
            </div>
        </div>
    </body>
</html>
