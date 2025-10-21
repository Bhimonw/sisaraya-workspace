@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
    
    {{-- ========================================
         BACK BUTTON
         ======================================== --}}
    <div class="mb-4">
        <x-back-button url="{{ route('dashboard') }}" />
    </div>

    {{-- ========================================
         HEADER SECTION
         ======================================== --}}
    <div class="mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-4">
            <div>
                <h1 class="text-3xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                    Manajemen Anggota
                </h1>
                <p class="text-gray-600 mt-1">Kelola akun pengguna Sisaraya</p>
            </div>
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                @php
                    $pendingRoleRequests = \App\Models\RoleChangeRequest::where('status', 'pending')->count();
                @endphp
                <a href="{{ route('admin.role-requests.index') }}" 
                   class="flex items-center justify-center gap-2 px-4 py-2.5 bg-gradient-to-r from-violet-500 to-purple-600 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl hover:scale-105 transition-all duration-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                    <span>Review Request Role</span>
                    @if($pendingRoleRequests > 0)
                        <span class="px-2 py-0.5 bg-yellow-400 text-yellow-900 rounded-full text-xs font-bold animate-pulse">
                            {{ $pendingRoleRequests }}
                        </span>
                    @endif
                </a>
                <button @click="$dispatch('open-create-user-modal')"
                   class="flex items-center justify-center gap-2 px-4 py-2.5 bg-gradient-to-r from-blue-500 to-cyan-600 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl hover:scale-105 transition-all duration-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Tambah User Baru</span>
                </button>
            </div>
        </div>
    </div>

    {{-- ========================================
         FLASH MESSAGES
         ======================================== --}}
    @if(session('success'))
        <div class="mb-6 bg-gradient-to-r from-green-50 to-emerald-50 border-l-4 border-green-500 text-green-700 px-6 py-4 rounded-lg shadow-sm">
            <div class="flex items-center gap-3">
                <svg class="h-6 w-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 bg-gradient-to-r from-red-50 to-pink-50 border-l-4 border-red-500 text-red-700 px-6 py-4 rounded-lg shadow-sm">
            <div class="flex items-center gap-3">
                <svg class="h-6 w-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="font-medium">{{ session('error') }}</span>
            </div>
        </div>
    @endif

    {{-- ========================================
         STATS BAR
         ======================================== --}}
    <div class="mb-6">
        <div class="flex items-center gap-2 text-gray-600">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            <span class="text-sm">
                Total: <span class="font-bold text-violet-600">{{ $users->count() }}</span> anggota
            </span>
        </div>
    </div>

    {{-- ========================================
         USER TABLE
         ======================================== --}}
    <x-users.user-table :users="$users" />

    {{-- ========================================
         CREATE USER MODAL
         ======================================== --}}
    @include('admin.users._create-modal')

</div>
@endsection
