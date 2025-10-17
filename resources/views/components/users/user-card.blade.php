{{-- 
    User Card Component
    
    Menampilkan informasi user dalam format card dengan avatar, roles, dan actions.
    
    Props:
    - user (required): User model instance
    - showActions (optional, default: true): Tampilkan action buttons
--}}

@props(['user', 'showActions' => true])

<div class="group relative overflow-hidden rounded-xl border-2 border-gray-200 bg-white p-6 hover:shadow-lg hover:border-violet-300 transition-all duration-300">
    
    {{-- User ID Badge (Top Right) --}}
    <div class="absolute top-2 right-2 px-2 py-1 bg-gray-100 text-gray-500 text-xs font-mono rounded">
        #{{ $user->id }}
    </div>

    <div class="flex items-start justify-between gap-4">
        
        {{-- LEFT: User Info --}}
        <div class="flex items-center gap-4 flex-1">
            
            {{-- Avatar with Status Indicator --}}
            <div class="relative">
                <div class="h-16 w-16 rounded-full bg-gradient-to-br from-violet-500 to-blue-600 flex items-center justify-center text-white font-bold text-xl shadow-lg">
                    {{ strtoupper(substr($user->name, 0, 2)) }}
                </div>
                {{-- Online Status --}}
                <div class="absolute -bottom-1 -right-1 h-5 w-5 rounded-full bg-green-500 border-2 border-white"></div>
            </div>

            {{-- User Details --}}
            <div class="flex-1 min-w-0">
                {{-- Name & Username --}}
                <div class="flex items-center gap-2 mb-1">
                    <h3 class="text-lg font-bold text-gray-900 truncate">{{ $user->name }}</h3>
                    <span class="inline-flex items-center text-sm text-gray-500">
                        <span class="text-gray-400">@</span>
                        <span class="font-medium">{{ $user->username }}</span>
                    </span>
                </div>
                
                {{-- Email --}}
                @if($user->email)
                <p class="text-sm text-gray-600 mb-2 flex items-center gap-1">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    {{ $user->email }}
                </p>
                @endif

                {{-- Role Badges --}}
                <div class="flex flex-wrap gap-1.5 mt-2">
                    @forelse($user->roles as $role)
                        <x-users.role-badge :role="$role->name" />
                    @empty
                        <span class="px-2.5 py-1 bg-gray-100 text-gray-500 text-xs font-medium rounded-full">
                            No roles
                        </span>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- RIGHT: Action Buttons --}}
        @if($showActions)
        <div class="flex flex-col gap-2 items-end">
            {{-- Manage Roles Button --}}
            <a href="{{ route('admin.users.manage-roles', $user) }}" 
               class="inline-flex items-center gap-1 px-3 py-1.5 bg-violet-500 text-white text-sm font-medium rounded-lg hover:bg-violet-600 hover:scale-105 active:scale-95 transition-all shadow-sm">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                Kelola Role
            </a>
        </div>
        @endif
        
    </div>
</div>
