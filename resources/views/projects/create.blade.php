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

    <form action="{{ route('projects.store') }}" 
          method="POST" 
          class="space-y-6" 
          x-data="projectForm()"
          @submit="if (submitting) { $event.preventDefault(); return false; } submitting = true;">
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
                            <option value="blackout" class="text-red-600 font-semibold">⚫ Blackout</option>
                        </select>
                        <p class="mt-1 text-xs text-gray-500">
                            <span class="text-red-600 font-semibold">Blackout</span>: Proyek dalam kondisi darurat atau kritis yang memerlukan perhatian khusus
                        </p>
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

                <!-- Label/Tag Proyek -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Label/Tag Proyek (Opsional)
                    </label>
                    <p class="text-xs text-gray-500 mb-3">
                        Pilih label untuk mengkategorikan proyek Anda
                    </p>
                    <x-project-label-selector :selected="old('label')" name="label" />
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
                <div class="space-y-2" x-data="memberFilter()">
                    <div class="flex flex-col gap-3 mb-3">
                        <div class="flex items-center justify-between">
                            <label class="block text-sm font-semibold text-gray-700">
                                Pilih Anggota Tim
                            </label>
                            
                            <!-- Select All Checkbox -->
                            <label class="flex items-center gap-2 cursor-pointer px-4 py-2 bg-gradient-to-r from-violet-50 to-blue-50 hover:from-violet-100 hover:to-blue-100 border border-violet-200 rounded-lg transition"
                                   @click.prevent="toggleAllMembers()">
                                <input type="checkbox" 
                                       id="select-all-members"
                                       class="w-4 h-4 text-violet-600 border-gray-300 rounded focus:ring-violet-500">
                                <span class="text-sm font-semibold text-violet-700">
                                    <svg class="inline h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                    Pilih Semua
                                </span>
                            </label>
                        </div>

                        <!-- Search and Filter Bar -->
                        <div class="flex items-center gap-2">
                            <!-- Search Input -->
                            <div class="flex-1 relative">
                                <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                                <input type="text" 
                                       x-model="searchQuery"
                                       placeholder="Cari nama anggota..."
                                       class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition text-sm">
                            </div>

                            <!-- Role Filter Dropdown -->
                            <div class="relative" x-data="{ open: false }" @click.away="open = false">
                                <button type="button"
                                        @click="open = !open"
                                        class="flex items-center gap-2 px-4 py-2.5 border border-gray-300 rounded-lg hover:bg-gray-50 transition text-sm font-medium text-gray-700 whitespace-nowrap">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                                    </svg>
                                    Filter Role
                                    <span x-show="selectedRoles.length > 0" 
                                          class="px-2 py-0.5 bg-violet-100 text-violet-700 rounded-full text-xs font-semibold"
                                          x-text="selectedRoles.length"></span>
                                </button>

                                <!-- Dropdown Menu -->
                                <div x-show="open"
                                     x-transition:enter="transition ease-out duration-100"
                                     x-transition:enter-start="transform opacity-0 scale-95"
                                     x-transition:enter-end="transform opacity-100 scale-100"
                                     x-transition:leave="transition ease-in duration-75"
                                     x-transition:leave-start="transform opacity-100 scale-100"
                                     x-transition:leave-end="transform opacity-0 scale-95"
                                     class="absolute right-0 mt-2 w-64 bg-white rounded-lg shadow-lg border border-gray-200 z-10 py-2">
                                    
                                    <!-- Clear Filter -->
                                    <div class="px-3 py-2 border-b border-gray-100">
                                        <button type="button"
                                                @click="selectedRoles = []; open = false"
                                                class="text-xs text-violet-600 hover:text-violet-700 font-medium">
                                            Reset Filter
                                        </button>
                                    </div>

                                    <!-- Role Options -->
                                    <div class="max-h-64 overflow-y-auto">
                                        <template x-for="role in availableRoles" :key="role.value">
                                            <label class="flex items-center gap-2 px-3 py-2 hover:bg-gray-50 cursor-pointer transition">
                                                <input type="checkbox" 
                                                       :value="role.value"
                                                       x-model="selectedRoles"
                                                       class="w-4 h-4 text-violet-600 border-gray-300 rounded focus:ring-violet-500">
                                                <span class="flex items-center gap-2 flex-1">
                                                    <span class="px-2 py-0.5 text-xs font-semibold rounded"
                                                          :class="role.color"
                                                          x-text="role.label"></span>
                                                    <span class="text-xs text-gray-500" x-text="'(' + role.count + ')'"></span>
                                                </span>
                                            </label>
                                        </template>
                                    </div>
                                </div>
                            </div>

                            <!-- Selected Counter -->
                            <div class="px-4 py-2.5 bg-gray-100 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 whitespace-nowrap">
                                <span x-text="selectedCount"></span> / <span x-text="visibleCount"></span> dipilih
                            </div>
                        </div>
                    </div>
                    
                    @php
                        // Exclude current user (PM) from list as they are automatically the project owner/member
                        $users = App\Models\User::where('id', '!=', Auth::id())->orderBy('name')->get();
                    @endphp

                    <div class="max-h-96 overflow-y-auto border border-gray-200 rounded-xl divide-y divide-gray-100">
                        @foreach($users as $user)
                        <div class="p-4 hover:bg-gray-50 transition member-row" 
                             x-data="{ selected: false, role: 'member' }"
                             x-show="isVisible({{ json_encode($user->name) }}, {{ json_encode($user->getRoleNames()->toArray()) }})"
                             x-init="$watch('selected', () => $dispatch('member-changed'))"
                             data-user-name="{{ $user->name }}"
                             data-user-roles="{{ json_encode($user->getRoleNames()->toArray()) }}">
                            <div class="flex items-center justify-between">
                                <!-- User Info -->
                                <div class="flex items-center gap-3 flex-1">
                                    <input type="checkbox" 
                                           x-model="selected"
                                           @change="if(!selected) role = 'member'"
                                           name="member_ids[]" 
                                           value="{{ $user->id }}"
                                           class="member-checkbox w-4 h-4 text-violet-600 border-gray-300 rounded focus:ring-violet-500">
                                    
                                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-violet-600 to-blue-600 flex items-center justify-center text-white font-semibold">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    
                                    <div class="flex-1">
                                        <p class="font-medium text-gray-900">{{ $user->name }}</p>
                                        <div class="flex items-center gap-1 mt-1">
                                            @foreach($user->getRoleNames() as $userRole)
                                                @php
                                                    $roleColors = [
                                                        'hr' => 'bg-purple-100 text-purple-700',
                                                        'pm' => 'bg-blue-100 text-blue-700',
                                                        'sekretaris' => 'bg-cyan-100 text-cyan-700',
                                                        'bendahara' => 'bg-green-100 text-green-700',
                                                        'media' => 'bg-pink-100 text-pink-700',
                                                        'pr' => 'bg-orange-100 text-orange-700',
                                                        'bisnis_manager' => 'bg-yellow-100 text-yellow-700',
                                                        'talent_manager' => 'bg-indigo-100 text-indigo-700',
                                                        'researcher' => 'bg-teal-100 text-teal-700',
                                                        'talent' => 'bg-rose-100 text-rose-700',
                                                        'member' => 'bg-gray-100 text-gray-700',
                                                        'guest' => 'bg-gray-100 text-gray-500',
                                                    ];
                                                    $colorClass = $roleColors[$userRole] ?? 'bg-gray-100 text-gray-700';
                                                @endphp
                                                <span class="px-2 py-0.5 text-xs font-semibold rounded {{ $colorClass }}">
                                                    {{ ucfirst($userRole) }}
                                                </span>
                                            @endforeach
                                        </div>
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

                    <!-- Empty State when no results -->
                    <div x-show="visibleCount === 0" 
                         class="p-8 text-center border border-gray-200 rounded-xl bg-gray-50">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <p class="mt-2 text-sm font-medium text-gray-900">Tidak ada anggota ditemukan</p>
                        <p class="mt-1 text-sm text-gray-500">Coba ubah kata kunci pencarian atau filter role</p>
                    </div>

                    <p class="text-xs text-gray-500 mt-2">
                        <strong>💡 Info:</strong> Anda otomatis menjadi anggota proyek ini dengan role Project Manager (Owner) dan memiliki kontrol penuh.
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
                    :disabled="submitting"
                    class="px-8 py-3 bg-gradient-to-r from-violet-600 to-blue-600 text-white font-semibold rounded-xl hover:shadow-lg hover:scale-105 active:scale-95 transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:scale-100">
                <span x-show="!submitting">Buat Proyek</span>
                <span x-show="submitting" class="flex items-center gap-2">
                    <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Menyimpan...
                </span>
            </button>
        </div>
    </form>
