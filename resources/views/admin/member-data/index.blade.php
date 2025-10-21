<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-bold text-2xl bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent leading-tight">
                    📊 Data Anggota
                </h2>
                <p class="text-sm text-gray-600 mt-1">Kelola dan pantau data anggota SISARAYA</p>
            </div>
            <a href="{{ route('admin.member-data.export') }}" class="bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white px-5 py-2.5 rounded-xl text-sm font-semibold flex items-center gap-2 shadow-lg hover:shadow-xl transition-all duration-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Export CSV
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Search -->
            <div class="bg-gradient-to-br from-white to-blue-50 overflow-hidden shadow-lg hover:shadow-xl transition-shadow duration-300 rounded-2xl mb-6 border border-blue-100">
                <div class="p-6">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="bg-gradient-to-r from-blue-500 to-purple-600 p-3 rounded-xl">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-900">Pencarian</h3>
                            <p class="text-sm text-gray-600">Cari berdasarkan nama atau username</p>
                        </div>
                    </div>
                    <form method="GET" action="{{ route('admin.member-data.index') }}" class="flex gap-3">
                        <div class="flex-1 relative">
                            <input type="text" name="search" value="{{ $search }}" placeholder="Ketik nama atau username..." 
                                   class="w-full pl-12 pr-4 py-3 rounded-xl border-2 border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200">
                            <svg class="w-5 h-5 text-gray-400 absolute left-4 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <button type="submit" class="px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white rounded-xl font-semibold shadow-md hover:shadow-lg transition-all duration-200">
                            Cari
                        </button>
                        @if($search)
                            <a href="{{ route('admin.member-data.index') }}" class="px-6 py-3 border-2 border-gray-300 rounded-xl text-gray-700 hover:bg-gray-50 font-semibold transition-all duration-200">
                                Reset
                            </a>
                        @endif
                    </form>
                </div>
            </div>

            <!-- Users List -->
            <div class="bg-white overflow-hidden shadow-lg sm:rounded-2xl border border-gray-100">
                <div class="bg-gradient-to-r from-gray-50 to-white px-6 py-4 border-b border-gray-200">
                    <h3 class="font-bold text-gray-900 flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        Daftar Anggota
                        <span class="ml-auto bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-sm font-semibold">{{ $users->total() }} anggota</span>
                    </h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        @forelse($users as $user)
                            <div class="group relative bg-gradient-to-br from-white to-gray-50 border-2 border-gray-200 hover:border-blue-300 rounded-2xl p-5 hover:shadow-xl transition-all duration-300">
                                <div class="flex items-start justify-between gap-4">
                                    <div class="flex items-start gap-4 flex-1">
                                        @if($user->photo_path)
                                            <img src="{{ asset('storage/' . $user->photo_path) }}" alt="{{ $user->name }}" 
                                                 class="w-16 h-16 rounded-2xl object-cover shadow-md ring-4 ring-white group-hover:ring-blue-100 transition-all duration-300">
                                        @else
                                            <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-blue-400 to-purple-500 flex items-center justify-center text-white font-bold text-2xl shadow-md ring-4 ring-white group-hover:ring-blue-100 transition-all duration-300">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </div>
                                        @endif
                                        
                                        <div class="flex-1">
                                            <h3 class="font-bold text-xl text-gray-900 group-hover:text-blue-600 transition-colors">{{ $user->name }}</h3>
                                            <p class="text-sm text-gray-600 font-medium">@{{ $user->username }}</p>
                                            
                                            <div class="flex flex-wrap gap-2 mt-3">
                                                @foreach($user->roles as $role)
                                                    <span class="text-xs px-3 py-1.5 bg-gradient-to-r from-blue-500 to-purple-500 text-white rounded-full font-semibold shadow-sm">
                                                        {{ ucfirst($role->name) }}
                                                    </span>
                                                @endforeach
                                            </div>
                                            
                                            @if($user->phone || $user->whatsapp)
                                                <div class="flex flex-wrap gap-4 mt-3 text-sm">
                                                    @if($user->phone)
                                                        <div class="flex items-center gap-1.5 text-gray-700 bg-white px-3 py-1.5 rounded-lg border border-gray-200">
                                                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                                            </svg>
                                                            <span>{{ $user->phone }}</span>
                                                        </div>
                                                    @endif
                                                    @if($user->whatsapp)
                                                        <div class="flex items-center gap-1.5 text-gray-700 bg-white px-3 py-1.5 rounded-lg border border-gray-200">
                                                            <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 24 24">
                                                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                                                            </svg>
                                                            <span>{{ $user->whatsapp }}</span>
                                                        </div>
                                                    @endif
                                                </div>
                                            @endif
                                            
                                            <div class="flex gap-3 mt-3">
                                                <div class="flex items-center gap-2 bg-gradient-to-r from-blue-50 to-blue-100 px-3 py-2 rounded-xl border border-blue-200">
                                                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                                                    </svg>
                                                    <span class="text-sm font-bold text-blue-700">{{ $user->skills_count }}</span>
                                                    <span class="text-xs text-blue-600">skill</span>
                                                </div>
                                                <div class="flex items-center gap-2 bg-gradient-to-r from-green-50 to-green-100 px-3 py-2 rounded-xl border border-green-200">
                                                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    <span class="text-sm font-bold text-green-700">{{ $user->modals_count }}</span>
                                                    <span class="text-xs text-green-600">modal</span>
                                                </div>
                                                <div class="flex items-center gap-2 bg-gradient-to-r from-purple-50 to-purple-100 px-3 py-2 rounded-xl border border-purple-200">
                                                    <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                                                    </svg>
                                                    <span class="text-sm font-bold text-purple-700">{{ $user->links_count }}</span>
                                                    <span class="text-xs text-purple-600">link</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <a href="{{ route('admin.member-data.show', $user) }}" 
                                       class="px-5 py-2.5 bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 text-white rounded-xl text-sm font-semibold whitespace-nowrap shadow-lg hover:shadow-xl transition-all duration-200 flex items-center gap-2">
                                        Lihat Detail
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-16">
                                <svg class="w-20 h-20 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                <p class="text-gray-500 text-lg font-semibold mb-2">
                                    @if($search)
                                        Tidak ada hasil untuk "{{ $search }}"
                                    @else
                                        Belum ada data anggota
                                    @endif
                                </p>
                                @if($search)
                                    <a href="{{ route('admin.member-data.index') }}" class="text-blue-600 hover:text-blue-700 font-medium">
                                        Reset pencarian
                                    </a>
                                @endif
                            </div>
                        @endforelse
                    </div>

                    <div class="mt-6">
                        {{ $users->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
