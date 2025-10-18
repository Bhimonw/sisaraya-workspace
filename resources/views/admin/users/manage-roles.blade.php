@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto">
    
    {{-- Header --}}
    <div class="mb-6">
        <div class="mb-4">
            <x-back-button :url="route('admin.users.index')" />
        </div>
        
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Kelola Role User</h1>
        <p class="text-gray-600">Atur role dan permission untuk {{ $user->name }}</p>
    </div>

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

    {{-- User Info Card --}}
    <div class="bg-white rounded-xl shadow-md border border-gray-200 p-6 mb-6">
        <div class="flex items-center gap-4">
            <div class="h-16 w-16 rounded-full bg-gradient-to-br from-violet-500 to-blue-600 flex items-center justify-center text-white font-bold text-xl shadow-lg">
                {{ strtoupper(substr($user->name, 0, 2)) }}
            </div>
            <div>
                <h3 class="text-xl font-bold text-gray-900">{{ $user->name }}</h3>
                <p class="text-gray-600 flex items-center gap-1">
                    <span class="text-gray-400">@</span>
                    <span class="font-medium">{{ $user->username }}</span>
                </p>
                @if($user->email)
                <p class="text-sm text-gray-500 mt-1">{{ $user->email }}</p>
                @endif
            </div>
        </div>
    </div>

    {{-- Role Management Form --}}
    <div class="bg-white rounded-xl shadow-md border border-gray-200 p-6">
        <h2 class="text-lg font-bold text-gray-900 mb-4">Pilih Role</h2>
        
        <form method="POST" action="{{ route('admin.users.update-roles', $user) }}">
            @csrf
            @method('PUT')

            <div class="space-y-3 mb-6">
                @foreach($roles as $role)
                <label class="flex items-center p-4 border-2 rounded-lg cursor-pointer transition-all
                              {{ $user->hasRole($role->name) ? 'border-violet-500 bg-violet-50' : 'border-gray-200 hover:border-violet-300 hover:bg-gray-50' }}">
                    <input type="checkbox" 
                           name="roles[]" 
                           value="{{ $role->name }}"
                           {{ $user->hasRole($role->name) ? 'checked' : '' }}
                           class="h-5 w-5 text-violet-600 border-gray-300 rounded focus:ring-violet-500">
                    <div class="ml-4 flex-1">
                        <div class="flex items-center gap-2">
                            <span class="font-semibold text-gray-900">{{ ucfirst($role->name) }}</span>
                            <x-users.role-badge :role="$role->name" />
                        </div>
                        @if($role->name === 'pm')
                            <p class="text-sm text-gray-600 mt-1">Project Manager - Mengelola proyek dan tim</p>
                        @elseif($role->name === 'hr')
                            <p class="text-sm text-gray-600 mt-1">Human Resources - Mengelola anggota dan role</p>
                        @elseif($role->name === 'bendahara')
                            <p class="text-sm text-gray-600 mt-1">Finance - Mengelola RAB dan keuangan</p>
                        @elseif($role->name === 'sekretaris')
                            <p class="text-sm text-gray-600 mt-1">Secretary - Mengelola dokumen dan administrasi</p>
                        @elseif($role->name === 'kewirausahaan')
                            <p class="text-sm text-gray-600 mt-1">Entrepreneurship - Mengelola bisnis dan usaha</p>
                        @elseif($role->name === 'member')
                            <p class="text-sm text-gray-600 mt-1">Member - Anggota umum organisasi</p>
                        @endif
                    </div>
                </label>
                @endforeach
            </div>

            @error('roles')
                <p class="text-red-600 text-sm mb-4">{{ $message }}</p>
            @enderror

            <div class="flex items-center gap-3">
                <button type="submit" 
                        class="px-6 py-3 bg-gradient-to-r from-violet-600 to-blue-600 text-white font-semibold rounded-xl hover:shadow-lg hover:scale-105 active:scale-95 transition-all duration-300">
                    <span class="flex items-center gap-2">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Simpan Perubahan Role
                    </span>
                </button>
                
                <a href="{{ route('admin.users.index') }}" 
                   class="px-6 py-3 border-2 border-gray-300 text-gray-700 font-medium rounded-xl hover:bg-gray-50 transition">
                    Batal
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
                <p class="font-semibold mb-1">Informasi Penting:</p>
                <ul class="list-disc list-inside space-y-1">
                    <li>Satu user dapat memiliki lebih dari satu role</li>
                    <li>Role akan langsung aktif setelah disimpan</li>
                    <li>HR hanya dapat mengubah role, tidak dapat mengedit data user atau menghapus akun</li>
                </ul>
            </div>
        </div>
    </div>

</div>
@endsection