</div>

<script>
function projectForm() {
    return {
        submitting: false,
    }
}

// Member Filter Component
function memberFilter() {
    return {
        searchQuery: '',
        selectedRoles: [],
        selectedCount: 0,
        visibleCount: 0,
        availableRoles: [
            { value: 'hr', label: 'HR', color: 'bg-purple-100 text-purple-700', count: 0 },
            { value: 'pm', label: 'PM', color: 'bg-blue-100 text-blue-700', count: 0 },
            { value: 'sekretaris', label: 'Sekretaris', color: 'bg-cyan-100 text-cyan-700', count: 0 },
            { value: 'bendahara', label: 'Bendahara', color: 'bg-green-100 text-green-700', count: 0 },
            { value: 'media', label: 'Media', color: 'bg-pink-100 text-pink-700', count: 0 },
            { value: 'pr', label: 'PR', color: 'bg-orange-100 text-orange-700', count: 0 },
            { value: 'bisnis_manager', label: 'Bisnis Manager', color: 'bg-yellow-100 text-yellow-700', count: 0 },
            { value: 'talent_manager', label: 'Talent Manager', color: 'bg-indigo-100 text-indigo-700', count: 0 },
            { value: 'researcher', label: 'Researcher', color: 'bg-teal-100 text-teal-700', count: 0 },
            { value: 'talent', label: 'Talent', color: 'bg-rose-100 text-rose-700', count: 0 },
            { value: 'member', label: 'Member', color: 'bg-gray-100 text-gray-700', count: 0 },
            { value: 'guest', label: 'Guest', color: 'bg-gray-100 text-gray-500', count: 0 },
        ],

        init() {
            // Count users per role
            this.countRoles();
            // Update counts initially
            this.updateCounts();
            // Listen for member changes
            window.addEventListener('member-changed', () => this.updateCounts());
            // Watch for search and filter changes
            this.$watch('searchQuery', () => this.updateCounts());
            this.$watch('selectedRoles', () => this.updateCounts());
        },

        countRoles() {
            const rows = document.querySelectorAll('.member-row');
            rows.forEach(row => {
                const roles = JSON.parse(row.dataset.userRoles || '[]');
                roles.forEach(role => {
                    const roleObj = this.availableRoles.find(r => r.value === role);
                    if (roleObj) roleObj.count++;
                });
            });
        },

        isVisible(userName, userRoles) {
            // Search filter
            const matchesSearch = !this.searchQuery || 
                                userName.toLowerCase().includes(this.searchQuery.toLowerCase());
            
            // Role filter
            const matchesRole = this.selectedRoles.length === 0 || 
                               userRoles.some(role => this.selectedRoles.includes(role));
            
            return matchesSearch && matchesRole;
        },

        updateCounts() {
            // Count visible members
            const rows = document.querySelectorAll('.member-row');
            let visible = 0;
            let selected = 0;
            
            rows.forEach(row => {
                const userName = row.dataset.userName;
                const userRoles = JSON.parse(row.dataset.userRoles || '[]');
                const isVisible = this.isVisible(userName, userRoles);
                
                if (isVisible) {
                    visible++;
                    const checkbox = row.querySelector('.member-checkbox');
                    if (checkbox && checkbox.checked) {
                        selected++;
                    }
                }
            });
            
            this.visibleCount = visible;
            this.selectedCount = selected;
        }
    }
}

