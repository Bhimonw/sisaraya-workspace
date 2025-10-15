@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="mb-8 flex items-center gap-4">
        <a href="{{ route('projects.index') }}" class="p-2 hover:bg-gray-100 rounded-lg transition">
            <svg class="h-6 w-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
        </a>
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Buat Proyek Baru</h1>
            <p class="text-gray-600 mt-1">Isi informasi proyek dan tambahkan anggota tim</p>
        </div>
    </div>

    <form action="{{ route('projects.store') }}" method="POST" class="space-y-6" x-data="projectForm()">
        @csrf

        <!-- Informasi Proyek -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="bg-gradient-to-r from-violet-600 to-blue-600 px-6 py-4">
                <h2 class="text-lg font-semibold text-white">Informasi Proyek</h2>
            </div>
            
            <div class="p-6 space-y-4">
                <!-- Nama Proyek -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Nama Proyek <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="name" 
                           required
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition"
                           placeholder="Contoh: Festival Musik SISARAYA 2025">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Deskripsi -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Deskripsi Proyek
                    </label>
                    <textarea name="description" 
                              rows="4"
                              class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition"
                              placeholder="Jelaskan tujuan dan detail proyek ini..."></textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status & Visibility -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Status -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Status <span class="text-red-500">*</span>
                        </label>
                        <select name="status" 
                                required
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition">
                            <option value="planning">Perencanaan</option>
                            <option value="active">Aktif</option>
                            <option value="on_hold">Ditunda</option>
                            <option value="completed">Selesai</option>
                        </select>
                    </div>

                    <!-- Visibility -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Visibilitas
                        </label>
                        <div class="flex items-center h-12 px-4 border border-gray-300 rounded-xl">
                            <input type="checkbox" 
                                   name="is_public" 
                                   value="1" 
                                   checked
                                   class="w-4 h-4 text-violet-600 border-gray-300 rounded focus:ring-violet-500">
                            <label class="ml-2 text-sm text-gray-700">Proyek Publik (dapat dilihat semua)</label>
                        </div>
                    </div>
                </div>

                <!-- Rentang Waktu (Optional) -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Rentang Waktu Proyek (Opsional)
                    </label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Start Date -->
                        <div>
                            <label class="block text-xs text-gray-600 mb-1">Tanggal Mulai</label>
                            <input type="date" 
                                   name="start_date"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition">
                            @error('start_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- End Date -->
                        <div>
                            <label class="block text-xs text-gray-600 mb-1">Tanggal Selesai</label>
                            <input type="date" 
                                   name="end_date"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition">
                            @error('end_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">
                        Kosongkan jika proyek tidak memiliki batas waktu tertentu
                    </p>
                </div>
            </div>
        </div>

        <!-- Tim Proyek -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="bg-gradient-to-r from-blue-600 to-emerald-600 px-6 py-4">
                <h2 class="text-lg font-semibold text-white">Tim Proyek</h2>
                <p class="text-sm text-white/90 mt-1">Pilih anggota dan tentukan role mereka</p>
            </div>
            
            <div class="p-6 space-y-6">
                <!-- Info Box -->
                <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded">
                    <div class="flex items-start">
                        <svg class="h-5 w-5 text-blue-500 mt-0.5 mr-2 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div class="flex-1 text-sm text-blue-800">
                            <p class="font-medium mb-1">Perbedaan Role:</p>
                            <ul class="list-disc list-inside space-y-1">
                                <li><strong>Admin Project:</strong> Dapat membuat tiket dan event</li>
                                <li><strong>Member:</strong> Dapat melihat dan mengambil tiket</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- User List -->
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-3">
                        Pilih Anggota Tim
                    </label>
                    
                    @php
                        $users = App\Models\User::where('id', '!=', Auth::id())->orderBy('name')->get();
                    @endphp

                    <div class="max-h-96 overflow-y-auto border border-gray-200 rounded-xl divide-y divide-gray-100">
                        @foreach($users as $user)
                        <div class="p-4 hover:bg-gray-50 transition" 
                             x-data="{ selected: false, role: 'member' }">
                            <div class="flex items-center justify-between">
                                <!-- User Info -->
                                <div class="flex items-center gap-3 flex-1">
                                    <input type="checkbox" 
                                           x-model="selected"
                                           @change="if(!selected) role = 'member'"
                                           name="member_ids[]" 
                                           value="{{ $user->id }}"
                                           class="w-4 h-4 text-violet-600 border-gray-300 rounded focus:ring-violet-500">
                                    
                                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-violet-600 to-blue-600 flex items-center justify-center text-white font-semibold">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    
                                    <div class="flex-1">
                                        <p class="font-medium text-gray-900">{{ $user->name }}</p>
                                        <p class="text-sm text-gray-500">{{ $user->email }}</p>
                                    </div>
                                </div>

                                <!-- Role Selection -->
                                <div x-show="selected" 
                                     x-transition
                                     class="flex items-center gap-2">
                                    <label class="flex items-center gap-2 cursor-pointer px-3 py-1.5 rounded-lg border border-gray-300 hover:bg-gray-50 transition"
                                           :class="role === 'member' ? 'bg-gray-100 border-gray-400' : ''">
                                        <input type="radio" 
                                               x-model="role"
                                               :name="'role_' + {{ $user->id }}"
                                               value="member"
                                               class="w-4 h-4 text-gray-600">
                                        <span class="text-sm font-medium text-gray-700">Member</span>
                                    </label>
                                    
                                    <label class="flex items-center gap-2 cursor-pointer px-3 py-1.5 rounded-lg border border-emerald-300 hover:bg-emerald-50 transition"
                                           :class="role === 'admin' ? 'bg-emerald-100 border-emerald-500' : ''">
                                        <input type="radio" 
                                               x-model="role"
                                               :name="'role_' + {{ $user->id }}"
                                               value="admin"
                                               class="w-4 h-4 text-emerald-600">
                                        <span class="text-sm font-medium text-emerald-700">Admin</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <p class="text-xs text-gray-500 mt-2">
                        Anda otomatis menjadi Project Manager dengan kontrol penuh atas proyek ini
                    </p>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center justify-between gap-4">
            <a href="{{ route('projects.index') }}" 
               class="px-6 py-3 border border-gray-300 text-gray-700 font-medium rounded-xl hover:bg-gray-50 transition">
                Batal
            </a>
            
            <button type="submit" 
                    class="px-8 py-3 bg-gradient-to-r from-violet-600 to-blue-600 text-white font-semibold rounded-xl hover:shadow-lg hover:scale-105 active:scale-95 transition-all duration-300">
                <span>Buat Proyek</span>
            </button>
        </div>
    </form>
</div>

<script>
function projectForm() {
    return {
        // Add any additional Alpine.js logic here if needed
    }
}
</script>
@endsection
