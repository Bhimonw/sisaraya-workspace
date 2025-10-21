{{-- 
    User Table Component
    
    Menampilkan daftar users dalam format tabel dengan fitur lengkap.
    
    Props:
    - users (required): Collection dari User models
    
    Features:
    - Responsive dengan horizontal scroll
    - Hover effect dengan gradient
    - Avatar mini untuk setiap user
    - Role badges dengan warna konsisten
    - Manage Roles action
    - Empty state dengan icon
--}}

@props(['users'])

<div class="bg-white rounded-2xl shadow-md border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            
            {{-- TABLE HEADER --}}
            <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">No</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">ID</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Anggota</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Username</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Roles</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            
            {{-- TABLE BODY --}}
            <tbody class="bg-white divide-y divide-gray-100">
                @forelse($users as $index => $user)
                    <tr class="hover:bg-gradient-to-r hover:from-violet-50 hover:to-blue-50 transition-colors duration-150">
                        
                        {{-- No Column --}}
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm font-semibold text-gray-700">{{ $index + 1 }}</span>
                        </td>
                        
                        {{-- ID Column --}}
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm font-mono text-gray-600 font-semibold">#{{ $user->id }}</span>
                        </td>
                        
                        {{-- Anggota Column (Avatar + Name) --}}
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 rounded-full bg-gradient-to-br from-violet-500 to-blue-600 flex items-center justify-center text-white font-bold text-sm shadow-md">
                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                </div>
                                <div>
                                    <div class="text-sm font-semibold text-gray-900">{{ $user->name }}</div>
                                </div>
                            </div>
                        </td>
                        
                        {{-- Username Column --}}
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-1">
                                <span class="text-gray-400 text-sm">@</span>
                                <span class="text-sm text-gray-900 font-medium">{{ $user->username }}</span>
                            </div>
                        </td>
                        
                        {{-- Roles Column --}}
                        <td class="px-6 py-4">
                            <div class="flex flex-wrap gap-1.5">
                                @forelse($user->roles as $role)
                                    <x-users.role-badge :role="$role->name" />
                                @empty
                                    <span class="px-2.5 py-1 bg-gray-100 text-gray-500 text-xs font-medium rounded-full">
                                        No roles
                                    </span>
                                @endforelse
                            </div>
                        </td>
                        
                        {{-- Actions Column --}}
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <div class="flex items-center gap-2">
                                {{-- Edit Button --}}
                                <a href="{{ route('admin.users.edit', $user) }}" 
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-lg transition-colors duration-150">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                    <span class="text-xs font-medium">Edit</span>
                                </a>
                                
                                {{-- Delete Button --}}
                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" 
                                      x-data="{ showConfirm: false }"
                                      @submit.prevent="if(showConfirm) $el.submit()">
                                    @csrf
                                    @method('DELETE')
                                    
                                    <button type="button"
                                            @click="showConfirm = true; setTimeout(() => showConfirm = false, 3000)"
                                            :class="showConfirm ? 'bg-red-600 text-white' : 'bg-red-50 text-red-700 hover:bg-red-100'"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg transition-all duration-150">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                        <span class="text-xs font-medium" x-text="showConfirm ? 'Klik lagi untuk hapus' : 'Hapus'"></span>
                                    </button>
                                    
                                    {{-- Hidden submit button that triggers when showConfirm is true --}}
                                    <button type="submit" x-show="showConfirm" class="hidden"></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    
                @empty
                    {{-- Empty State Row --}}
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center gap-3">
                                <svg class="h-16 w-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                                <p class="text-gray-500 font-medium">Belum ada anggota terdaftar</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
            
        </table>
    </div>
</div>
