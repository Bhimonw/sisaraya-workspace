@php
    use App\Models\Project;
    use App\Models\Document;
    use App\Models\Ticket;
    use App\Models\PersonalActivity;

    $documentsCount = Document::count();
    $user = auth()->user();
    $userRoles = $user->getRoleNames()->toArray();
    
    // Projectku: proyek AKTIF yang diikuti user (owner OR member)
    $activeProjectsCount = Project::where(function($q) use ($user) {
        $q->where('owner_id', $user->id)
          ->orWhereHas('members', function($q2) use ($user) {
              $q2->where('user_id', $user->id);
          });
    })->where('status', 'active')->count();
    
    // Tiketku: tiket AKTIF saja (todo & doing) - bukan done
    $myTicketsCount = Ticket::where(function($q) use ($user, $userRoles) {
        $q->where('claimed_by', $user->id)
          ->orWhere(function($q2) use ($userRoles) {
              $q2->whereIn('target_role', $userRoles)
                 ->whereNull('claimed_by');
          })
          ->orWhere(function($q3) use ($user) {
              $q3->where('target_user_id', $user->id)
                 ->whereNull('claimed_by');
          });
    })
    ->whereIn('status', ['todo', 'doing']) // HANYA tiket aktif
    ->count();
    
    $upcomingActivitiesCount = PersonalActivity::where('user_id', auth()->id())
        ->where('start_time', '>=', now())
        ->where('start_time', '<=', now()->addDays(7))
        ->count();
@endphp

