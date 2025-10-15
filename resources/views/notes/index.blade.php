<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Catatan Pribadi
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 rounded-lg bg-green-50 p-4 text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Create Note Form -->
            <div class="mb-6 overflow-hidden bg-white shadow-sm sm:rounded-lg" x-data="{ showForm: false }">
                <div class="p-6">
                    <button 
                        @click="showForm = !showForm"
                        class="inline-flex items-center rounded-lg bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">
                        <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Buat Catatan Baru
                    </button>

                    <div x-show="showForm" 
                         x-transition
                         class="mt-4">
                        <form action="{{ route('notes.store') }}" method="POST" class="space-y-4">
                            @csrf
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Judul</label>
                                <input type="text" 
                                       name="title" 
                                       required
                                       class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Isi Catatan</label>
                                <textarea name="content" 
                                          rows="5" 
                                          required
                                          class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Warna</label>
                                <div class="mt-2 flex gap-3">
                                    <label class="inline-flex items-center">
                                        <input type="radio" name="color" value="yellow" checked class="text-yellow-500 focus:ring-yellow-500">
                                        <span class="ml-2 flex h-8 w-8 items-center justify-center rounded-full bg-yellow-200">
                                            <svg class="h-5 w-5 text-yellow-700" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                            </svg>
                                        </span>
                                    </label>
                                    <label class="inline-flex items-center">
                                        <input type="radio" name="color" value="blue" class="text-blue-500 focus:ring-blue-500">
                                        <span class="ml-2 h-8 w-8 rounded-full bg-blue-200"></span>
                                    </label>
                                    <label class="inline-flex items-center">
                                        <input type="radio" name="color" value="green" class="text-green-500 focus:ring-green-500">
                                        <span class="ml-2 h-8 w-8 rounded-full bg-green-200"></span>
                                    </label>
                                    <label class="inline-flex items-center">
                                        <input type="radio" name="color" value="red" class="text-red-500 focus:ring-red-500">
                                        <span class="ml-2 h-8 w-8 rounded-full bg-red-200"></span>
                                    </label>
                                    <label class="inline-flex items-center">
                                        <input type="radio" name="color" value="purple" class="text-purple-500 focus:ring-purple-500">
                                        <span class="ml-2 h-8 w-8 rounded-full bg-purple-200"></span>
                                    </label>
                                </div>
                            </div>

                            <div class="flex gap-2">
                                <button type="submit" 
                                        class="rounded-lg bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">
                                    Simpan Catatan
                                </button>
                                <button type="button" 
                                        @click="showForm = false"
                                        class="rounded-lg bg-gray-300 px-4 py-2 text-gray-700 hover:bg-gray-400">
                                    Batal
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Notes Grid -->
            @if($notes->isEmpty())
                <div class="rounded-lg bg-white p-12 text-center shadow-sm">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada catatan</h3>
                    <p class="mt-1 text-sm text-gray-500">Mulai dengan membuat catatan pribadi pertama Anda.</p>
                </div>
            @else
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach($notes as $note)
                        <div 
                            x-data="{ 
                                editing: false,
                                title: '{{ $note->title }}',
                                content: `{{ addslashes($note->content) }}`
                            }"
                            class="relative rounded-lg p-4 shadow-md @if($note->color === 'yellow') bg-yellow-100 @elseif($note->color === 'blue') bg-blue-100 @elseif($note->color === 'green') bg-green-100 @elseif($note->color === 'red') bg-red-100 @else bg-purple-100 @endif">
                            
                            <!-- Pin Badge -->
                            @if($note->is_pinned)
                                <div class="absolute -right-2 -top-2 flex h-8 w-8 items-center justify-center rounded-full bg-yellow-500 text-white shadow-lg">
                                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10 3a1 1 0 011 1v5h3a1 1 0 110 2h-3v5a1 1 0 11-2 0v-5H6a1 1 0 110-2h3V4a1 1 0 011-1z"/>
                                    </svg>
                                </div>
                            @endif

                            <!-- Note Content -->
                            <div x-show="!editing">
                                <h3 class="mb-2 text-lg font-semibold text-gray-900">{{ $note->title }}</h3>
                                <p class="mb-4 whitespace-pre-wrap text-sm text-gray-700">{{ $note->content }}</p>
                                <div class="text-xs text-gray-500">
                                    {{ $note->updated_at->diffForHumans() }}
                                </div>
                            </div>

                            <!-- Edit Form -->
                            <form x-show="editing" 
                                  action="{{ route('notes.update', $note) }}" 
                                  method="POST"
                                  class="space-y-3">
                                @csrf
                                @method('PUT')
                                <input type="text" 
                                       x-model="title"
                                       name="title" 
                                       required
                                       class="w-full rounded border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                                <textarea x-model="content"
                                          name="content" 
                                          rows="5" 
                                          required
                                          class="w-full rounded border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                                <input type="hidden" name="color" value="{{ $note->color }}">
                                <div class="flex gap-2">
                                    <button type="submit" class="rounded bg-blue-600 px-3 py-1 text-xs text-white hover:bg-blue-700">
                                        Simpan
                                    </button>
                                    <button type="button" 
                                            @click="editing = false"
                                            class="rounded bg-gray-300 px-3 py-1 text-xs text-gray-700 hover:bg-gray-400">
                                        Batal
                                    </button>
                                </div>
                            </form>

                            <!-- Actions -->
                            <div x-show="!editing" class="mt-4 flex justify-end gap-2">
                                <!-- Pin/Unpin -->
                                <form action="{{ route('notes.togglePin', $note) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" 
                                            class="text-gray-600 hover:text-yellow-600"
                                            title="{{ $note->is_pinned ? 'Lepas Pin' : 'Sematkan' }}">
                                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M10 3a1 1 0 011 1v5h3a1 1 0 110 2h-3v5a1 1 0 11-2 0v-5H6a1 1 0 110-2h3V4a1 1 0 011-1z"/>
                                        </svg>
                                    </button>
                                </form>

                                <!-- Edit -->
                                <button @click="editing = true" 
                                        class="text-gray-600 hover:text-blue-600"
                                        title="Edit">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                                            class="text-gray-600 hover:text-red-600"
                                            title="Hapus">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
