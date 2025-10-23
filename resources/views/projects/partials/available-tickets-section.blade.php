{{-- Available Tickets Section --}}
@props(['availableTickets'])

<div class="bg-white rounded-xl shadow-lg border border-blue-100 overflow-hidden hover:shadow-xl transition-shadow duration-300">
    {{-- Header --}}
    <div class="bg-gradient-to-r from-blue-600 via-cyan-600 to-blue-700 px-6 py-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-white/20 backdrop-blur-sm rounded-lg">
                    <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-bold text-white text-lg">Tiket Tersedia untuk Anda</h3>
                    <p class="text-blue-100 text-xs">Tiket yang bisa Anda ambil atau sedang Anda kerjakan</p>
                </div>
            </div>
            <span class="px-4 py-2 bg-white/20 backdrop-blur-sm rounded-full text-white font-bold text-sm border border-white/30">
                {{ $availableTickets->count() }} Tiket
            </span>
        </div>
    </div>
    
    {{-- Content --}}
    <div class="p-6 bg-gradient-to-br from-gray-50 to-blue-50/30">
        @if($availableTickets->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 max-h-[500px] overflow-y-auto pr-2 custom-scrollbar">
                @foreach($availableTickets as $ticket)
                    @include('projects.partials.ticket-card', ['ticket' => $ticket])
                @endforeach
            </div>
        @else
            <div class="text-center py-12">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-blue-100 to-cyan-100 rounded-full mb-4">
                    <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <p class="text-sm text-gray-600 font-semibold mb-1">Tidak ada tiket tersedia</p>
                <p class="text-xs text-gray-400">Semua tiket sudah diklaim atau tidak ada tiket untuk role Anda</p>
            </div>
        @endif
    </div>
</div>
