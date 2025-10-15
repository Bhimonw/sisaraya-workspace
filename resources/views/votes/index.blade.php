<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Voting
            </h2>
            <a href="{{ route('votes.create') }}" 
               class="inline-flex items-center rounded-lg bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">
                <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Buat Voting Baru
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 rounded-lg bg-green-50 p-4 text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Active Votes -->
            <div class="mb-8">
                <h3 class="mb-4 text-lg font-semibold text-gray-900">Voting Aktif</h3>
                
                @if($activeVotes->isEmpty())
                    <div class="rounded-lg bg-white p-8 text-center shadow-sm">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                        </svg>
                        <p class="mt-2 text-sm text-gray-500">Tidak ada voting aktif saat ini</p>
                    </div>
                @else
                    <div class="grid gap-4 sm:grid-cols-2">
                        @foreach($activeVotes as $vote)
                            <div class="rounded-lg bg-white p-6 shadow-sm">
                                <div class="mb-4">
                                    <h4 class="text-lg font-semibold text-gray-900">{{ $vote->title }}</h4>
                                    @if($vote->description)
                                        <p class="mt-1 text-sm text-gray-600">{{ $vote->description }}</p>
                                    @endif
                                </div>

                                <div class="mb-4 flex flex-wrap gap-2 text-xs">
                                    @if($vote->allow_multiple)
                                        <span class="rounded-full bg-blue-100 px-2 py-1 text-blue-800">Pilihan Ganda</span>
                                    @else
                                        <span class="rounded-full bg-gray-100 px-2 py-1 text-gray-800">Pilihan Tunggal</span>
                                    @endif
                                    
                                    @if($vote->is_anonymous)
                                        <span class="rounded-full bg-purple-100 px-2 py-1 text-purple-800">Anonim</span>
                                    @endif

                                    @if($vote->closes_at)
                                        <span class="rounded-full bg-orange-100 px-2 py-1 text-orange-800">
                                            Tutup: {{ $vote->closes_at->format('d M Y H:i') }}
                                        </span>
                                    @endif
                                </div>

                                <div class="mb-4 text-sm text-gray-600">
                                    <div>Dibuat: {{ $vote->creator->name }}</div>
                                    <div>Total Suara: {{ $vote->responses->count() }}</div>
                                </div>

                                <a href="{{ route('votes.show', $vote) }}" 
                                   class="inline-block rounded-lg bg-blue-600 px-4 py-2 text-sm text-white hover:bg-blue-700">
                                    @if($vote->hasVoted(auth()->user()))
                                        Lihat Hasil
                                    @else
                                        Berikan Suara
                                    @endif
                                </a>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Closed Votes -->
            <div>
                <h3 class="mb-4 text-lg font-semibold text-gray-900">Voting Selesai</h3>
                
                @if($closedVotes->isEmpty())
                    <div class="rounded-lg bg-white p-8 text-center shadow-sm">
                        <p class="text-sm text-gray-500">Belum ada voting yang selesai</p>
                    </div>
                @else
                    <div class="grid gap-4 sm:grid-cols-2">
                        @foreach($closedVotes as $vote)
                            <div class="rounded-lg bg-gray-50 p-6 shadow-sm">
                                <div class="mb-4">
                                    <div class="mb-2 flex items-center justify-between">
                                        <h4 class="text-lg font-semibold text-gray-700">{{ $vote->title }}</h4>
                                        <span class="rounded-full bg-gray-600 px-2 py-1 text-xs text-white">SELESAI</span>
                                    </div>
                                    @if($vote->description)
                                        <p class="text-sm text-gray-500">{{ $vote->description }}</p>
                                    @endif
                                </div>

                                <div class="mb-4 text-sm text-gray-600">
                                    <div>Total Suara: {{ $vote->responses->count() }}</div>
                                </div>

                                <a href="{{ route('votes.show', $vote) }}" 
                                   class="inline-block rounded-lg bg-gray-600 px-4 py-2 text-sm text-white hover:bg-gray-700">
                                    Lihat Hasil
                                </a>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
