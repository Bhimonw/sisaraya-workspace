@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto" x-data="{ 
    selectedRoles: {{ json_encode(old('roles', $user->roles->pluck('name')->toArray())) }},
    get isGuestSelected() {
        return this.selectedRoles.includes('guest');
    },
    get hasOtherRoles() {
        return this.selectedRoles.some(role => role !== 'guest');
    },
    toggleRole(roleName) {
        if (this.selectedRoles.includes(roleName)) {
            this.selectedRoles = this.selectedRoles.filter(r => r !== roleName);
        } else {
            // If selecting guest, clear other roles
            if (roleName === 'guest') {
                this.selectedRoles = ['guest'];
            } 
            // If selecting other role and guest is selected, remove guest
            else if (this.selectedRoles.includes('guest')) {
                this.selectedRoles = [roleName];
            } 
            // Normal addition
            else {
                this.selectedRoles.push(roleName);
            }
        }
    }
}">
    <div class="mb-6">
        <x-back-button :url="route('admin.users.index')" />
    </div>

    <div class="mb-6">
        <div class="flex items-center gap-3">
            <svg class="h-8 w-8 text-indigo-600 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
            </svg>
            <h1 class="text-3xl font-bold text-gray-900">Edit User</h1>
        </div>
        <p class="text-gray-600 mt-2">Update informasi pengguna {{ $user->name }}</p>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-6">
        <form method="POST" action="{{ route('admin.users.update', $user) }}">
            @csrf
            @method('PUT')

            <!-- Name -->
            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                    Nama Lengkap <span class="text-red-500">*</span>
                </label>
                <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Username -->
            <div class="mb-4">
                <label for="username" class="block text-sm font-medium text-gray-700 mb-2">
                    Username <span class="text-red-500">*</span>
                </label>
                <div class="flex">
                    <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">
                        @
                    </span>
                    <input type="text" name="username" id="username" value="{{ old('username', $user->username) }}" required
                        class="flex-1 px-3 py-2 border border-gray-300 rounded-r-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('username') border-red-500 @enderror">
                </div>
                @error('username')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email -->
            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                    Email <span class="text-gray-400">(Opsional)</span>
                </label>
                <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('email') border-red-500 @enderror">
                @error('email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password (Optional) -->
            <div class="mb-4">
                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                    Password Baru <span class="text-gray-400">(Kosongkan jika tidak ingin mengubah)</span>
                </label>
                <input type="password" name="password" id="password"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('password') border-red-500 @enderror">
                <p class="mt-1 text-xs text-gray-500">Minimal 8 karakter</p>
                @error('password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password Confirmation -->
            <div class="mb-6">
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                    Konfirmasi Password Baru
                </label>
                <input type="password" name="password_confirmation" id="password_confirmation"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <!-- Roles -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-3">
                    Role(s) <span class="text-gray-400">(Pilih satu atau lebih)</span>
                </label>
                <div class="mb-3 p-3 bg-amber-50 border border-amber-200 rounded-md">
                    <div class="flex items-start gap-2">
                        <svg class="h-5 w-5 text-amber-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-sm text-amber-800">
                            <strong>Penting:</strong> Role <strong>Guest</strong> tidak dapat digabung dengan role lainnya. Guest adalah role khusus dengan akses terbatas hanya ke proyek tertentu.
                        </p>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    @php
                        $userRoles = $user->roles->pluck('name')->toArray();
                    @endphp
                    @foreach($roles as $role)
                        <label 
                            class="flex items-center p-3 border border-gray-300 rounded-md cursor-pointer transition-all"
                            :class="{
                                'bg-gray-50 hover:bg-gray-100': selectedRoles.includes('{{ $role->name }}'),
                                'hover:bg-gray-50': !selectedRoles.includes('{{ $role->name }}'),
                                'opacity-50 cursor-not-allowed': (isGuestSelected && '{{ $role->name }}' !== 'guest') || (hasOtherRoles && '{{ $role->name }}' === 'guest')
                            }"
                        >
                            <input 
                                type="checkbox" 
                                name="roles[]" 
                                value="{{ $role->name }}" 
                                :checked="selectedRoles.includes('{{ $role->name }}')"
                                :disabled="(isGuestSelected && '{{ $role->name }}' !== 'guest') || (hasOtherRoles && '{{ $role->name }}' === 'guest')"
                                @change="toggleRole('{{ $role->name }}')"
                                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                            <span class="ml-2 text-sm text-gray-700 capitalize flex-1">
                                {{ $role->name }}
                                @if($role->name === 'guest')
                                    <span class="text-xs text-amber-600 block font-normal">Tidak bisa dicampur dengan role lain</span>
                                @endif
                            </span>
                        </label>
                    @endforeach
                </div>
                @error('roles')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Projects Selection (shown when guest role is selected) -->
            <div x-show="isGuestSelected" 
                 x-transition
                 class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-3">
                    Proyek yang Diikuti <span class="text-red-500">*</span>
                    <span class="text-xs text-gray-500 font-normal">(Wajib untuk role Guest - hanya proyek aktif)</span>
                </label>
                <div class="border border-gray-300 rounded-md p-4 max-h-64 overflow-y-auto space-y-2">
                    @forelse($projects as $project)
                        <label class="flex items-start p-2 hover:bg-gray-50 rounded cursor-pointer">
                            <input type="checkbox" name="projects[]" value="{{ $project->id }}" 
                                {{ in_array($project->id, old('projects', $userProjects)) ? 'checked' : '' }}
                                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded mt-0.5">
                            <div class="ml-3 flex-1">
                                <div class="flex items-center gap-2">
                                    <span class="text-sm font-medium text-gray-900">{{ $project->name }}</span>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                        Aktif
                                    </span>
                                </div>
                                <p class="text-xs text-gray-500">{{ Str::limit($project->description, 60) }}</p>
                            </div>
                        </label>
                    @empty
                        <div class="text-center py-6">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                            </svg>
                            <p class="mt-2 text-sm text-gray-500">Belum ada proyek aktif tersedia</p>
                            <p class="text-xs text-gray-400 mt-1">Buat proyek baru dengan status "Aktif" terlebih dahulu</p>
                        </div>
                    @endforelse
                </div>
                <p class="mt-2 text-xs text-gray-600">
                    <svg class="inline h-4 w-4 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    User dengan role Guest hanya bisa melihat dan berpartisipasi dalam proyek yang dipilih
                </p>
                @error('projects')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Buttons -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
                <a href="{{ route('admin.users.index') }}" 
                    class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                    Batal
                </a>
                <button type="submit" 
                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    Update User
                </button>
            </div>
        </form>
    </div>
</div>
@endsection