<ul class="space-y-1" x-data="{ 
    openMenus: {
        mejaKerja: false,
        rab: false,
        penyimpanan: false,
        management: false,
        pribadi: false
    }
}">
    {{-- 1. Dashboard --}}
    <li>
        @php $active = request()->routeIs('dashboard') || request()->routeIs('app.dashboard'); @endphp
        <a href="{{ route('dashboard') }}" class="flex items-center justify-between w-full px-3 py-2 rounded {{ $active ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50' }}">
            <span class="inline-flex items-center gap-2">
                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M13 5v6h6" />
                </svg>
                {{ __('Dashboard') }}
            </span>
        </a>
    </li>

    @auth
    {{-- 2. Ruang Pribadi (tidak untuk Guest) --}}
    @if(!$user->hasRole('guest'))
        <li>
            <button @click="openMenus.pribadi = !openMenus.pribadi" class="flex items-center justify-between w-full px-3 py-2 rounded text-gray-600 hover:bg-gray-50">
                <span class="inline-flex items-center gap-2">
                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    {{ __('Ruang Pribadi') }}
                </span>
                <svg class="h-4 w-4 transition-transform" :class="openMenus.pribadi ? 'rotate-90' : ''" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </button>
            <ul x-show="openMenus.pribadi" x-collapse class="ml-6 mt-1 space-y-1">
                <li>
                    @php $active = request()->routeIs('calendar.personal'); @endphp
                    <a href="{{ route('calendar.personal') }}" class="flex items-center justify-between px-3 py-1.5 text-sm rounded {{ $active ? 'bg-indigo-100 text-indigo-900' : 'text-gray-600 hover:bg-gray-50' }}">
                        <span class="inline-flex items-center gap-2">
                            <svg class="h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            Kalender Pribadi
                        </span>
                        @if($upcomingActivitiesCount > 0)
                            <span class="text-xs bg-indigo-100 text-indigo-700 rounded-full px-1.5 py-0.5">{{ $upcomingActivitiesCount }}</span>
                        @endif
                    </a>
                </li>
                <li>
                    @php $active = request()->routeIs('notes.*'); @endphp
                    <a href="{{ route('notes.index') }}" class="flex items-center gap-2 px-3 py-1.5 text-sm rounded {{ $active ? 'bg-indigo-100 text-indigo-900' : 'text-gray-600 hover:bg-gray-50' }}">
                        <svg class="h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Catatan Pribadi
                    </a>
                </li>
            </ul>
        </li>
    @endif

    {{-- 3. Meja Kerja (Proyek & Tiket) - Accessible to ALL users --}}
    <li>
        <button @click="openMenus.mejaKerja = !openMenus.mejaKerja" class="flex items-center justify-between w-full px-3 py-2 rounded text-gray-600 hover:bg-gray-50">
            <span class="inline-flex items-center gap-2">
                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                </svg>
                {{ __('Meja Kerja') }}
                @if($activeProjectsCount > 0 || $myTicketsCount > 0)
                    <span class="text-xs bg-green-100 text-green-700 rounded-full px-2 py-0.5">
                        {{ $activeProjectsCount + $myTicketsCount }}
                    </span>
                @endif
            </span>
            <svg class="h-4 w-4 transition-transform" :class="openMenus.mejaKerja ? 'rotate-90' : ''" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
        </button>
        <ul x-show="openMenus.mejaKerja" x-collapse class="ml-6 mt-1 space-y-1">
            @if(!$user->hasRole('guest'))
            <li>
                @php $active = request()->routeIs('workspace'); @endphp
                <a href="{{ route('workspace') }}" class="flex items-center justify-between px-3 py-1.5 text-sm rounded {{ $active ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50' }}">
                    <span class="inline-flex items-center gap-2">
                        <svg class="h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                        <span>
                            Projectku
                            <span class="block text-[10px] text-gray-500">Aktif</span>
                        </span>
                    </span>
                    @if($activeProjectsCount > 0)
                        <span class="text-xs bg-green-100 text-green-700 rounded-full px-1.5 py-0.5">{{ $activeProjectsCount }}</span>
                    @endif
                </a>
            </li>
            <li>
                @php $active = request()->routeIs('tickets.mine'); @endphp
                <a href="{{ route('tickets.mine') }}" class="flex items-center justify-between px-3 py-1.5 text-sm rounded {{ $active ? 'bg-purple-100 text-purple-900' : 'text-gray-600 hover:bg-gray-50' }}">
                    <span class="inline-flex items-center gap-2">
                        <svg class="h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        <span>
                            Tiketku
                            <span class="block text-[10px] text-gray-500">Aktif</span>
                        </span>
                    </span>
                    @if($myTicketsCount > 0)
                        <span class="text-xs bg-purple-100 text-purple-700 rounded-full px-1.5 py-0.5">{{ $myTicketsCount }}</span>
                    @endif
                </a>
            </li>
            @endif
            <li>
                @php $active = request()->routeIs('projects.allMine'); @endphp
                <a href="{{ route('projects.allMine') }}" class="flex items-center justify-between px-3 py-1.5 text-sm rounded {{ $active ? 'bg-blue-100 text-blue-900' : 'text-gray-600 hover:bg-gray-50' }}">
                    <span class="inline-flex items-center gap-2">
                        <svg class="h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                        </svg>
                        <span>
                            Semua Projectku
                            <span class="block text-[10px] text-gray-500">Riwayat</span>
                        </span>
                    </span>
                </a>
            </li>
            <li>
                @php $active = request()->routeIs('tickets.overview'); @endphp
                <a href="{{ route('tickets.overview') }}" class="flex items-center justify-between px-3 py-1.5 text-sm rounded {{ $active ? 'bg-purple-100 text-purple-900' : 'text-gray-600 hover:bg-gray-50' }}">
                    <span class="inline-flex items-center gap-2">
                        <svg class="h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span>
                            Semua Tiketku
                            <span class="block text-[10px] text-gray-500">Riwayat</span>
                        </span>
                    </span>
                </a>
            </li>
        </ul>
    </li>

    {{-- 4. RAB & Laporan (tidak untuk Guest) --}}
    @if(!$user->hasRole('guest'))
    <li>
        <button @click="openMenus.rab = !openMenus.rab" class="flex items-center justify-between w-full px-3 py-2 rounded text-gray-600 hover:bg-gray-50">
            <span class="inline-flex items-center gap-2">
                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                {{ __('RAB & Laporan') }}
            </span>
            <svg class="h-4 w-4 transition-transform" :class="openMenus.rab ? 'rotate-90' : ''" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
        </button>
        <ul x-show="openMenus.rab" x-collapse class="ml-6 mt-1 space-y-1">
            <li>
                @php $active = request()->routeIs('rabs.create'); @endphp
                <a href="{{ route('rabs.create') }}" class="flex items-center gap-2 px-3 py-1.5 text-sm rounded {{ $active ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50' }}">
                    <svg class="h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Pengajuan RAB
                </a>
            </li>
            @role('bendahara')
            <li>
                @php $active = request()->routeIs('rabs.index'); @endphp
                <a href="{{ route('rabs.index') }}" class="flex items-center gap-2 px-3 py-1.5 text-sm rounded {{ $active ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50' }}">
                    <svg class="h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Persetujuan RAB
                </a>
            </li>
            @endrole
            <li>
                @php $active = request()->routeIs('rabs.index'); @endphp
                <a href="{{ route('rabs.index') }}" class="flex items-center gap-2 px-3 py-1.5 text-sm rounded {{ $active ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50' }}">
                    <svg class="h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Daftar RAB & Laporan
                </a>
            </li>
        </ul>
    </li>
    @endif

    {{-- 5. Ruang Management (Role-Specific, tidak untuk Guest) --}}
    @if($user->hasAnyRole(['hr','pm','bendahara','sekretaris','kewirausahaan']))
        <li class="pt-2 mt-2 border-t border-gray-200">
            <button @click="openMenus.management = !openMenus.management" class="flex items-center justify-between w-full px-3 py-2 rounded text-gray-600 hover:bg-gray-50">
                <span class="inline-flex items-center gap-2">
                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    {{ __('Ruang Management') }}
                </span>
                <svg class="h-4 w-4 transition-transform" :class="openMenus.management ? 'rotate-90' : ''" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </button>
            <ul x-show="openMenus.management" x-collapse class="ml-6 mt-1 space-y-1">
                @role('hr')
                    <li>
                        @php $active = request()->routeIs('admin.users.*'); @endphp
                        <a href="{{ route('admin.users.index') }}" class="flex items-center gap-2 px-3 py-1.5 text-sm rounded {{ $active ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50' }}">
                            <svg class="h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            Manajemen Anggota
                        </a>
                    </li>
                @endrole
                @role('pm')
                    <li>
                        @php $active = request()->routeIs('projects.*'); @endphp
                        <a href="{{ route('projects.index') }}" class="flex items-center gap-2 px-3 py-1.5 text-sm rounded {{ $active ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50' }}">
                            <svg class="h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                            Manajemen Proyek
                        </a>
                    </li>
                    <li>
                        @php $active = request()->routeIs('tickets.index'); @endphp
                        <a href="{{ route('tickets.index') }}" class="flex items-center gap-2 px-3 py-1.5 text-sm rounded {{ $active ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50' }}">
                            <svg class="h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            Manajemen Tiket
                        </a>
                    </li>
                @endrole
                @role('bendahara')
                    <li>
                        @php $active = request()->routeIs('rabs.index'); @endphp
                        <a href="{{ route('rabs.index') }}" class="flex items-center gap-2 px-3 py-1.5 text-sm rounded {{ $active ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50' }}">
                            <svg class="h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Verifikasi RAB
                        </a>
                    </li>
                @endrole
                @role('sekretaris')
                    <li>
                        @php $active = request()->routeIs('documents.index'); @endphp
                        <a href="{{ route('documents.index') }}" class="flex items-center gap-2 px-3 py-1.5 text-sm rounded {{ $active ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50' }}">
                            <svg class="h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                            </svg>
                            Pengelolaan Arsip
                        </a>
                    </li>
                @endrole
                @role('kewirausahaan')
                    <li>
                        @php $active = request()->routeIs('businesses.*'); @endphp
                        <a href="{{ route('businesses.index') }}" class="flex items-center gap-2 px-3 py-1.5 text-sm rounded {{ $active ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50' }}">
                            <svg class="h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            Usaha Aktif
                        </a>
                    </li>
                @endrole
            </ul>
        </li>
    @endif

    {{-- 6. Ruang Dokumen (tidak untuk Guest) --}}
    @if(!$user->hasRole('guest'))
    <li>
        <button @click="openMenus.penyimpanan = !openMenus.penyimpanan" class="flex items-center justify-between w-full px-3 py-2 rounded text-gray-600 hover:bg-gray-50">
            <span class="inline-flex items-center gap-2">
                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                </svg>
                {{ __('Ruang Dokumen') }}
            </span>
            <svg class="h-4 w-4 transition-transform" :class="openMenus.penyimpanan ? 'rotate-90' : ''" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
        </button>
        <ul x-show="openMenus.penyimpanan" x-collapse class="ml-6 mt-1 space-y-1">
            <li>
                @php $active = request()->routeIs('documents.index'); @endphp
                <a href="{{ route('documents.index') }}" class="flex items-center gap-2 px-3 py-1.5 text-sm rounded {{ $active ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50' }}">
                    <svg class="h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                    </svg>
                    Dokumen Umum
                </a>
            </li>
            @role('sekretaris|hr')
            <li>
                @php $active = request()->routeIs('documents.index') && request()->get('type') === 'confidential'; @endphp
                <a href="{{ route('documents.index', ['type' => 'confidential']) }}" class="flex items-center gap-2 px-3 py-1.5 text-sm rounded {{ $active ? 'bg-red-100 text-red-900' : 'text-gray-600 hover:bg-gray-50' }}">
                    <svg class="h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                    Dokumen Rahasia
                </a>
            </li>
            @endrole
        </ul>
    </li>
    @endif

    {{-- 7. Voting (untuk non-Guest) --}}
    @if(!$user->hasRole('guest'))
    <li>
        @php $active = request()->routeIs('votes.*'); @endphp
        <a href="{{ route('votes.index') }}" class="flex items-center justify-between w-full px-3 py-2 rounded {{ $active ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50' }}">
            <span class="inline-flex items-center gap-2">
                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                </svg>
                {{ __('Voting') }}
            </span>
        </a>
    </li>
    @endif

    {{-- 8. Akun & Pengaturan --}}
    <li class="pt-2 mt-2 border-t border-gray-200">
        @php $active = request()->routeIs('profile.*'); @endphp
        <a href="{{ route('profile.edit') }}" class="flex items-center justify-between w-full px-3 py-2 rounded {{ $active ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50' }}">
            <span class="inline-flex items-center gap-2">
                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                {{ __('Akun & Pengaturan') }}
            </span>
        </a>
    </li>
    @endauth
</ul>
