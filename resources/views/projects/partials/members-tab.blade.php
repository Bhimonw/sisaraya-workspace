        {{-- MEMBERS TAB (Visible to All, but only PM can manage) --}}
        <div x-show="activeTab === 'members'" x-transition>
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-2 border-violet-100" x-data="memberManagement()">
        <div class="bg-gradient-to-r from-violet-600 to-blue-600 px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <div>
                        <h2 class="text-lg font-semibold text-white">Anggota Tim Proyek</h2>
                        <p class="text-sm text-white/90">Daftar anggota dan role mereka</p>
                    </div>
                </div>
                
                {{-- Action Buttons --}}
                <div class="flex items-center gap-3">
                    @if($project->canManageMembers(Auth::user()))
                    <button @click="showManageMembers = !showManageMembers" 
                            class="px-4 py-2 bg-white text-violet-600 font-semibold rounded-lg hover:bg-violet-50 hover:scale-105 active:scale-95 transition-all duration-300 shadow-lg">
                        <span x-show="!showManageMembers" class="flex items-center gap-2">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            Kelola Member
                        </span>
                        <span x-show="showManageMembers" class="flex items-center gap-2">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            Tutup
                        </span>
                    </button>
                    
                    <button @click="showAddMember = !showAddMember" 
                            x-show="showManageMembers"
                            class="px-4 py-2 bg-emerald-600 text-white font-semibold rounded-lg hover:bg-emerald-700 hover:scale-105 active:scale-95 transition-all duration-300 shadow-lg">
                        <span x-show="!showAddMember" class="flex items-center gap-2">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Tambah Member
                        </span>
                        <span x-show="showAddMember" class="flex items-center gap-2">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            Tutup
                        </span>
                    </button>
                    @endif
                </div>
            </div>
        </div>

        <div class="p-6">
            @if($project->canManageMembers(Auth::user()))
            {{-- Add Member Form (PM/HR/Admin Only) --}}
            <div x-show="showAddMember && showManageMembers" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform -translate-y-2"
                 x-transition:enter-end="opacity-100 transform translate-y-0"
                 class="mb-6 p-6 bg-gradient-to-br from-violet-50 to-blue-50 rounded-xl border-2 border-violet-200 shadow-sm">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="h-5 w-5 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                    </svg>
                    Tambah Member Baru
                </h3>
                
                <form action="{{ route('projects.members.store', $project) }}" method="POST" class="space-y-4">
                    @csrf
                    
                    {{-- Info Box --}}
                    <div class="p-4 bg-blue-100 border-l-4 border-blue-500 rounded">
                        <div class="flex items-start gap-2">
                            <svg class="h-5 w-5 text-blue-600 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div class="flex-1 text-sm text-blue-900">
                                <p class="font-semibold mb-2">Tentang Role:</p>
                                <ul class="space-y-1 list-disc list-inside">
                                    <li><strong>Admin Project:</strong> Dapat membuat tiket dan event</li>
                                    <li><strong>Member:</strong> Dapat melihat dan mengambil tiket</li>
                                    <li><strong>Event Role:</strong> Bisa pilih role permanent atau event role untuk setiap member</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    @php
                        $availableUsers = \App\Models\User::where('id', '!=', $project->owner_id)
                            ->whereNotIn('id', $project->members->pluck('id'))
                            ->orderBy('name')
                            ->get();
                    @endphp

                    {{-- User List with Search, Filter, and Role Selection --}}
                    <div class="space-y-3" x-data="addMemberFilter()">
                        <div class="flex flex-col gap-3 mb-3">
                            <div class="flex items-center justify-between">
                                <label class="block text-sm font-semibold text-gray-700">
                                    Pilih User & Atur Role <span class="text-red-500">*</span>
                                </label>
                                
                                <!-- Select All Checkbox -->
                                <label class="flex items-center gap-2 cursor-pointer px-4 py-2 bg-gradient-to-r from-violet-50 to-blue-50 hover:from-violet-100 hover:to-blue-100 border border-violet-200 rounded-lg transition"
                                       @click.prevent="toggleAllMembers()">
                                    <input type="checkbox" 
                                           id="select-all-add-members"
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

                        @if($availableUsers->count() > 0)
                        <div class="max-h-96 overflow-y-auto border-2 border-gray-200 rounded-xl divide-y divide-gray-100">
                            @foreach($availableUsers as $user)
                            <div class="p-4 hover:bg-gray-50 transition add-member-row" 
                                 x-data="{ selected: false, projectRole: 'member', eventRole: '' }"
                                 x-show="isVisible({{ json_encode($user->name) }}, {{ json_encode($user->getRoleNames()->toArray()) }})"
                                 x-init="$watch('selected', () => $dispatch('add-member-changed'))"
                                 data-user-name="{{ $user->name }}"
                                 data-user-roles="{{ json_encode($user->getRoleNames()->toArray()) }}">
                                <div class="flex items-center gap-4">
                                    {{-- Checkbox --}}
                                    <div class="flex items-center">
                                        <input type="checkbox" 
                                               x-model="selected"
                                               @change="if(!selected) { projectRole = 'member'; eventRole = ''; }"
                                               name="user_ids[]" 
                                               value="{{ $user->id }}"
                                               class="add-member-checkbox w-5 h-5 text-violet-600 border-gray-300 rounded focus:ring-violet-500">
                                    </div>

                                    {{-- Avatar & Name --}}
                                    <div class="flex items-center gap-3 flex-1">
                                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-violet-600 to-blue-600 flex items-center justify-center text-white font-semibold">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                        <div class="flex-1">
                                            <p class="font-semibold text-gray-900">{{ $user->name }}</p>
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
                                                    @endphp
                                                    <span class="px-2 py-0.5 text-xs font-semibold rounded {{ $roleColors[$userRole] ?? 'bg-gray-100 text-gray-700' }}">
                                                        {{ ucfirst($userRole) }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Role Selection (shown when checked) --}}
                                    <div x-show="selected" 
                                         x-transition
                                         class="flex items-center gap-2 min-w-[400px]">
                                        {{-- Project Role --}}
                                        <div class="flex-1">
                                            <select x-model="projectRole"
                                                    :name="'project_role_' + {{ $user->id }}"
                                                    class="w-full text-sm border border-gray-300 p-2 rounded-lg focus:ring-2 focus:ring-violet-500">
                                                <option value="member">Member</option>
                                                <option value="admin">Admin</option>
                                            </select>
                                        </div>

                                        {{-- Event Role --}}
                                        <div class="flex-1">
                                            <select x-model="eventRole"
                                                    :name="'event_role_' + {{ $user->id }}"
                                                    class="w-full text-sm border border-gray-300 p-2 rounded-lg focus:ring-2 focus:ring-violet-500">
                                                <option value="">-- Pilih Event Role --</option>
                                                {{-- Only Event Roles, NOT Permanent Roles --}}
                                                <optgroup label="Role Event (Temporary)">
                                                    @foreach(\App\Models\Ticket::getEventRoles() as $key => $label)
                                                        <option value="{{ $key }}">{{ $label }}</option>
                                                    @endforeach
                                                </optgroup>
                                            </select>
                                            <p class="text-[10px] text-gray-500 mt-1">
                                                â„¹ï¸ Role permanent (HR, PM, dll) tidak dapat diatur di project
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <div class="p-6 text-center text-gray-500 border-2 border-dashed border-gray-300 rounded-xl">
                            Semua user sudah menjadi member
                        </div>
                        @endif
                    </div>
                    
                    <div class="flex items-center gap-3 pt-2">
                        <button type="submit" 
                                class="px-6 py-3 bg-gradient-to-r from-violet-600 to-blue-600 text-white font-semibold rounded-xl hover:shadow-lg hover:scale-105 active:scale-95 transition-all duration-300">
                            <span class="flex items-center gap-2">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                Tambahkan Member
                            </span>
                        </button>
                        <button type="button" 
                                @click="showAddMember = false"
                                class="px-6 py-3 border-2 border-gray-300 text-gray-700 font-medium rounded-xl hover:bg-gray-50 transition">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
            @endif
            {{-- End Add Member Form (PM/HR/Admin Only) --}}

            {{-- Bulk Actions Bar --}}
            @if($project->canManageMembers(Auth::user()))
            <div x-show="showManageMembers && selectedMembers.length > 0" 
                 x-transition
                 class="mb-4 p-4 bg-violet-50 border-2 border-violet-200 rounded-xl">
                <div class="flex items-center justify-between flex-wrap gap-3">
                    <div class="flex items-center gap-3">
                        <span class="text-sm font-semibold text-gray-700">
                            <span x-text="selectedMembers.length"></span> member dipilih
                        </span>
                        <button @click="selectedMembers = []" 
                                class="text-xs text-violet-600 hover:text-violet-700 font-medium">
                            Batal Pilihan
                        </button>
                    </div>
                    
                    <div class="flex items-center gap-2">
                        <!-- Bulk Change to Admin -->
                        <button @click="bulkChangeRole('admin')" 
                                class="px-4 py-2 bg-emerald-600 text-white text-sm font-semibold rounded-lg hover:bg-emerald-700 transition flex items-center gap-2">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Jadikan Admin
                        </button>
                        
                        <!-- Bulk Change to Member -->
                        <button @click="bulkChangeRole('member')" 
                                class="px-4 py-2 bg-gray-600 text-white text-sm font-semibold rounded-lg hover:bg-gray-700 transition flex items-center gap-2">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            Jadikan Member
                        </button>
                        
                        <!-- Bulk Delete -->
                        <button @click="bulkDeleteMembers()" 
                                class="px-4 py-2 bg-red-600 text-white text-sm font-semibold rounded-lg hover:bg-red-700 transition flex items-center gap-2">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Hapus Terpilih
                        </button>
                    </div>
                </div>
            </div>
            @endif

            {{-- Search and Filter Bar (Visible when managing members) --}}
            @if($project->canManageMembers(Auth::user()))
            <div x-show="showManageMembers" 
                 x-transition
                 class="mb-4">
                <div class="flex items-center gap-2">
                    <!-- Search Input -->
                    <div class="flex-1 relative">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input type="text" 
                               x-model="searchQuery"
                               placeholder="Cari nama member..."
                               class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition text-sm">
                    </div>

                    <!-- Project Role Filter -->
                    <div class="relative" x-data="{ open: false }" @click.away="open = false">
                        <button type="button"
                                @click="open = !open"
                                class="flex items-center gap-2 px-4 py-2.5 border border-gray-300 rounded-lg hover:bg-gray-50 transition text-sm font-medium text-gray-700 whitespace-nowrap">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                            </svg>
                            Filter Role
                            <span x-show="projectRoleFilter !== 'all'" 
                                  class="px-2 py-0.5 bg-violet-100 text-violet-700 rounded-full text-xs font-semibold">
                                1
                            </span>
                        </button>

                        <!-- Dropdown Menu -->
                        <div x-show="open"
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-lg border border-gray-200 z-10 py-2">
                            
                            <label class="flex items-center gap-2 px-3 py-2 hover:bg-gray-50 cursor-pointer transition"
                                   @click="projectRoleFilter = 'all'; open = false">
                                <input type="radio" 
                                       x-model="projectRoleFilter"
                                       value="all"
                                       class="w-4 h-4 text-violet-600 border-gray-300 focus:ring-violet-500">
                                <span class="text-sm text-gray-700">Semua Role</span>
                            </label>
                            
                            <label class="flex items-center gap-2 px-3 py-2 hover:bg-gray-50 cursor-pointer transition"
                                   @click="projectRoleFilter = 'admin'; open = false">
                                <input type="radio" 
                                       x-model="projectRoleFilter"
                                       value="admin"
                                       class="w-4 h-4 text-violet-600 border-gray-300 focus:ring-violet-500">
                                <span class="text-sm text-gray-700">Admin Project</span>
                            </label>
                            
                            <label class="flex items-center gap-2 px-3 py-2 hover:bg-gray-50 cursor-pointer transition"
                                   @click="projectRoleFilter = 'member'; open = false">
                                <input type="radio" 
                                       x-model="projectRoleFilter"
                                       value="member"
                                       class="w-4 h-4 text-violet-600 border-gray-300 focus:ring-violet-500">
                                <span class="text-sm text-gray-700">Member</span>
                            </label>
                        </div>
                    </div>

                    <!-- Visible Counter -->
                    <div class="px-4 py-2.5 bg-gray-100 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 whitespace-nowrap">
                        <span x-text="visibleMembersCount"></span> member
                    </div>
                </div>
            </div>
            @endif

            {{-- Member List Header (Visible to All) --}}
            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide">
                    Daftar Anggota Tim ({{ $project->members->count() }})
                </h3>
                
                @if($project->canManageMembers(Auth::user()))
                <div x-show="showManageMembers" class="flex items-center gap-2">
                    <label class="flex items-center gap-2 text-sm font-medium text-violet-600 cursor-pointer hover:text-violet-700">
                        <input type="checkbox" 
                               @change="toggleSelectAll($event.target.checked)"
                               class="w-4 h-4 text-violet-600 border-gray-300 rounded focus:ring-violet-500">
                        <span>Pilih Semua</span>
                    </label>
                </div>
                @endif
            </div>

            {{-- Member Cards --}}
            <div class="space-y-3">
                {{-- Project Manager (Owner) --}}
                <div class="group relative overflow-hidden rounded-xl border-2 border-purple-200 bg-gradient-to-r from-purple-50 to-violet-50 p-4 hover:shadow-lg transition-all duration-300">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4 flex-1">
                            <div class="relative">
                                <div class="w-14 h-14 rounded-full bg-gradient-to-br from-purple-600 to-violet-600 text-white flex items-center justify-center font-bold text-xl shadow-lg">
                                    {{ strtoupper(substr($project->owner->name, 0, 1)) }}
                                </div>
                                <div class="absolute -bottom-1 -right-1 w-6 h-6 bg-yellow-400 rounded-full border-2 border-white flex items-center justify-center">
                                    <svg class="h-3 w-3 text-yellow-900" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-1">
                                <div class="font-bold text-gray-900 text-lg">{{ $project->owner->name }}</div>
                                <div class="text-sm text-gray-600">{{ $project->owner->email }}</div>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="px-4 py-2 text-sm font-bold rounded-full bg-gradient-to-r from-purple-600 to-violet-600 text-white shadow-md">
                                Project Manager
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Other Members --}}
                @foreach($project->members->sortByDesc(function($member) { return $member->pivot->role === 'admin'; }) as $member)
                @if($member->id !== $project->owner_id)
                @php
                    $eventRolesArray = $member->pivot->event_roles ? json_decode($member->pivot->event_roles, true) : [];
                    $eventRole = !empty($eventRolesArray) ? $eventRolesArray[0] : null;
                    $allRoles = \App\Models\Ticket::getAllRoles();
                    
                    // Check if member has permanent role
                    $permanentRoleKeys = array_keys(\App\Models\Ticket::getAvailableRoles());
                    $hasPermanentRole = !empty($eventRole) && in_array($eventRole, $permanentRoleKeys);
                @endphp
                <div class="member-card group relative overflow-hidden rounded-xl border-2 border-gray-200 bg-white p-4 hover:border-violet-300 hover:shadow-md transition-all duration-300"
                     x-data="{ editMode: false, projectRole: '{{ $member->pivot->role }}', eventRole: '{{ $eventRole ?? '' }}' }"
                     x-show="isMemberVisible('{{ $member->name }}', '{{ $member->pivot->role }}')"
                     data-member-name="{{ $member->name }}"
                     data-member-role="{{ $member->pivot->role }}">
                    <div class="flex items-center justify-between">
                        @if($project->canManageMembers(Auth::user()))
                        <!-- Bulk Select Checkbox -->
                        <div x-show="showManageMembers" class="mr-3">
                            <input type="checkbox" 
                                   :value="{{ $member->id }}"
                                   @change="toggleMemberSelection({{ $member->id }}, $event.target.checked, {{ $hasPermanentRole ? 'true' : 'false' }})"
                                   :disabled="{{ $hasPermanentRole ? 'true' : 'false' }}"
                                   class="w-5 h-5 text-violet-600 border-gray-300 rounded focus:ring-violet-500 {{ $hasPermanentRole ? 'opacity-40 cursor-not-allowed' : '' }}">
                        </div>
                        @endif
                        
                        <div class="flex items-center gap-4 flex-1">
                            <div class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-600 to-indigo-600 text-white flex items-center justify-center font-semibold text-lg shadow">
                                {{ strtoupper(substr($member->name, 0, 1)) }}
                            </div>
                            <div class="flex-1">
                                <div class="font-semibold text-gray-900">{{ $member->name }}</div>
                                <div class="text-sm text-gray-500">{{ $member->email }}</div>
                                
                                {{-- Show event role if exists --}}
                                @if($eventRole)
                                <div class="mt-2">
                                    <span class="text-xs px-2 py-1 {{ $hasPermanentRole ? 'bg-blue-100 text-blue-700 border-blue-300' : 'bg-amber-100 text-amber-700 border-amber-300' }} rounded-full border">
                                        {{ $hasPermanentRole ? '' : '' }} {{ $allRoles[$eventRole] ?? $eventRole }}
                                        @if($hasPermanentRole)
                                            <span class="text-[10px] opacity-75">(Permanent)</span>
                                        @endif
                                    </span>
                                </div>
                                @endif
                            </div>
                        </div>
                        
                        <div class="flex items-center gap-2">
                            {{-- Role Badge --}}
                            <span class="px-3 py-1.5 text-xs font-semibold rounded-full {{ $member->pivot->role === 'admin' ? 'bg-emerald-100 text-emerald-700 border border-emerald-300' : 'bg-gray-100 text-gray-700 border border-gray-300' }}">
                                {{ $member->pivot->role === 'admin' ? 'Admin Project' : 'Member' }}
                            </span>
                            
                            @if($project->canManageMembers(Auth::user()))
                            <div x-show="showManageMembers" class="flex items-center gap-2">
                                {{-- Edit Button (Disabled if permanent role) (PM/HR/Admin Only) --}}
                                @if(!$hasPermanentRole)
                                <button @click="editMode = !editMode" 
                                        class="p-2 hover:bg-blue-50 rounded-lg transition"
                                        title="Edit Role">
                                    <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>
                                @else
                                <div class="p-2 opacity-40 cursor-not-allowed" title="Role permanent tidak dapat diubah">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                </div>
                                @endif

                                {{-- Remove Button (Disabled if permanent role) (PM/HR/Admin Only) --}}
                                @if(!$hasPermanentRole)
                                <form action="{{ route('projects.members.destroy', [$project, $member]) }}" 
                                      method="POST" 
                                      class="inline" 
                                      onsubmit="return confirm('Hapus {{ $member->name }} dari project ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="p-2 hover:bg-red-50 rounded-lg transition"
                                            title="Hapus dari Project">
                                        <svg class="h-5 w-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                                @else
                                <div class="p-2 opacity-40 cursor-not-allowed" title="Member dengan role permanent tidak dapat dihapus dari project">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            @endif
                            {{-- End PM/HR/Admin Only Actions --}}
                        </div>
                    </div>
                    
                    @if($project->canManageMembers(Auth::user()))
                    {{-- Edit Role Form (PM/HR/Admin Only) --}}
                    <div x-show="editMode && showManageMembers" 
                         x-transition
                         class="mt-4 pt-4 border-t-2 border-gray-100">
                        <form action="{{ route('projects.members.updateRole', [$project, $member]) }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                {{-- Project Role --}}
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 mb-2">Project Role</label>
                                    <select name="role" 
                                            x-model="projectRole"
                                            class="w-full text-sm border border-gray-300 p-2 rounded-lg focus:ring-2 focus:ring-violet-500 focus:border-violet-500">
                                        <option value="member">Member</option>
                                        <option value="admin">Admin</option>
                                    </select>
                                </div>
                                
                                {{-- Event Role (Single Select) --}}
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 mb-2">Event Role (Temporary)</label>
                                    <select name="event_role"
                                            x-model="eventRole"
                                            class="w-full text-sm border border-gray-300 p-2 rounded-lg focus:ring-2 focus:ring-violet-500 focus:border-violet-500">
                                        <option value="">-- Tidak Ada --</option>
                                        {{-- Only Event Roles --}}
                                        <optgroup label="Role Event">
                                            @foreach(\App\Models\Ticket::getEventRoles() as $key => $label)
                                                <option value="{{ $key }}">{{ $label }}</option>
                                            @endforeach
                                        </optgroup>
                                    </select>
                                    <p class="text-[10px] text-gray-500 mt-1">
                                        â„¹ï¸ Role permanent tidak dapat diubah di project
                                    </p>
                                </div>
                            </div>
                            
                            <div class="flex items-center gap-2 mt-4">
                                <button type="submit" 
                                        class="px-4 py-2 text-sm bg-violet-600 text-white font-medium rounded-lg hover:bg-violet-700 transition">
                                    Simpan
                                </button>
                                <button type="button" 
                                        @click="editMode = false"
                                        class="px-4 py-2 text-sm border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                                    Batal
                                </button>
                            </div>
                        </form>
                    </div>
                    @endif
                    {{-- End Edit Role Form (PM/HR/Admin Only) --}}
                </div>
                @endif
                @endforeach

                @if($project->members->where('id', '!==', $project->owner_id)->count() === 0)
                <div class="text-center py-12 px-6 bg-gradient-to-br from-gray-50 to-blue-50 rounded-xl border-2 border-dashed border-gray-300">
                    <svg class="h-16 w-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <p class="text-gray-600 font-medium mb-2">Belum ada member lain di project ini</p>
                    <p class="text-sm text-gray-500">Klik tombol "Tambah Member" untuk mengundang anggota tim</p>
                </div>
                @endif
            </div>{{-- End Member List --}}
        </div>{{-- End Members Card p-6 --}}
    </div>{{-- End Members Card --}}
    </div>{{-- End Members Tab --}}
    
    <script>
    function memberManagement() {
        return {
            showAddMember: false,
            showManageMembers: false,
            selectedMembers: [],
            searchQuery: '',
            projectRoleFilter: 'all',
            visibleMembersCount: 0,
            
            init() {
                // Update visible count initially
                this.updateVisibleCount();
                // Watch for filter changes
                this.$watch('searchQuery', () => this.updateVisibleCount());
                this.$watch('projectRoleFilter', () => this.updateVisibleCount());
            },
            
            isMemberVisible(memberName, memberRole) {
                // Search filter
                const matchesSearch = !this.searchQuery || 
                                    memberName.toLowerCase().includes(this.searchQuery.toLowerCase());
                
                // Role filter
                const matchesRole = this.projectRoleFilter === 'all' || 
                                   this.projectRoleFilter === memberRole;
                
                return matchesSearch && matchesRole;
            },
            
            updateVisibleCount() {
                // Count visible member cards
                const cards = document.querySelectorAll('.member-card');
                let visible = 0;
                
                cards.forEach(card => {
                    const memberName = card.dataset.memberName;
                    const memberRole = card.dataset.memberRole;
                    
                    if (this.isMemberVisible(memberName, memberRole)) {
                        visible++;
                    }
                });
                
                this.visibleMembersCount = visible;
            },
            
            toggleMemberSelection(memberId, isChecked, isPermanent) {
                if (isPermanent) return; // Don't allow selection of permanent role members
                
                if (isChecked) {
                    if (!this.selectedMembers.includes(memberId)) {
                        this.selectedMembers.push(memberId);
                    }
                } else {
                    this.selectedMembers = this.selectedMembers.filter(id => id !== memberId);
                }
            },
            
            toggleSelectAll(isChecked) {
                // Only select checkboxes in member cards (not in add member form)
                const checkboxes = document.querySelectorAll('.member-card input[type="checkbox"][value]:not([disabled])');
                this.selectedMembers = [];
                
                checkboxes.forEach(cb => {
                    if (cb.hasAttribute('value')) {
                        cb.checked = isChecked;
                        if (isChecked) {
                            const memberId = parseInt(cb.value);
                            if (!this.selectedMembers.includes(memberId)) {
                                this.selectedMembers.push(memberId);
                            }
                        }
                    }
                });
            },
            
            async bulkChangeRole(newRole) {
                if (this.selectedMembers.length === 0) {
                    alert('Pilih minimal 1 member terlebih dahulu');
                    return;
                }
                
                const roleLabel = newRole === 'admin' ? 'Admin Project' : 'Member';
                if (!confirm(`Ubah ${this.selectedMembers.length} member menjadi ${roleLabel}?`)) {
                    return;
                }
                
                try {
                    const response = await fetch('{{ route("projects.members.bulkUpdateRole", $project) }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            member_ids: this.selectedMembers,
                            role: newRole
                        })
                    });
                    
                    if (response.ok) {
                        window.location.reload();
                    } else {
                        alert('Gagal mengubah role members');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan');
                }
            },
            
            async bulkDeleteMembers() {
                if (this.selectedMembers.length === 0) {
                    alert('Pilih minimal 1 member terlebih dahulu');
                    return;
                }
                
                if (!confirm(`Hapus ${this.selectedMembers.length} member dari project ini?`)) {
                    return;
                }
                
                try {
                    const response = await fetch('{{ route("projects.members.bulkDelete", $project) }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            member_ids: this.selectedMembers
                        })
                    });
                    
                    if (response.ok) {
                        window.location.reload();
                    } else {
                        alert('Gagal menghapus members');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan');
                }
            }
        }
    }
    
    // Target User Filter Component (for ticket creation)
    function targetUserFilter() {
        return {
            searchQuery: '',
            roleFilter: 'all',
            visibleCount: 0,
            selectedCount: 0,
            
            init() {
                this.updateCounts();
                this.$watch('searchQuery', () => this.updateCounts());
                this.$watch('roleFilter', () => this.updateCounts());
                // Listen for selection changes
                window.addEventListener('target-user-changed', () => this.updateCounts());
            },
            
            isVisible(memberName, isAdmin, hasPermanentRole, hasEventRole) {
                // Search filter
                const matchesSearch = !this.searchQuery || 
                                    memberName.toLowerCase().includes(this.searchQuery.toLowerCase());
                
                // Role filter
                let matchesRole = true;
                if (this.roleFilter === 'admin') {
                    matchesRole = isAdmin;
                } else if (this.roleFilter === 'permanent') {
                    matchesRole = hasPermanentRole;
                } else if (this.roleFilter === 'event') {
                    matchesRole = hasEventRole;
                }
                // 'all' matches everything
                
                return matchesSearch && matchesRole;
            },
            
            updateCounts() {
                const items = document.querySelectorAll('.target-user-item');
                let visible = 0;
                let selected = 0;
                
                items.forEach(item => {
                    const memberName = item.dataset.memberName;
                    const isAdmin = item.dataset.isAdmin === 'true';
                    const hasPermanent = item.dataset.hasPermanent === 'true';
                    const hasEvent = item.dataset.hasEvent === 'true';
                    
                    if (this.isVisible(memberName, isAdmin, hasPermanent, hasEvent)) {
                        visible++;
                        const checkbox = item.querySelector('.target-user-checkbox');
                        if (checkbox && checkbox.checked) {
                            selected++;
                        }
                    }
                });
                
                this.visibleCount = visible;
                this.selectedCount = selected;
            },
            
            selectAllVisible() {
                const items = document.querySelectorAll('.target-user-item');
                
                items.forEach(item => {
                    const memberName = item.dataset.memberName;
                    const isAdmin = item.dataset.isAdmin === 'true';
                    const hasPermanent = item.dataset.hasPermanent === 'true';
                    const hasEvent = item.dataset.hasEvent === 'true';
                    
                    if (this.isVisible(memberName, isAdmin, hasPermanent, hasEvent)) {
                        const checkbox = item.querySelector('.target-user-checkbox');
                        if (checkbox && !checkbox.checked) {
                            checkbox.click(); // Trigger Alpine's x-model
                        }
                    }
                });
            }
        }
    }
    
    // Add Member Filter Component (similar to create.blade.php)
    function addMemberFilter() {
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
                window.addEventListener('add-member-changed', () => this.updateCounts());
                // Watch for search and filter changes
                this.$watch('searchQuery', () => this.updateCounts());
                this.$watch('selectedRoles', () => this.updateCounts());
            },

            countRoles() {
                const rows = document.querySelectorAll('.add-member-row');
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
                const rows = document.querySelectorAll('.add-member-row');
                let visible = 0;
                let selected = 0;
                
                rows.forEach(row => {
                    const userName = row.dataset.userName;
                    const userRoles = JSON.parse(row.dataset.userRoles || '[]');
                    const isVisible = this.isVisible(userName, userRoles);
                    
                    if (isVisible) {
                        visible++;
                        const checkbox = row.querySelector('.add-member-checkbox');
                        if (checkbox && checkbox.checked) {
                            selected++;
                        }
                    }
                });
                
                this.visibleCount = visible;
                this.selectedCount = selected;
            },

            toggleAllMembers() {
                const selectAllCheckbox = document.getElementById('select-all-add-members');
                const memberCheckboxes = document.querySelectorAll('.add-member-checkbox');
                
                selectAllCheckbox.checked = !selectAllCheckbox.checked;
                const isChecked = selectAllCheckbox.checked;
                
                // Only toggle visible checkboxes
                memberCheckboxes.forEach(checkbox => {
                    const row = checkbox.closest('.add-member-row');
                    const isVisible = row && window.getComputedStyle(row).display !== 'none';
                    
                    if (isVisible && checkbox.checked !== isChecked) {
                        checkbox.click(); // Trigger click to maintain Alpine.js state
                    }
                });
            }
        }
