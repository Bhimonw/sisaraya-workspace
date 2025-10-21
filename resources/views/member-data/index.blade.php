<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-900">
                    Data Anggota Saya
                </h2>
                <p class="text-sm text-gray-600 mt-1">Kelola informasi keahlian, kontribusi, dan link Anda</p>
            </div>
            <button @click="$dispatch('open-add-modal')" class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white px-6 py-3 rounded-xl text-sm font-semibold shadow-lg shadow-blue-500/30 hover:shadow-xl hover:shadow-blue-500/40 transition-all duration-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Tambah Data
            </button>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="bg-gradient-to-r from-green-50 to-emerald-50 border-l-4 border-green-500 px-6 py-4 rounded-lg shadow-sm" role="alert">
                    <div class="flex items-center gap-3">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="text-green-800 font-medium">{{ session('status') }}</span>
                    </div>
                </div>
            @endif

            <!-- Skills -->
            <div class="bg-white overflow-hidden shadow-md hover:shadow-xl transition-shadow duration-300 rounded-2xl border border-gray-100">
                <div class="bg-gradient-to-r from-blue-500 to-indigo-600 px-6 py-4">
                    <h3 class="text-lg font-bold text-white flex items-center gap-3">
                        <div class="bg-white/20 p-2 rounded-lg">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                            </svg>
                        </div>
                        <span>Keahlian & Skills</span>
                        <span class="ml-auto bg-white/20 px-3 py-1 rounded-full text-sm">{{ $skills->count() }}</span>
                    </h3>
                </div>
                
                <div class="p-6">
                    @if($skills->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($skills as $skill)
                                <div class="group relative bg-gradient-to-br from-blue-50 to-indigo-50 border border-blue-200 hover:border-blue-400 rounded-xl p-5 transition-all duration-200 hover:shadow-lg">
                                    <div class="flex justify-between items-start mb-3">
                                        <div class="flex-1">
                                            <h4 class="font-bold text-gray-900 text-lg mb-2">{{ $skill->nama_skill }}</h4>
                                            <div class="inline-flex items-center gap-2 bg-white px-3 py-1 rounded-full border border-blue-300">
                                                @php
                                                    $colors = [
                                                        'pemula' => 'text-yellow-600',
                                                        'menengah' => 'text-blue-600',
                                                        'mahir' => 'text-purple-600',
                                                        'expert' => 'text-red-600'
                                                    ];
                                                @endphp
                                                <span class="w-2 h-2 rounded-full {{ str_contains($skill->tingkat_keahlian, 'expert') ? 'bg-red-500' : (str_contains($skill->tingkat_keahlian, 'mahir') ? 'bg-purple-500' : (str_contains($skill->tingkat_keahlian, 'menengah') ? 'bg-blue-500' : 'bg-yellow-500')) }}"></span>
                                                <span class="text-sm font-semibold {{ $colors[$skill->tingkat_keahlian] ?? 'text-gray-600' }}">
                                                    {{ ucfirst($skill->tingkat_keahlian) }}
                                                </span>
                                            </div>
                                        </div>
                                        <form method="POST" action="{{ route('member-data.destroy', ['skill', $skill->id]) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" onclick="return confirm('Hapus skill ini?')" class="opacity-0 group-hover:opacity-100 transition-opacity duration-200 p-2 hover:bg-red-100 rounded-lg">
                                                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                    @if($skill->deskripsi)
                                        <p class="text-sm text-gray-700 leading-relaxed bg-white/50 p-3 rounded-lg">{{ $skill->deskripsi }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                            </svg>
                            <p class="text-gray-500 mb-3">Belum ada data keahlian</p>
                            <button @click="$dispatch('open-add-modal', 'skills')" class="inline-flex items-center gap-2 text-blue-600 hover:text-blue-700 font-semibold">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                Tambah sekarang
                            </button>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Modal (Contributions) -->
            <div class="bg-white overflow-hidden shadow-md hover:shadow-xl transition-shadow duration-300 rounded-2xl border border-gray-100">
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
                                <div class="group relative {{ $modal->jenis == 'uang' ? 'bg-gradient-to-br from-green-50 to-emerald-50 border-green-200 hover:border-green-400' : 'bg-gradient-to-br from-purple-50 to-pink-50 border-purple-200 hover:border-purple-400' }} border rounded-xl p-5 transition-all duration-200 hover:shadow-lg">
                                    <div class="flex justify-between items-start mb-3">
                                        <div class="flex-1">
                                            <div class="flex items-center gap-2 mb-3">
                                                <div class="{{ $modal->jenis == 'uang' ? 'bg-green-500' : 'bg-purple-500' }} p-2 rounded-lg">
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
                                                <span class="text-xs font-bold px-3 py-1 rounded-full {{ $modal->jenis == 'uang' ? 'bg-green-200 text-green-800' : 'bg-purple-200 text-purple-800' }}">
                                                    {{ strtoupper($modal->jenis) }}
                                                </span>
                                            </div>
                                            <h4 class="font-bold text-gray-900 text-lg mb-2">{{ $modal->nama_item }}</h4>
                                            @if($modal->jenis == 'uang' && $modal->jumlah_uang)
                                                <p class="text-2xl font-bold text-green-600 mb-2">Rp {{ number_format($modal->jumlah_uang, 0, ',', '.') }}</p>
                                            @endif
                                            @if($modal->dapat_dipinjam)
                                                <span class="inline-flex items-center gap-1 text-xs font-semibold px-2 py-1 bg-blue-100 text-blue-700 rounded-full">
                                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                    </svg>
                                                    Dapat dipinjam
                                                </span>
                                            @endif
                                        </div>
                                        <form method="POST" action="{{ route('member-data.destroy', ['modal', $modal->id]) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" onclick="return confirm('Hapus modal ini?')" class="opacity-0 group-hover:opacity-100 transition-opacity duration-200 p-2 hover:bg-red-100 rounded-lg">
                                                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                    @if($modal->deskripsi)
                                        <p class="text-sm text-gray-700 leading-relaxed bg-white/50 p-3 rounded-lg mt-3">{{ $modal->deskripsi }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p class="text-gray-500 mb-3">Belum ada data modal</p>
                            <button @click="$dispatch('open-add-modal', 'modal')" class="inline-flex items-center gap-2 text-green-600 hover:text-green-700 font-semibold">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                Tambah sekarang
                            </button>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Links -->
            <div class="bg-white overflow-hidden shadow-md hover:shadow-xl transition-shadow duration-300 rounded-2xl border border-gray-100">
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
                                <div class="group relative bg-gradient-to-br from-purple-50 to-pink-50 border border-purple-200 hover:border-purple-400 rounded-xl p-5 transition-all duration-200 hover:shadow-lg">
                                    <div class="flex justify-between items-start mb-3">
                                        <div class="flex-1">
                                            <div class="flex items-center gap-2 mb-2">
                                                <div class="bg-purple-500 p-2 rounded-lg">
                                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                                                    </svg>
                                                </div>
                                                <h4 class="font-bold text-gray-900 text-lg">{{ $link->nama }}</h4>
                                            </div>
                                            @if($link->bidang)
                                                <span class="inline-block text-xs font-semibold px-3 py-1 bg-purple-200 text-purple-800 rounded-full mb-2">{{ $link->bidang }}</span>
                                            @endif
                                            @if($link->url)
                                                <a href="{{ $link->url }}" target="_blank" class="block text-sm text-blue-600 hover:text-blue-800 font-medium bg-white p-2 rounded-lg mt-2 flex items-center gap-2 group/link">
                                                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                                    </svg>
                                                    <span class="truncate group-hover/link:underline">{{ $link->url }}</span>
                                                </a>
                                            @endif
                                            @if($link->contact)
                                                <div class="flex items-center gap-2 mt-2 text-sm text-gray-700 bg-white/50 p-2 rounded-lg">
                                                    <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                                    </svg>
                                                    <span>{{ $link->contact }}</span>
                                                </div>
                                            @endif
                                        </div>
                                        <form method="POST" action="{{ route('member-data.destroy', ['link', $link->id]) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" onclick="return confirm('Hapus link ini?')" class="opacity-0 group-hover:opacity-100 transition-opacity duration-200 p-2 hover:bg-red-100 rounded-lg">
                                                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                            </svg>
                            <p class="text-gray-500 mb-3">Belum ada data link</p>
                            <button @click="$dispatch('open-add-modal', 'links')" class="inline-flex items-center gap-2 text-purple-600 hover:text-purple-700 font-semibold">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                Tambah sekarang
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Include Modal Component -->
    @include('member-data._add-data-modal')
</x-app-layout>