// Select All Members functionality
function toggleAllMembers() {
    const selectAllCheckbox = document.getElementById('select-all-members');
    const memberCheckboxes = document.querySelectorAll('.member-checkbox');
    
    selectAllCheckbox.checked = !selectAllCheckbox.checked;
    const isChecked = selectAllCheckbox.checked;
    
    // Only toggle visible checkboxes
    memberCheckboxes.forEach(checkbox => {
        const row = checkbox.closest('.member-row');
        const isVisible = row && window.getComputedStyle(row).display !== 'none';
        
        if (isVisible && checkbox.checked !== isChecked) {
            checkbox.click(); // Trigger click to maintain Alpine.js state
        }
    });
}

// Update select-all state when individual checkboxes change
document.addEventListener('alpine:initialized', () => {
    setTimeout(() => {
        const selectAllCheckbox = document.getElementById('select-all-members');
        if (selectAllCheckbox) {
            const memberCheckboxes = document.querySelectorAll('.member-checkbox');
            
            memberCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', () => {
                    // Only count visible checkboxes
                    const visibleCheckboxes = Array.from(memberCheckboxes).filter(cb => {
                        const row = cb.closest('.member-row');
                        return row && window.getComputedStyle(row).display !== 'none';
                    });
                    
                    const allChecked = visibleCheckboxes.every(cb => cb.checked);
                    const someChecked = visibleCheckboxes.some(cb => cb.checked);
                    
                    selectAllCheckbox.checked = allChecked;
                    selectAllCheckbox.indeterminate = someChecked && !allChecked;
                });
            });
        }
    }, 100);
});
</script>
@endsection
