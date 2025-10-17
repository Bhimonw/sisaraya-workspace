@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto">
    
    <h1 class="text-3xl font-bold text-gray-900 mb-2">Request Perubahan Role</h1>
    <p class="text-gray-600 mb-6">Ajukan permohonan perubahan role Anda kepada HR</p>

    {{-- Flash Messages --}}
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

    {{-- Pending Request Warning --}}
    @if($pendingRequest)
        <div class="mb-6 bg-yellow-50 border-l-4 border-yellow-500 p-6 rounded-lg">
            <div class="flex gap-3">
                <svg class="h-6 w-6 text-yellow-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <div>
                    <h3 class="font-bold text-yellow-800 mb-1">Request Pending</h3>
                    <p class="text-sm text-yellow-700">Anda sudah memiliki request yang belum diproses. Silakan tunggu hingga HR merespon request Anda.</p>
                    <a href="{{ route('role-requests.my-requests') }}" class="text-sm text-yellow-800 font-semibold underline mt-2 inline-block">
                        Lihat Request Saya →
                    </a>
                </div>
            </div>
        </div>
    @endif

    {{-- Current Roles --}}
    <div class="bg-white rounded-xl shadow-md border border-gray-200 p-6 mb-6">
        <h2 class="text-lg font-bold text-gray-900 mb-4">Role Anda Saat Ini</h2>
        <div class="flex flex-wrap gap-2">
            @forelse($user->roles as $role)
                <x-users.role-badge :role="$role->name" />
            @empty
                <span class="text-gray-500">Belum memiliki role</span>
            @endforelse
        </div>
    </div>

    {{-- Request Form --}}
    <div class="bg-white rounded-xl shadow-md border border-gray-200 p-6">
        <h2 class="text-lg font-bold text-gray-900 mb-4">Ajukan Role Baru</h2>
        
        <form method="POST" action="{{ route('role-requests.store') }}" x-data="{ selectedRoles: [] }">
            @csrf

            <div class="space-y-3 mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-3">
                    Pilih Role yang Diinginkan (bisa lebih dari satu)
                </label>
                
                @foreach($roles as $role)
                <label class="flex items-center p-4 border-2 rounded-lg cursor-pointer transition-all hover:border-violet-300 hover:bg-gray-50">
                    <input type="checkbox" 
                           name="requested_roles[]" 
                           value="{{ $role->name }}"
                           {{ $pendingRequest ? 'disabled' : '' }}
                           x-model="selectedRoles"
                           class="h-5 w-5 text-violet-600 border-gray-300 rounded focus:ring-violet-500">
                    <div class="ml-4 flex-1">
                        <div class="flex items-center gap-2">
                            <span class="font-semibold text-gray-900">{{ ucfirst($role->name) }}</span>
                            <x-users.role-badge :role="$role->name" />
                        </div>
                    </div>
                </label>
                @endforeach
            </div>

            @error('requested_roles')
                <p class="text-red-600 text-sm mb-4">{{ $message }}</p>
            @enderror

            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Alasan Request <span class="text-red-500">*</span>
                </label>
                <textarea 
                    name="reason" 
                    rows="5"
                    {{ $pendingRequest ? 'disabled' : '' }}
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-violet-500 focus:border-violet-500"
                    placeholder="Jelaskan mengapa Anda memerlukan perubahan role ini (minimal 10 karakter)"
                    required>{{ old('reason') }}</textarea>
                @error('reason')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center gap-3">
                <button type="submit" 
                        {{ $pendingRequest ? 'disabled' : '' }}
                        class="px-6 py-3 bg-gradient-to-r from-violet-600 to-blue-600 text-white font-semibold rounded-xl hover:shadow-lg hover:scale-105 active:scale-95 transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed">
                    <span class="flex items-center gap-2">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                        Kirim Request
                    </span>
                </button>
                
                <a href="{{ route('role-requests.my-requests') }}" 
                   class="px-6 py-3 border-2 border-gray-300 text-gray-700 font-medium rounded-xl hover:bg-gray-50 transition">
                    Lihat Request Saya
                </a>
            </div>
        </form>
    </div>

    {{-- Info Box --}}
    <div class="mt-6 bg-blue-50 border-l-4 border-blue-500 p-4 rounded-lg">
        <div class="flex gap-3">
            <svg class="h-6 w-6 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div class="text-sm text-blue-700">
                <p class="font-semibold mb-1">Informasi:</p>
                <ul class="list-disc list-inside space-y-1">
                    <li>Request akan direview oleh HR</li>
                    <li>Jelaskan alasan yang jelas dan spesifik</li>
                    <li>Anda hanya bisa mengajukan satu request dalam satu waktu</li>
                    <li>Proses approval membutuhkan waktu 1-3 hari kerja</li>
                </ul>
            </div>
        </div>
    </div>

</div>
@endsection
