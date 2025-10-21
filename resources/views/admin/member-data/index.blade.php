<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Data Anggota
            </h2>
            <a href="{{ route('admin.member-data.export') }}" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Export CSV
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Search -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ route('admin.member-data.index') }}" class="flex gap-2">
                        <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama atau username..." class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <button type="submit" class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg">
                            Cari
                        </button>
                        @if($search)
                            <a href="{{ route('admin.member-data.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                                Reset
                            </a>
                        @endif
                    </form>
                </div>
            </div>

            <!-- Users List -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="space-y-4">
                        @forelse($users as $user)
                            <div class="border rounded-lg p-4 hover:bg-gray-50">
                                <div class="flex items-start justify-between">
                                    <div class="flex items-start gap-3 flex-1">
                                        @if($user->photo_path)
                                            <img src="{{ asset('storage/' . $user->photo_path) }}" alt="{{ $user->name }}" class="w-12 h-12 rounded-full object-cover">
                                        @else
                                            <div class="w-12 h-12 rounded-full bg-gray-200 flex items-center justify-center text-gray-600 font-bold">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </div>
                                        @endif
                                        
                                        <div class="flex-1">
                                            <h3 class="font-semibold text-lg">{{ $user->name }}</h3>
                                            <p class="text-sm text-gray-600">@{{ $user->username }}</p>
                                            
                                            <div class="flex flex-wrap gap-1 mt-2">
                                                @foreach($user->roles as $role)
                                                    <span class="text-xs px-2 py-1 bg-blue-100 text-blue-800 rounded">{{ $role->name }}</span>
                                                @endforeach
                                            </div>
                                            
                                            <div class="flex gap-4 mt-2 text-sm text-gray-600">
                                                @if($user->phone)
                                                    <span>📞 {{ $user->phone }}</span>
                                                @endif
                                                @if($user->whatsapp)
                                                    <span>💬 {{ $user->whatsapp }}</span>
                                                @endif
                                            </div>
                                            
                                            <div class="flex gap-4 mt-2 text-sm">
                                                <span class="text-blue-600">{{ $user->skills_count }} skill(s)</span>
                                                <span class="text-green-600">{{ $user->modals_count }} modal</span>
                                                <span class="text-purple-600">{{ $user->links_count }} link(s)</span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <a href="{{ route('admin.member-data.show', $user) }}" class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg text-sm whitespace-nowrap">
                                        Lihat Detail
                                    </a>
                                </div>
                            </div>
                        @empty
                            <p class="text-center text-gray-500 py-8">
                                @if($search)
                                    Tidak ada hasil untuk "{{ $search }}"
                                @else
                                    Belum ada data anggota
                                @endif
                            </p>
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
