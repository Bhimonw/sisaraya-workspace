<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Data Anggota Saya
            </h2>
            <a href="{{ route('member-data.create') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium">
                + Tambah Data
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('status') }}</span>
                </div>
            @endif

            <!-- Skills -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                        </svg>
                        Keahlian / Skills
                    </h3>
                    
                    @if($skills->count() > 0)
                        <div class="space-y-3">
                            @foreach($skills as $skill)
                                <div class="border rounded-lg p-4 flex justify-between items-start" x-data="{ editing: false }">
                                    <div class="flex-1">
                                        <h4 class="font-semibold">{{ $skill->nama_skill }}</h4>
                                        <p class="text-sm text-gray-600">
                                            Tingkat: <span class="font-medium">{{ ucfirst($skill->tingkat_keahlian) }}</span>
                                        </p>
                                        @if($skill->deskripsi)
                                            <p class="text-sm text-gray-500 mt-1">{{ $skill->deskripsi }}</p>
                                        @endif
                                    </div>
                                    <form method="POST" action="{{ route('member-data.destroy', ['skill', $skill->id]) }}" class="ml-4">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" onclick="return confirm('Hapus skill ini?')" class="text-red-600 hover:text-red-800 text-sm">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-sm">Belum ada data keahlian. <a href="{{ route('member-data.create') }}" class="text-blue-600 hover:underline">Tambah sekarang</a></p>
                    @endif
                </div>
            </div>

            <!-- Modal (Contributions) -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Modal (Uang / Alat)
                    </h3>
                    
                    @if($modals->count() > 0)
                        <div class="space-y-3">
                            @foreach($modals as $modal)
                                <div class="border rounded-lg p-4 flex justify-between items-start">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2">
                                            <h4 class="font-semibold">{{ $modal->nama_item }}</h4>
                                            <span class="text-xs px-2 py-1 rounded {{ $modal->jenis == 'uang' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                                                {{ ucfirst($modal->jenis) }}
                                            </span>
                                        </div>
                                        @if($modal->jenis == 'uang' && $modal->jumlah_uang)
                                            <p class="text-sm text-gray-600 mt-1">Jumlah: Rp {{ number_format($modal->jumlah_uang, 0, ',', '.') }}</p>
                                        @endif
                                        @if($modal->deskripsi)
                                            <p class="text-sm text-gray-500 mt-1">{{ $modal->deskripsi }}</p>
                                        @endif
                                        @if($modal->dapat_dipinjam)
                                            <span class="text-xs text-blue-600 mt-1 inline-block">✓ Dapat dipinjam</span>
                                        @endif
                                    </div>
                                    <form method="POST" action="{{ route('member-data.destroy', ['modal', $modal->id]) }}" class="ml-4">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" onclick="return confirm('Hapus modal ini?')" class="text-red-600 hover:text-red-800 text-sm">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-sm">Belum ada data modal. <a href="{{ route('member-data.create') }}" class="text-blue-600 hover:underline">Tambah sekarang</a></p>
                    @endif
                </div>
            </div>

            <!-- Links -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                        </svg>
                        Link & Kontak Eksternal
                    </h3>
                    
                    @if($links->count() > 0)
                        <div class="space-y-3">
                            @foreach($links as $link)
                                <div class="border rounded-lg p-4 flex justify-between items-start">
                                    <div class="flex-1">
                                        <h4 class="font-semibold">{{ $link->nama }}</h4>
                                        @if($link->bidang)
                                            <p class="text-sm text-gray-600">Bidang: {{ $link->bidang }}</p>
                                        @endif
                                        @if($link->url)
                                            <a href="{{ $link->url }}" target="_blank" class="text-sm text-blue-600 hover:underline flex items-center gap-1 mt-1">
                                                {{ $link->url }}
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                                </svg>
                                            </a>
                                        @endif
                                        @if($link->contact)
                                            <p class="text-sm text-gray-500 mt-1">Kontak: {{ $link->contact }}</p>
                                        @endif
                                    </div>
                                    <form method="POST" action="{{ route('member-data.destroy', ['link', $link->id]) }}" class="ml-4">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" onclick="return confirm('Hapus link ini?')" class="text-red-600 hover:text-red-800 text-sm">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-sm">Belum ada data link. <a href="{{ route('member-data.create') }}" class="text-blue-600 hover:underline">Tambah sekarang</a></p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
