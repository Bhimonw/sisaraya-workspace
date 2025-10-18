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
    <x-users.page-header 
        title="Manajemen Anggota"
        description="Kelola akun pengguna Sisaraya"
        :actionUrl="route('admin.users.create')"
        actionText="Tambah User Baru"
    />

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

</div>
@endsection
