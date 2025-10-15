<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ $vote->title }}
            </h2>
            @can('update', $vote)
                @if(!$vote->isClosed())
                    <form action="{{ route('votes.close', $vote) }}" 
                          method="POST"
                          onsubmit="return confirm('Tutup voting ini?')">
                        @csrf
                        <button type="submit" 
                                class="rounded-lg bg-red-600 px-4 py-2 text-white hover:bg-red-700">
                            Tutup Voting
                        </button>
                    </form>
                @endif
            @endcan
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 rounded-lg bg-green-50 p-4 text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 rounded-lg bg-red-50 p-4 text-red-800">
                    {{ session('error') }}
                </div>
            @endif

            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <!-- Vote Info -->
                    <div class="mb-6">
                        @if($vote->description)
                            <p class="mb-4 text-gray-700">{{ $vote->description }}</p>
                        @endif

                        <div class="flex flex-wrap gap-2 text-sm">
                            <span class="rounded-full bg-gray-100 px-3 py-1 text-gray-800">
                                Dibuat oleh: {{ $vote->creator->name }}
                            </span>
                            
                            @if($vote->allow_multiple)
                                <span class="rounded-full bg-blue-100 px-3 py-1 text-blue-800">Pilihan Ganda</span>
                            @else
                                <span class="rounded-full bg-gray-100 px-3 py-1 text-gray-800">Pilihan Tunggal</span>
                            @endif
                            
                            @if($vote->is_anonymous)
                                <span class="rounded-full bg-purple-100 px-3 py-1 text-purple-800">Anonim</span>
                            @endif

                            @if($vote->isClosed())
                                <span class="rounded-full bg-red-100 px-3 py-1 text-red-800">DITUTUP</span>
                            @elseif($vote->closes_at)
                                <span class="rounded-full bg-orange-100 px-3 py-1 text-orange-800">
                                    Tutup: {{ $vote->closes_at->format('d M Y H:i') }}
                                </span>
                            @else
                                <span class="rounded-full bg-green-100 px-3 py-1 text-green-800">AKTIF</span>
                            @endif
                        </div>
                    </div>

                    <!-- Voting Form or Results -->
                    @if(!$vote->isClosed() && !$hasVoted)
                        <div class="mb-6 rounded-lg bg-blue-50 p-6">
                            <h3 class="mb-4 text-lg font-semibold text-gray-900">Berikan Suara Anda</h3>
                            <form action="{{ route('votes.cast', $vote) }}" method="POST" class="space-y-3">
                                @csrf
                                @foreach($vote->options as $option)
                                    <label class="flex cursor-pointer items-center rounded-lg border border-gray-300 p-4 hover:bg-blue-100">
                                        <input type="{{ $vote->allow_multiple ? 'checkbox' : 'radio' }}" 
                                               name="option_ids[]" 
                                               value="{{ $option->id }}"
                                               {{ $vote->allow_multiple ? '' : 'required' }}
                                               class="mr-3 h-5 w-5 text-blue-600 focus:ring-blue-500">
                                        <span class="text-gray-900">{{ $option->option_text }}</span>
                                    </label>
                                @endforeach

                                <button type="submit" 
                                        class="mt-4 rounded-lg bg-blue-600 px-6 py-3 text-white hover:bg-blue-700">
                                    Kirim Suara
                                </button>
                            </form>
                        </div>
                    @endif

                    <!-- Results -->
                    @if($vote->show_results || $vote->isClosed() || $hasVoted)
                        <div>
                            <h3 class="mb-4 text-lg font-semibold text-gray-900">
                                Hasil Voting
                                <span class="text-sm font-normal text-gray-600">({{ $vote->responses->count() }} suara)</span>
                            </h3>

                            <div class="space-y-4">
                                @foreach($vote->options as $option)
                                    @php
                                        $voteCount = $option->responses->count();
                                        $totalVotes = $vote->responses->count();
                                        $percentage = $totalVotes > 0 ? ($voteCount / $totalVotes) * 100 : 0;
                                        $isUserChoice = in_array($option->id, $userVotes);
                                    @endphp
                                    
                                    <div class="rounded-lg border p-4 {{ $isUserChoice ? 'border-blue-500 bg-blue-50' : 'border-gray-300' }}">
                                        <div class="mb-2 flex items-center justify-between">
                                            <span class="font-medium text-gray-900">
                                                {{ $option->option_text }}
                                                @if($isUserChoice)
                                                    <span class="ml-2 text-xs text-blue-600">(Pilihan Anda)</span>
                                                @endif
                                            </span>
                                            <span class="text-sm font-semibold text-gray-700">
                                                {{ $voteCount }} suara ({{ number_format($percentage, 1) }}%)
                                            </span>
                                        </div>
                                        
                                        <!-- Progress Bar -->
                                        <div class="h-4 overflow-hidden rounded-full bg-gray-200">
                                            <div class="h-full bg-blue-600 transition-all duration-500" 
                                                 style="width: {{ $percentage }}%"></div>
                                        </div>

                                        <!-- Voter List (if not anonymous) -->
                                        @if(!$vote->is_anonymous && $voteCount > 0)
                                            <div class="mt-2">
                                                <p class="text-xs text-gray-600">Dipilih oleh:</p>
                                                <div class="mt-1 flex flex-wrap gap-1">
                                                    @foreach($option->responses as $response)
                                                        <span class="rounded-full bg-gray-100 px-2 py-1 text-xs text-gray-700">
                                                            {{ $response->user->name }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @elseif(!$vote->show_results && !$hasVoted)
                        <div class="rounded-lg bg-gray-100 p-8 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <p class="mt-2 text-gray-600">Hasil akan ditampilkan setelah Anda memberikan suara</p>
                        </div>
                    @endif

                    <div class="mt-6">
                        <a href="{{ route('votes.index') }}" 
                           class="inline-flex items-center text-sm text-blue-600 hover:text-blue-700">
                            <svg class="mr-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            Kembali ke Daftar Voting
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
