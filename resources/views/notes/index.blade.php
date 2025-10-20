<x-app-layout>
    <div class="py-6" x-data="{ showCreateModal: false, filterColor: 'all', filterPinned: 'all' }">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            
            <!-- Modern Header with Gradient -->
            <div class="mb-8">
                <div class="bg-gradient-to-r from-purple-600 to-indigo-700 rounded-2xl shadow-xl p-8 text-white">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="bg-white bg-opacity-20 p-4 rounded-xl backdrop-blur-sm">
                                <svg class="h-10 w-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </div>
                            <div>
                                <h1 class="text-3xl font-bold">Catatan Pribadi</h1>
                                <p class="text-purple-100 mt-1">Kelola catatan dan ide kreatif Anda</p>
                            </div>
                        </div>
                        <button @click="showCreateModal = true"
                                class="inline-flex items-center gap-2 bg-white text-purple-600 px-6 py-3 rounded-xl hover:bg-purple-50 transition shadow-lg font-semibold hover:shadow-xl transform hover:scale-105">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Buat Catatan Baru
                        </button>
                    </div>
                </div>
            </div>

            @if(session('success'))
                <div class="mb-6 rounded-xl bg-green-50 border-2 border-green-200 p-4 text-green-800 flex items-center gap-3">
                    <svg class="h-5 w-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
            @endif

            <!-- Stats Cards -->
            <div class="mb-8 grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Total Notes -->
                <div class="bg-white rounded-xl shadow-md border border-gray-200 p-5 hover:shadow-lg transition">
                    <div class="flex items-center gap-3">
                        <div class="p-3 bg-purple-100 rounded-lg">
                            <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 font-medium">Total Catatan</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $notes->count() }}</p>
                        </div>
                    </div>
                </div>

                <!-- Pinned Notes -->
                <div class="bg-white rounded-xl shadow-md border border-gray-200 p-5 hover:shadow-lg transition">
                    <div class="flex items-center gap-3">
                        <div class="p-3 bg-yellow-100 rounded-lg">
                            <svg class="h-6 w-6 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 3a1 1 0 011 1v5h3a1 1 0 110 2h-3v5a1 1 0 11-2 0v-5H6a1 1 0 110-2h3V4a1 1 0 011-1z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 font-medium">Disematkan</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $notes->where('is_pinned', true)->count() }}</p>
                        </div>
                    </div>
                </div>

                <!-- Yellow Notes -->
                <div class="bg-white rounded-xl shadow-md border border-gray-200 p-5 hover:shadow-lg transition">
                    <div class="flex items-center gap-3">
                        <div class="p-3 bg-yellow-200 rounded-lg">
                            <svg class="h-6 w-6 text-yellow-700" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a1 1 0 110 2h-3a1 1 0 01-1-1v-2a1 1 0 00-1-1H9a1 1 0 00-1 1v2a1 1 0 01-1 1H4a1 1 0 110-2V4zm3 1h2v2H7V5zm2 4H7v2h2V9zm2-4h2v2h-2V5zm2 4h-2v2h2V9z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 font-medium">Kuning</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $notes->where('color', 'yellow')->count() }}</p>
                        </div>
                    </div>
                </div>

                <!-- Other Colors -->
                <div class="bg-white rounded-xl shadow-md border border-gray-200 p-5 hover:shadow-lg transition">
                    <div class="flex items-center gap-3">
                        <div class="p-3 bg-gradient-to-br from-blue-100 via-green-100 to-purple-100 rounded-lg">
                            <svg class="h-6 w-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 font-medium">Warna Lain</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $notes->whereIn('color', ['blue', 'green', 'red', 'purple'])->count() }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter Buttons -->
            <div class="mb-6 flex flex-wrap items-center gap-3">
                <span class="text-sm font-semibold text-gray-700">Filter:</span>
                
                <!-- Color Filter -->
                <div class="flex gap-2">
                    <button @click="filterColor = 'all'" 
                            :class="filterColor === 'all' ? 'bg-gray-800 text-white' : 'bg-white text-gray-700 hover:bg-gray-100'"
                            class="px-4 py-2 rounded-lg border border-gray-300 text-sm font-medium transition">
                        Semua Warna
                    </button>
                    <button @click="filterColor = 'yellow'" 
                            :class="filterColor === 'yellow' ? 'bg-yellow-500 text-white' : 'bg-yellow-100 text-yellow-700 hover:bg-yellow-200'"
                            class="px-4 py-2 rounded-lg text-sm font-medium transition flex items-center gap-1.5">
                        <span class="inline-block w-3 h-3 rounded-full bg-yellow-400 border border-yellow-600"></span>
                        Kuning
                    </button>
                    <button @click="filterColor = 'blue'" 
                            :class="filterColor === 'blue' ? 'bg-blue-500 text-white' : 'bg-blue-100 text-blue-700 hover:bg-blue-200'"
                            class="px-4 py-2 rounded-lg text-sm font-medium transition flex items-center gap-1.5">
                        <span class="inline-block w-3 h-3 rounded-full bg-blue-400 border border-blue-600"></span>
                        Biru
                    </button>
                    <button @click="filterColor = 'green'" 
                            :class="filterColor === 'green' ? 'bg-green-500 text-white' : 'bg-green-100 text-green-700 hover:bg-green-200'"
                            class="px-4 py-2 rounded-lg text-sm font-medium transition flex items-center gap-1.5">
                        <span class="inline-block w-3 h-3 rounded-full bg-green-400 border border-green-600"></span>
                        Hijau
                    </button>
                    <button @click="filterColor = 'red'" 
                            :class="filterColor === 'red' ? 'bg-red-500 text-white' : 'bg-red-100 text-red-700 hover:bg-red-200'"
                            class="px-4 py-2 rounded-lg text-sm font-medium transition flex items-center gap-1.5">
                        <span class="inline-block w-3 h-3 rounded-full bg-red-400 border border-red-600"></span>
                        Merah
                    </button>
                    <button @click="filterColor = 'purple'" 
                            :class="filterColor === 'purple' ? 'bg-purple-500 text-white' : 'bg-purple-100 text-purple-700 hover:bg-purple-200'"
                            class="px-4 py-2 rounded-lg text-sm font-medium transition flex items-center gap-1.5">
                        <span class="inline-block w-3 h-3 rounded-full bg-purple-400 border border-purple-600"></span>
                        Ungu
                    </button>
                </div>

                <!-- Pin Filter -->
                <div class="flex gap-2 ml-4">
                    <button @click="filterPinned = 'all'" 
                            :class="filterPinned === 'all' ? 'bg-gray-800 text-white' : 'bg-white text-gray-700 hover:bg-gray-100'"
                            class="px-4 py-2 rounded-lg border border-gray-300 text-sm font-medium transition">
                        Semua
                    </button>
                    <button @click="filterPinned = 'pinned'" 
                            :class="filterPinned === 'pinned' ? 'bg-yellow-500 text-white' : 'bg-white text-gray-700 hover:bg-gray-100'"
                            class="px-4 py-2 rounded-lg border border-gray-300 text-sm font-medium transition flex items-center gap-1">
                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 3a1 1 0 011 1v5h3a1 1 0 110 2h-3v5a1 1 0 11-2 0v-5H6a1 1 0 110-2h3V4a1 1 0 011-1z"/>
                        </svg>
                        Disematkan
                    </button>
                </div>
            </div>

            <!-- Create Note Modal -->
            <div x-show="showCreateModal" 
                 x-cloak
                 @click.self="showCreateModal = false"
                 @keydown.escape.window="showCreateModal = false"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 z-50 overflow-y-auto bg-black bg-opacity-50 backdrop-blur-sm flex items-center justify-center p-4"
                 role="dialog"
                 aria-modal="true">
                
                <div @click.stop
                     x-show="showCreateModal"
                     x-transition:enter="transition ease-out duration-300 transform"
                     x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                     x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-200 transform"
                     x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                     x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                     class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full">
                    
                    <!-- Modal Header -->
                    <div class="bg-gradient-to-r from-purple-600 to-indigo-700 px-6 py-4 flex items-center justify-between rounded-t-2xl">
                        <div class="flex items-center gap-3">
                            <div class="bg-white bg-opacity-20 p-2 rounded-lg">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-white">Buat Catatan Baru</h2>
                                <p class="text-purple-100 text-sm">Tulis ide dan catatan penting Anda</p>
                            </div>
                        </div>
                        <button @click="showCreateModal = false" 
                                class="text-white hover:bg-white hover:bg-opacity-20 p-2 rounded-lg transition">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Modal Body -->
                    <form action="{{ route('notes.store') }}" method="POST" class="p-6 space-y-5">
                            @csrf
                            
                            <!-- Judul -->
                            <div>
                                <label for="title" class="block text-sm font-bold text-gray-900 mb-2">
                                    <span class="flex items-center gap-2">
                                        <svg class="h-4 w-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                        </svg>
                                        Judul Catatan
                                        <span class="text-red-500">*</span>
                                    </span>
                                </label>
                                <input type="text" 
                                       id="title"
                                       name="title" 
                                       required
                                       placeholder="Contoh: Ide Proyek Baru, Meeting Notes, dll."
                                       class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-4 focus:ring-purple-500 focus:ring-opacity-30 focus:border-purple-500 transition">
                            </div>

                            <!-- Isi Catatan -->
                            <div>
                                <label for="content" class="block text-sm font-bold text-gray-900 mb-2">
                                    <span class="flex items-center gap-2">
                                        <svg class="h-4 w-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        Isi Catatan
                                        <span class="text-red-500">*</span>
                                    </span>
                                </label>
                                <textarea id="content"
                                          name="content" 
                                          rows="6" 
                                          required
                                          placeholder="Tulis catatan Anda di sini..."
                                          class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-4 focus:ring-purple-500 focus:ring-opacity-30 focus:border-purple-500 transition resize-none"></textarea>
                            </div>

                            <!-- Warna -->
                            <div>
                                <label class="block text-sm font-bold text-gray-900 mb-3">
                                    <span class="flex items-center gap-2">
                                        <svg class="h-4 w-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" />
                                        </svg>
                                        Pilih Warna Catatan
                                    </span>
                                </label>
                                <div class="flex gap-3">
                                    <label class="cursor-pointer group">
                                        <input type="radio" name="color" value="yellow" checked class="sr-only peer">
                                        <div class="w-12 h-12 rounded-xl bg-yellow-200 border-2 border-gray-300 peer-checked:border-yellow-500 peer-checked:ring-4 peer-checked:ring-yellow-500 peer-checked:ring-opacity-30 group-hover:scale-110 transition flex items-center justify-center">
                                            <span class="inline-block w-6 h-6 rounded-full bg-yellow-400 border-2 border-yellow-600"></span>
                                        </div>
                                    </label>
                                    <label class="cursor-pointer group">
                                        <input type="radio" name="color" value="blue" class="sr-only peer">
                                        <div class="w-12 h-12 rounded-xl bg-blue-200 border-2 border-gray-300 peer-checked:border-blue-500 peer-checked:ring-4 peer-checked:ring-blue-500 peer-checked:ring-opacity-30 group-hover:scale-110 transition flex items-center justify-center">
                                            <span class="inline-block w-6 h-6 rounded-full bg-blue-400 border-2 border-blue-600"></span>
                                        </div>
                                    </label>
                                    <label class="cursor-pointer group">
                                        <input type="radio" name="color" value="green" class="sr-only peer">
                                        <div class="w-12 h-12 rounded-xl bg-green-200 border-2 border-gray-300 peer-checked:border-green-500 peer-checked:ring-4 peer-checked:ring-green-500 peer-checked:ring-opacity-30 group-hover:scale-110 transition flex items-center justify-center">
                                            <span class="inline-block w-6 h-6 rounded-full bg-green-400 border-2 border-green-600"></span>
                                        </div>
                                    </label>
                                    <label class="cursor-pointer group">
                                        <input type="radio" name="color" value="red" class="sr-only peer">
                                        <div class="w-12 h-12 rounded-xl bg-red-200 border-2 border-gray-300 peer-checked:border-red-500 peer-checked:ring-4 peer-checked:ring-red-500 peer-checked:ring-opacity-30 group-hover:scale-110 transition flex items-center justify-center">
                                            <span class="inline-block w-6 h-6 rounded-full bg-red-400 border-2 border-red-600"></span>
                                        </div>
                                    </label>
                                    <label class="cursor-pointer group">
                                        <input type="radio" name="color" value="purple" class="sr-only peer">
                                        <div class="w-12 h-12 rounded-xl bg-purple-200 border-2 border-gray-300 peer-checked:border-purple-500 peer-checked:ring-4 peer-checked:ring-purple-500 peer-checked:ring-opacity-30 group-hover:scale-110 transition flex items-center justify-center">
                                            <span class="inline-block w-6 h-6 rounded-full bg-purple-400 border-2 border-purple-600"></span>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex items-center justify-end gap-3 pt-4 border-t-2 border-gray-200">
                                <button type="button" 
                                        @click="showCreateModal = false"
                                        class="px-5 py-2.5 border-2 border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition font-medium">
                                    Batal
                                </button>
                                <button type="submit"
                                        class="px-6 py-2.5 bg-gradient-to-r from-purple-600 to-indigo-700 text-white rounded-lg hover:from-purple-700 hover:to-indigo-800 transition font-semibold shadow-lg hover:shadow-xl transform hover:scale-105 flex items-center gap-2">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Simpan Catatan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Notes Grid -->
            @if($notes->isEmpty())
                <div class="rounded-2xl bg-white p-16 text-center shadow-xl border-2 border-dashed border-gray-300">
                    <div class="inline-flex p-4 bg-purple-100 rounded-full mb-4">
                        <svg class="h-16 w-16 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">Belum Ada Catatan</h3>
                    <p class="text-gray-600 mb-6">Mulai dengan membuat catatan pribadi pertama Anda</p>
                    <button @click="showCreateModal = true"
                            class="inline-flex items-center gap-2 bg-gradient-to-r from-purple-600 to-indigo-700 text-white px-6 py-3 rounded-xl hover:from-purple-700 hover:to-indigo-800 transition font-semibold shadow-lg">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Buat Catatan Pertama
                    </button>
                </div>
            @else
                <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                    @foreach($notes as $note)
                        <div 
                            x-data="{ 
                                editing: false,
                                title: '{{ $note->title }}',
                                content: `{{ addslashes($note->content) }}`,
                                color: '{{ $note->color }}',
                                isPinned: {{ $note->is_pinned ? 'true' : 'false' }}
                            }"
                            x-show="(filterColor === 'all' || filterColor === color) && (filterPinned === 'all' || (filterPinned === 'pinned' && isPinned))"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 scale-95"
                            x-transition:enter-end="opacity-100 scale-100"
                            class="relative rounded-2xl p-5 shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 @if($note->color === 'yellow') bg-gradient-to-br from-yellow-100 to-yellow-200 @elseif($note->color === 'blue') bg-gradient-to-br from-blue-100 to-blue-200 @elseif($note->color === 'green') bg-gradient-to-br from-green-100 to-green-200 @elseif($note->color === 'red') bg-gradient-to-br from-red-100 to-red-200 @else bg-gradient-to-br from-purple-100 to-purple-200 @endif border-2 @if($note->color === 'yellow') border-yellow-300 @elseif($note->color === 'blue') border-blue-300 @elseif($note->color === 'green') border-green-300 @elseif($note->color === 'red') border-red-300 @else border-purple-300 @endif">
                            
                            <!-- Pin Badge -->
                            @if($note->is_pinned)
                                <div class="absolute -right-3 -top-3 flex h-10 w-10 items-center justify-center rounded-full bg-gradient-to-br from-yellow-400 to-amber-500 text-white shadow-xl animate-pulse">
                                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10 3a1 1 0 011 1v5h3a1 1 0 110 2h-3v5a1 1 0 11-2 0v-5H6a1 1 0 110-2h3V4a1 1 0 011-1z"/>
                                    </svg>
                                </div>
                            @endif

                            <!-- Note Content -->
                            <div x-show="!editing" class="min-h-[160px]">
                                <h3 class="mb-3 text-xl font-bold text-gray-900 line-clamp-2">{{ $note->title }}</h3>
                                <p class="mb-4 whitespace-pre-wrap text-sm text-gray-700 leading-relaxed line-clamp-6">{{ $note->content }}</p>
                                <div class="flex items-center gap-2 text-xs text-gray-600 font-medium">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    {{ $note->updated_at->diffForHumans() }}
                                </div>
                            </div>

                            <!-- Edit Form -->
                            <form x-show="editing" 
                                  action="{{ route('notes.update', $note) }}" 
                                  method="POST"
                                  class="mt-5 space-y-4">
                                @csrf
                                @method('PUT')
                                
                                <div>
                                    <label class="block text-base font-bold text-gray-900 mb-2 flex items-center gap-2">
                                        <svg class="h-5 w-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                                        </svg>
                                        Judul Catatan
                                    </label>
                                    <input type="text" 
                                           x-model="title"
                                           name="title" 
                                           required
                                           class="w-full px-4 py-3 text-base border-2 @if($note->color === 'yellow') border-yellow-300 focus:border-yellow-500 focus:ring-yellow-200 @elseif($note->color === 'blue') border-blue-300 focus:border-blue-500 focus:ring-blue-200 @elseif($note->color === 'green') border-green-300 focus:border-green-500 focus:ring-green-200 @elseif($note->color === 'red') border-red-300 focus:border-red-500 focus:ring-red-200 @else border-purple-300 focus:border-purple-500 focus:ring-purple-200 @endif rounded-xl focus:ring-4 transition" 
                                           placeholder="Masukkan judul catatan...">
                                </div>

                                <div>
                                    <label class="block text-base font-bold text-gray-900 mb-2 flex items-center gap-2">
                                        <svg class="h-5 w-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        Konten
                                    </label>
                                    <textarea x-model="content"
                                              name="content" 
                                              rows="6" 
                                              required
                                              class="w-full px-4 py-3 text-base border-2 @if($note->color === 'yellow') border-yellow-300 focus:border-yellow-500 focus:ring-yellow-200 @elseif($note->color === 'blue') border-blue-300 focus:border-blue-500 focus:ring-blue-200 @elseif($note->color === 'green') border-green-300 focus:border-green-500 focus:ring-green-200 @elseif($note->color === 'red') border-red-300 focus:border-red-500 focus:ring-red-200 @else border-purple-300 focus:border-purple-500 focus:ring-purple-200 @endif rounded-xl focus:ring-4 transition" 
                                              placeholder="Tulis catatan kamu..."></textarea>
                                </div>

                                <div>
                                    <label class="block text-base font-bold text-gray-900 mb-3 flex items-center gap-2">
                                        <svg class="h-5 w-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>
                                        </svg>
                                        Pilih Warna
                                    </label>
                                    <div class="flex gap-3">
                                        <label class="cursor-pointer">
                                            <input type="radio" name="color" value="yellow" {{ $note->color === 'yellow' ? 'checked' : '' }} class="sr-only peer">
                                            <span class="flex items-center justify-center w-12 h-12 rounded-xl bg-yellow-200 peer-checked:ring-4 peer-checked:ring-yellow-400 hover:scale-110 transition transform">
                                                <span class="inline-block w-6 h-6 rounded-full bg-yellow-400 border-2 border-yellow-600"></span>
                                            </span>
                                        </label>
                                        <label class="cursor-pointer">
                                            <input type="radio" name="color" value="blue" {{ $note->color === 'blue' ? 'checked' : '' }} class="sr-only peer">
                                            <span class="flex items-center justify-center w-12 h-12 rounded-xl bg-blue-200 peer-checked:ring-4 peer-checked:ring-blue-400 hover:scale-110 transition transform">
                                                <span class="inline-block w-6 h-6 rounded-full bg-blue-400 border-2 border-blue-600"></span>
                                            </span>
                                        </label>
                                        <label class="cursor-pointer">
                                            <input type="radio" name="color" value="green" {{ $note->color === 'green' ? 'checked' : '' }} class="sr-only peer">
                                            <span class="flex items-center justify-center w-12 h-12 rounded-xl bg-green-200 peer-checked:ring-4 peer-checked:ring-green-400 hover:scale-110 transition transform">
                                                <span class="inline-block w-6 h-6 rounded-full bg-green-400 border-2 border-green-600"></span>
                                            </span>
                                        </label>
                                        <label class="cursor-pointer">
                                            <input type="radio" name="color" value="red" {{ $note->color === 'red' ? 'checked' : '' }} class="sr-only peer">
                                            <span class="flex items-center justify-center w-12 h-12 rounded-xl bg-red-200 peer-checked:ring-4 peer-checked:ring-red-400 hover:scale-110 transition transform">
                                                <span class="inline-block w-6 h-6 rounded-full bg-red-400 border-2 border-red-600"></span>
                                            </span>
                                        </label>
                                        <label class="cursor-pointer">
                                            <input type="radio" name="color" value="purple" {{ $note->color === 'purple' ? 'checked' : '' }} class="sr-only peer">
                                            <span class="flex items-center justify-center w-12 h-12 rounded-xl bg-purple-200 peer-checked:ring-4 peer-checked:ring-purple-400 hover:scale-110 transition transform">
                                                <span class="inline-block w-6 h-6 rounded-full bg-purple-400 border-2 border-purple-600"></span>
                                            </span>
                                        </label>
                                    </div>
                                </div>

                                <div class="flex gap-3 pt-2">
                                    <button type="submit" class="flex-1 px-6 py-3 bg-gradient-to-r @if($note->color === 'yellow') from-yellow-500 to-yellow-600 @elseif($note->color === 'blue') from-blue-500 to-blue-600 @elseif($note->color === 'green') from-green-500 to-green-600 @elseif($note->color === 'red') from-red-500 to-red-600 @else from-purple-500 to-purple-600 @endif text-white font-bold rounded-xl hover:shadow-xl transform hover:scale-105 transition flex items-center justify-center gap-2">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                                        </svg>
                                        Simpan Perubahan
                                    </button>
                                    <button type="button" 
                                            @click="editing = false"
                                            class="flex-1 px-6 py-3 bg-gray-200 text-gray-700 font-bold rounded-xl hover:bg-gray-300 hover:shadow-lg transform hover:scale-105 transition flex items-center justify-center gap-2">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                        Batal
                                    </button>
                                </div>
                            </form>

                            <!-- Actions -->
                            <div x-show="!editing" class="mt-5 pt-4 border-t-2 @if($note->color === 'yellow') border-yellow-300 @elseif($note->color === 'blue') border-blue-300 @elseif($note->color === 'green') border-green-300 @elseif($note->color === 'red') border-red-300 @else border-purple-300 @endif flex justify-end gap-2">
                                <!-- Pin/Unpin -->
                                <form action="{{ route('notes.togglePin', $note) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" 
                                            class="p-2.5 rounded-lg @if($note->color === 'yellow') bg-yellow-200 hover:bg-yellow-300 @elseif($note->color === 'blue') bg-blue-200 hover:bg-blue-300 @elseif($note->color === 'green') bg-green-200 hover:bg-green-300 @elseif($note->color === 'red') bg-red-200 hover:bg-red-300 @else bg-purple-200 hover:bg-purple-300 @endif text-gray-700 hover:text-gray-900 transition transform hover:scale-110"
                                            title="{{ $note->is_pinned ? 'Lepas Pin' : 'Sematkan' }}">
                                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M10 3a1 1 0 011 1v5h3a1 1 0 110 2h-3v5a1 1 0 11-2 0v-5H6a1 1 0 110-2h3V4a1 1 0 011-1z"/>
                                        </svg>
                                    </button>
                                </form>

                                <!-- Edit -->
                                <button @click="editing = true" 
                                        class="p-2.5 rounded-lg @if($note->color === 'yellow') bg-yellow-200 hover:bg-yellow-300 @elseif($note->color === 'blue') bg-blue-200 hover:bg-blue-300 @elseif($note->color === 'green') bg-green-200 hover:bg-green-300 @elseif($note->color === 'red') bg-red-200 hover:bg-red-300 @else bg-purple-200 hover:bg-purple-300 @endif text-gray-700 hover:text-gray-900 transition transform hover:scale-110"
                                        title="Edit">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>

                                <!-- Delete -->
                                <form action="{{ route('notes.destroy', $note) }}" 
                                      method="POST" 
                                      onsubmit="return confirm('Hapus catatan ini?')"
                                      class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="p-2.5 rounded-lg bg-red-500 hover:bg-red-600 text-white transition transform hover:scale-110"
                                            title="Hapus">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
