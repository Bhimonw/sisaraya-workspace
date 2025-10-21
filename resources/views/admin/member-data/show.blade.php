<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Data Kepegawaian: {{ $user->name }}
            </h2>
            <a href="{{ route('admin.member-data.index') }}" class="text-gray-600 hover:text-gray-800">
                ← Kembali ke Daftar
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- User Info -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-start gap-4">
                        @if($user->photo_path)
                            <img src="{{ asset('storage/' . $user->photo_path) }}" alt="{{ $user->name }}" class="w-24 h-24 rounded-full object-cover">
                        @else
                            <div class="w-24 h-24 rounded-full bg-gray-200 flex items-center justify-center text-gray-600 font-bold text-3xl">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                        @endif
                        
                        <div class="flex-1">
                            <h3 class="text-2xl font-bold">{{ $user->name }}</h3>
                            <p class="text-gray-600">@{{ $user->username }}</p>
                            
                            @if($user->bio)
                                <p class="mt-2 text-gray-700">{{ $user->bio }}</p>
                            @endif
                            
                            <div class="flex flex-wrap gap-2 mt-3">
                                @foreach($user->roles as $role)
                                    <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium">{{ $role->name }}</span>
                                @endforeach
                            </div>
                            
                            <div class="grid grid-cols-2 gap-4 mt-4 text-sm">
                                @if($user->phone)
                                    <div>
                                        <span class="text-gray-500">Telepon:</span>
                                        <span class="font-medium">{{ $user->phone }}</span>
                                    </div>
                                @endif
                                @if($user->whatsapp)
                                    <div>
                                        <span class="text-gray-500">WhatsApp:</span>
                                        <span class="font-medium">{{ $user->whatsapp }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Skills -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                        </svg>
                        Keahlian / Skills ({{ $skills->count() }})
                    </h3>
                    
                    @if($skills->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($skills as $skill)
                                <div class="border rounded-lg p-4 bg-blue-50">
                                    <h4 class="font-semibold text-gray-900">{{ $skill->nama_skill }}</h4>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span class="text-xs px-2 py-1 bg-blue-200 text-blue-900 rounded font-medium">
                                            {{ ucfirst($skill->tingkat_keahlian) }}
                                        </span>
                                    </div>
                                    @if($skill->deskripsi)
                                        <p class="text-sm text-gray-700 mt-2">{{ $skill->deskripsi }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-sm">Belum ada data keahlian.</p>
                    @endif
                </div>
            </div>

            <!-- Modal (Contributions) -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Modal (Uang / Alat) ({{ $modals->count() }})
                    </h3>
                    
                    @if($modals->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($modals as $modal)
                                <div class="border rounded-lg p-4 {{ $modal->jenis == 'uang' ? 'bg-green-50' : 'bg-indigo-50' }}">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <h4 class="font-semibold text-gray-900">{{ $modal->nama_item }}</h4>
                                            <span class="text-xs px-2 py-1 rounded mt-1 inline-block {{ $modal->jenis == 'uang' ? 'bg-green-200 text-green-900' : 'bg-indigo-200 text-indigo-900' }}">
                                                {{ ucfirst($modal->jenis) }}
                                            </span>
                                        </div>
                                        @if($modal->dapat_dipinjam)
                                            <span class="text-xs text-blue-600 font-medium">✓ Dapat dipinjam</span>
                                        @endif
                                    </div>
                                    @if($modal->jenis == 'uang' && $modal->jumlah_uang)
                                        <p class="text-sm font-medium text-gray-900 mt-2">Rp {{ number_format($modal->jumlah_uang, 0, ',', '.') }}</p>
                                    @endif
                                    @if($modal->deskripsi)
                                        <p class="text-sm text-gray-700 mt-2">{{ $modal->deskripsi }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-sm">Belum ada data modal.</p>
                    @endif
                </div>
            </div>

            <!-- Links -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                        </svg>
                        Link & Kontak Eksternal ({{ $links->count() }})
                    </h3>
                    
                    @if($links->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($links as $link)
                                <div class="border rounded-lg p-4 bg-purple-50">
                                    <h4 class="font-semibold text-gray-900">{{ $link->nama }}</h4>
                                    @if($link->bidang)
                                        <p class="text-sm text-gray-600 mt-1">{{ $link->bidang }}</p>
                                    @endif
                                    @if($link->url)
                                        <a href="{{ $link->url }}" target="_blank" class="text-sm text-blue-600 hover:underline flex items-center gap-1 mt-2">
                                            {{ Str::limit($link->url, 40) }}
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                            </svg>
                                        </a>
                                    @endif
                                    @if($link->contact)
                                        <p class="text-sm text-gray-700 mt-2">
                                            <span class="text-gray-500">Kontak:</span> {{ $link->contact }}
                                        </p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-sm">Belum ada data link.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
