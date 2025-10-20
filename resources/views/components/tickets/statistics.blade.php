{{-- Ticket Statistics Cards Component --}}
<div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
    {{-- Total Tiket --}}
    <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-5 text-white transform hover:scale-105 transition-transform duration-200">
        <div class="flex items-center justify-between mb-3">
            <div class="p-2.5 bg-white bg-opacity-20 rounded-lg backdrop-blur-sm">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <span class="text-3xl font-extrabold">{{ $totalTickets }}</span>
        </div>
        <div class="text-sm font-medium text-blue-100">Total Tiket</div>
    </div>

    {{-- Belum Diambil --}}
    <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-5 text-white transform hover:scale-105 transition-transform duration-200">
        <div class="flex items-center justify-between mb-3">
            <div class="p-2.5 bg-white bg-opacity-20 rounded-lg backdrop-blur-sm">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <span class="text-3xl font-extrabold">{{ $unclaimedTickets }}</span>
        </div>
        <div class="text-sm font-medium text-purple-100">Belum Diambil</div>
    </div>

    {{-- Berjalan --}}
    <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-5 text-white transform hover:scale-105 transition-transform duration-200">
        <div class="flex items-center justify-between mb-3">
            <div class="p-2.5 bg-white bg-opacity-20 rounded-lg backdrop-blur-sm">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
            </div>
            <span class="text-3xl font-extrabold">{{ $activeTickets }}</span>
        </div>
        <div class="text-sm font-medium text-green-100">Berjalan</div>
    </div>

    {{-- Selesai --}}
    <div class="bg-gradient-to-br from-teal-500 to-teal-600 rounded-xl shadow-lg p-5 text-white transform hover:scale-105 transition-transform duration-200">
        <div class="flex items-center justify-between mb-3">
            <div class="p-2.5 bg-white bg-opacity-20 rounded-lg backdrop-blur-sm">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <span class="text-3xl font-extrabold">{{ $completedTickets }}</span>
        </div>
        <div class="text-sm font-medium text-teal-100">Selesai</div>
    </div>

    {{-- Blackout --}}
    <div class="bg-gradient-to-br from-gray-600 to-gray-700 rounded-xl shadow-lg p-5 text-white transform hover:scale-105 transition-transform duration-200">
        <div class="flex items-center justify-between mb-3">
            <div class="p-2.5 bg-white bg-opacity-20 rounded-lg backdrop-blur-sm">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                </svg>
            </div>
            <span class="text-3xl font-extrabold">{{ $blackoutTickets }}</span>
        </div>
        <div class="text-sm font-medium text-gray-100">Blackout</div>
    </div>
</div>
