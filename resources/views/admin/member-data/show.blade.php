<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-bold text-2xl bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent leading-tight">
                    {{ $user->name }}
                </h2>
                <p class="text-sm text-gray-600 mt-1">Detail lengkap data anggota</p>
            </div>
            <a href="{{ route('admin.member-data.index') }}" class="flex items-center gap-2 px-5 py-2.5 bg-white hover:bg-gray-50 text-gray-700 border-2 border-gray-300 rounded-xl font-semibold shadow-md hover:shadow-lg transition-all duration-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- User Info -->
            <div class="bg-gradient-to-br from-white to-blue-50 overflow-hidden shadow-xl rounded-2xl border-2 border-blue-100">
                <div class="bg-gradient-to-r from-blue-600 to-purple-600 px-6 py-4">
                    <h3 class="text-lg font-bold text-white flex items-center gap-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Profil Anggota
                    </h3>
                </div>
                <div class="p-8">
                    <div class="flex items-start gap-6">
                        @if($user->photo_path)
                            <img src="{{ asset('storage/' . $user->photo_path) }}" alt="{{ $user->name }}" 
                                 class="w-32 h-32 rounded-2xl object-cover shadow-xl ring-4 ring-white">
                        @else
                            <div class="w-32 h-32 rounded-2xl bg-gradient-to-br from-blue-400 to-purple-500 flex items-center justify-center text-white font-bold text-5xl shadow-xl ring-4 ring-white">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                        @endif
                        
                        <div class="flex-1">
                            <h3 class="text-3xl font-bold text-gray-900">{{ $user->name }}</h3>
                            <p class="text-lg text-gray-600 font-medium">@{{ $user->username }}</p>
                            
                            @if($user->bio)
                                <p class="mt-3 text-gray-700 bg-white p-4 rounded-xl border border-gray-200">{{ $user->bio }}</p>
                            @endif
                            
                            <div class="flex flex-wrap gap-2 mt-4">
                                @foreach($user->roles as $role)
                                    <span class="px-4 py-2 bg-gradient-to-r from-blue-500 to-purple-500 text-white rounded-xl text-sm font-bold shadow-md">
                                        {{ ucfirst($role->name) }}
                                    </span>
                                @endforeach
                            </div>
                            
                            @if($user->phone || $user->whatsapp)
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mt-4">
                                    @if($user->phone)
                                        <div class="bg-white p-4 rounded-xl border-2 border-gray-200 flex items-center gap-3">
                                            <div class="bg-green-100 p-3 rounded-xl">
                                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="text-xs text-gray-500 font-semibold">Telepon</p>
                                                <p class="font-bold text-gray-900">{{ $user->phone }}</p>
                                            </div>
                                        </div>
                                    @endif
                                    @if($user->whatsapp)
                                        <div class="bg-white p-4 rounded-xl border-2 border-gray-200 flex items-center gap-3">
                                            <div class="bg-green-100 p-3 rounded-xl">
                                                <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="text-xs text-gray-500 font-semibold">WhatsApp</p>
                                                <p class="font-bold text-gray-900">{{ $user->whatsapp }}</p>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Skills -->
            <div class="bg-white overflow-hidden shadow-xl hover:shadow-2xl transition-shadow duration-300 rounded-2xl border border-gray-100">
                <div class="bg-gradient-to-r from-blue-500 to-cyan-600 px-6 py-4">
                    <h3 class="text-lg font-bold text-white flex items-center gap-3">
                        <div class="bg-white/20 p-2 rounded-lg">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                            </svg>
                        </div>
                        <span>Keahlian / Skills</span>
                        <span class="ml-auto bg-white/20 px-3 py-1 rounded-full text-sm">{{ $skills->count() }}</span>
                    </h3>
                </div>
                
                <div class="p-6">
                    @if($skills->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($skills as $skill)
                                <div class="group bg-gradient-to-br from-blue-50 to-cyan-50 border-2 border-blue-200 hover:border-blue-400 rounded-xl p-5 transition-all duration-200 hover:shadow-lg">
                                    <div class="flex items-start gap-3 mb-3">
                                        <div class="bg-blue-500 p-2.5 rounded-lg shadow-md">
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                                            </svg>
                                        </div>
                                        <div class="flex-1">
                                            <h4 class="font-bold text-gray-900 text-lg">{{ $skill->nama_skill }}</h4>
                                            <span class="inline-block text-xs px-3 py-1.5 bg-blue-500 text-white rounded-full font-bold mt-2 shadow-sm">
                                                @if($skill->tingkat_keahlian == 'pemula')
                                                    🌱 Pemula
                                                @elseif($skill->tingkat_keahlian == 'menengah')
                                                    ⭐ Menengah
                                                @elseif($skill->tingkat_keahlian == 'mahir')
                                                    🔥 Mahir
                                                @else
                                                    💎 Expert
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                    @if($skill->deskripsi)
                                        <p class="text-sm text-gray-700 leading-relaxed bg-white/70 p-3 rounded-lg">{{ $skill->deskripsi }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                            </svg>
                            <p class="text-gray-500 font-medium">Belum ada data keahlian</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Modal (Contributions) -->
            <div class="bg-white overflow-hidden shadow-xl hover:shadow-2xl transition-shadow duration-300 rounded-2xl border border-gray-100">
                <div class="bg-gradient-to-r from-green-500 to-emerald-600 px-6 py-4">
                    <h3 class="text-lg font-bold text-white flex items-center gap-3">
                        <div class="bg-white/20 p-2 rounded-lg">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <span>Modal & Kontribusi</span>
                        <span class="ml-auto bg-white/20 px-3 py-1 rounded-full text-sm">{{ $modals->count() }}</span>
                    </h3>
                </div>
                
                <div class="p-6">
                    @if($modals->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($modals as $modal)
                                <div class="group {{ $modal->jenis == 'uang' ? 'bg-gradient-to-br from-green-50 to-emerald-50 border-green-200 hover:border-green-400' : 'bg-gradient-to-br from-purple-50 to-pink-50 border-purple-200 hover:border-purple-400' }} border-2 rounded-xl p-5 transition-all duration-200 hover:shadow-lg">
                                    <div class="flex items-start justify-between mb-3">
                                        <div class="flex-1">
                                            <div class="flex items-center gap-3 mb-3">
                                                <div class="{{ $modal->jenis == 'uang' ? 'bg-green-500' : 'bg-purple-500' }} p-2.5 rounded-lg shadow-md">
                                                    @if($modal->jenis == 'uang')
                                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                        </svg>
                                                    @else
                                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                                        </svg>
                                                    @endif
                                                </div>
                                                <span class="text-xs font-bold px-3 py-1.5 rounded-full {{ $modal->jenis == 'uang' ? 'bg-green-200 text-green-800' : 'bg-purple-200 text-purple-800' }} shadow-sm">
                                                    {{ strtoupper($modal->jenis) }}
                                                </span>
                                            </div>
                                            <h4 class="font-bold text-gray-900 text-lg mb-2">{{ $modal->nama_item }}</h4>
                                            @if($modal->jenis == 'uang' && $modal->jumlah_uang)
                                                <p class="text-2xl font-bold text-green-600 mb-2">Rp {{ number_format($modal->jumlah_uang, 0, ',', '.') }}</p>
                                            @endif
                                            @if($modal->dapat_dipinjam)
                                                <span class="inline-flex items-center gap-1 text-xs font-semibold px-3 py-1.5 bg-blue-100 text-blue-700 rounded-full shadow-sm">
                                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                    </svg>
                                                    Dapat dipinjam
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    @if($modal->deskripsi)
                                        <p class="text-sm text-gray-700 leading-relaxed bg-white/70 p-3 rounded-lg">{{ $modal->deskripsi }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p class="text-gray-500 font-medium">Belum ada data modal</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Links -->
            <div class="bg-white overflow-hidden shadow-xl hover:shadow-2xl transition-shadow duration-300 rounded-2xl border border-gray-100">
                <div class="bg-gradient-to-r from-purple-500 to-pink-600 px-6 py-4">
                    <h3 class="text-lg font-bold text-white flex items-center gap-3">
                        <div class="bg-white/20 p-2 rounded-lg">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                            </svg>
                        </div>
                        <span>Link & Kontak</span>
                        <span class="ml-auto bg-white/20 px-3 py-1 rounded-full text-sm">{{ $links->count() }}</span>
                    </h3>
                </div>
                
                <div class="p-6">
                    @if($links->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($links as $link)
                                <div class="group bg-gradient-to-br from-purple-50 to-pink-50 border-2 border-purple-200 hover:border-purple-400 rounded-xl p-5 transition-all duration-200 hover:shadow-lg">
                                    <div class="flex items-start gap-3 mb-3">
                                        <div class="bg-purple-500 p-2.5 rounded-lg shadow-md">
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                                            </svg>
                                        </div>
                                        <div class="flex-1">
                                            <h4 class="font-bold text-gray-900 text-lg">{{ $link->nama }}</h4>
                                            @if($link->bidang)
                                                <span class="inline-block text-xs font-semibold px-3 py-1.5 bg-purple-200 text-purple-800 rounded-full mt-2 shadow-sm">{{ $link->bidang }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    @if($link->url)
                                        <a href="{{ $link->url }}" target="_blank" class="block text-sm text-blue-600 hover:text-blue-800 font-medium bg-white p-3 rounded-lg mt-3 flex items-center gap-2 group/link hover:bg-blue-50 transition-colors">
                                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                            </svg>
                                            <span class="truncate group-hover/link:underline">{{ $link->url }}</span>
                                        </a>
                                    @endif
                                    @if($link->contact)
                                        <div class="flex items-center gap-2 mt-3 text-sm text-gray-700 bg-white/70 p-3 rounded-lg">
                                            <svg class="w-4 h-4 text-purple-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                            </svg>
                                            <span class="font-medium">{{ $link->contact }}</span>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                            </svg>
                            <p class="text-gray-500 font-medium">Belum ada data link</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
