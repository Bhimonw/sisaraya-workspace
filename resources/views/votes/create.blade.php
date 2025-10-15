<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Buat Voting Baru
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('votes.store') }}" 
                          method="POST" 
                          x-data="{ 
                              options: ['', ''],
                              addOption() { this.options.push('') },
                              removeOption(index) { this.options.splice(index, 1) }
                          }"
                          class="space-y-6">
                        @csrf

                        <!-- Title -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Judul Voting *</label>
                            <input type="text" 
                                   name="title" 
                                   required
                                   value="{{ old('title') }}"
                                   class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('title')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Deskripsi</label>
                            <textarea name="description" 
                                      rows="3"
                                      class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('description') }}</textarea>
                        </div>

                        <!-- Options -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Pilihan Voting *</label>
                            <div class="mt-2 space-y-2">
                                <template x-for="(option, index) in options" :key="index">
                                    <div class="flex gap-2">
                                        <input type="text" 
                                               :name="'options[' + index + ']'"
                                               x-model="options[index]"
                                               required
                                               :placeholder="'Pilihan ' + (index + 1)"
                                               class="block flex-1 rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        <button type="button" 
                                                @click="removeOption(index)"
                                                x-show="options.length > 2"
                                                class="rounded-lg bg-red-100 px-3 py-2 text-red-700 hover:bg-red-200">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>
                                    </div>
                                </template>
                            </div>
                            <button type="button" 
                                    @click="addOption"
                                    class="mt-2 inline-flex items-center text-sm text-blue-600 hover:text-blue-700">
                                <svg class="mr-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                Tambah Pilihan
                            </button>
                            @error('options')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Settings -->
                        <div class="space-y-3 rounded-lg bg-gray-50 p-4">
                            <h3 class="font-medium text-gray-900">Pengaturan</h3>
                            
                            <label class="flex items-center">
                                <input type="checkbox" 
                                       name="allow_multiple" 
                                       value="1"
                                       {{ old('allow_multiple') ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">Izinkan memilih lebih dari satu pilihan</span>
                            </label>

                            <label class="flex items-center">
                                <input type="checkbox" 
                                       name="show_results" 
                                       value="1"
                                       {{ old('show_results', true) ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">Tampilkan hasil sebelum voting ditutup</span>
                            </label>

                            <label class="flex items-center">
                                <input type="checkbox" 
                                       name="is_anonymous" 
                                       value="1"
                                       {{ old('is_anonymous') ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">Voting anonim (sembunyikan nama pemilih)</span>
                            </label>
                        </div>

                        <!-- Close Date -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Batas Waktu (Opsional)</label>
                            <input type="datetime-local" 
                                   name="closes_at"
                                   value="{{ old('closes_at') }}"
                                   class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <p class="mt-1 text-xs text-gray-500">Kosongkan jika tidak ada batas waktu</p>
                        </div>

                        <!-- Submit -->
                        <div class="flex gap-3">
                            <button type="submit" 
                                    class="rounded-lg bg-blue-600 px-6 py-2 text-white hover:bg-blue-700">
                                Buat Voting
                            </button>
                            <a href="{{ route('votes.index') }}" 
                               class="rounded-lg bg-gray-300 px-6 py-2 text-gray-700 hover:bg-gray-400">
                                Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
