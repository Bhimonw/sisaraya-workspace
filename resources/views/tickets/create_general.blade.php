<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Buat Tiket Umum untuk Anggota
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <!-- Info Box -->
                    <div class="mb-6 rounded-lg bg-blue-50 p-4">
                        <div class="flex">
                            <svg class="h-5 w-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-blue-800">Tiket Umum untuk Role Utama</h3>
                                <p class="mt-2 text-sm text-blue-700">
                                    Fitur ini memungkinkan PM untuk membuat tiket yang disebar ke semua anggota dengan role tertentu. 
                                    Tiket akan muncul di halaman "Tiketku" untuk setiap anggota dengan role yang dipilih, dan mereka bisa mengambil (claim) tiket tersebut.
                                </p>
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('tickets.storeGeneral') }}" 
                          method="POST"
                          x-data="{ selectedRoles: ['member'] }"
                          class="space-y-6">
                        @csrf

                        <!-- Title -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Judul Tiket *</label>
                            <input type="text" 
                                   name="title" 
                                   required
                                   value="{{ old('title') }}"
                                   placeholder="Contoh: Persiapan Rapat Koordinasi Bulanan"
                                   class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('title')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Deskripsi</label>
                            <textarea name="description" 
                                      rows="4"
                                      placeholder="Jelaskan detail tugas, ekspektasi, dan deliverables..."
                                      class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Target Roles -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-3">Target Role * (Pilih minimal 1)</label>
                            <div class="space-y-2">
                                <label class="flex items-center rounded-lg border border-gray-300 p-3 hover:bg-gray-50 cursor-pointer">
                                    <input type="checkbox" 
                                           name="target_roles[]" 
                                           value="member"
                                           x-model="selectedRoles"
                                           checked
                                           class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <div class="ml-3">
                                        <span class="font-medium text-gray-900">Member</span>
                                        <p class="text-xs text-gray-500">Anggota umum organisasi</p>
                                    </div>
                                </label>

                                <label class="flex items-center rounded-lg border border-gray-300 p-3 hover:bg-gray-50 cursor-pointer">
                                    <input type="checkbox" 
                                           name="target_roles[]" 
                                           value="pm"
                                           x-model="selectedRoles"
                                           class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <div class="ml-3">
                                        <span class="font-medium text-gray-900">Project Manager</span>
                                        <p class="text-xs text-gray-500">Pengelola proyek</p>
                                    </div>
                                </label>

                                <label class="flex items-center rounded-lg border border-gray-300 p-3 hover:bg-gray-50 cursor-pointer">
                                    <input type="checkbox" 
                                           name="target_roles[]" 
                                           value="bendahara"
                                           x-model="selectedRoles"
                                           class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <div class="ml-3">
                                        <span class="font-medium text-gray-900">Bendahara</span>
                                        <p class="text-xs text-gray-500">Pengelola keuangan</p>
                                    </div>
                                </label>

                                <label class="flex items-center rounded-lg border border-gray-300 p-3 hover:bg-gray-50 cursor-pointer">
                                    <input type="checkbox" 
                                           name="target_roles[]" 
                                           value="sekretaris"
                                           x-model="selectedRoles"
                                           class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <div class="ml-3">
                                        <span class="font-medium text-gray-900">Sekretaris</span>
                                        <p class="text-xs text-gray-500">Administrasi dan dokumentasi</p>
                                    </div>
                                </label>

                                <label class="flex items-center rounded-lg border border-gray-300 p-3 hover:bg-gray-50 cursor-pointer">
                                    <input type="checkbox" 
                                           name="target_roles[]" 
                                           value="hr"
                                           x-model="selectedRoles"
                                           class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <div class="ml-3">
                                        <span class="font-medium text-gray-900">Human Resources</span>
                                        <p class="text-xs text-gray-500">Manajemen SDM</p>
                                    </div>
                                </label>

                                <label class="flex items-center rounded-lg border border-gray-300 p-3 hover:bg-gray-50 cursor-pointer">
                                    <input type="checkbox" 
                                           name="target_roles[]" 
                                           value="kewirausahaan"
                                           x-model="selectedRoles"
                                           class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <div class="ml-3">
                                        <span class="font-medium text-gray-900">Kewirausahaan</span>
                                        <p class="text-xs text-gray-500">Pengembangan bisnis</p>
                                    </div>
                                </label>

                                <label class="flex items-center rounded-lg border border-gray-300 p-3 hover:bg-gray-50 cursor-pointer">
                                    <input type="checkbox" 
                                           name="target_roles[]" 
                                           value="researcher"
                                           x-model="selectedRoles"
                                           class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <div class="ml-3">
                                        <span class="font-medium text-gray-900">Researcher</span>
                                        <p class="text-xs text-gray-500">Peneliti dan evaluator</p>
                                    </div>
                                </label>
                            </div>
                            <p class="mt-2 text-sm text-gray-500">
                                Tiket akan dibuat untuk setiap role yang dipilih. Total tiket: <span x-text="selectedRoles.length" class="font-semibold text-blue-600"></span>
                            </p>
                            @error('target_roles')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Priority & Due Date -->
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Prioritas *</label>
                                <select name="priority" 
                                        required
                                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>
                                        🟢 Rendah
                                    </option>
                                    <option value="medium" {{ old('priority', 'medium') == 'medium' ? 'selected' : '' }}>
                                        🔵 Sedang
                                    </option>
                                    <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>
                                        🟠 Tinggi
                                    </option>
                                    <option value="urgent" {{ old('priority') == 'urgent' ? 'selected' : '' }}>
                                        🔴 Mendesak
                                    </option>
                                </select>
                                @error('priority')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div x-data="{ 
                                weight: {{ old('weight', 5) }},
                                getLabel() {
                                    if (this.weight <= 3) return { text: 'Ringan', color: 'text-green-600', bg: 'bg-green-100', border: 'border-green-300' };
                                    if (this.weight <= 6) return { text: 'Sedang', color: 'text-yellow-600', bg: 'bg-yellow-100', border: 'border-yellow-300' };
                                    return { text: 'Berat', color: 'text-red-600', bg: 'bg-red-100', border: 'border-red-300' };
                                }
                            }">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Bobot Kesulitan <span class="text-red-500">*</span>
                                </label>
                                
                                {{-- Weight Display Card --}}
                                <div class="mb-3 p-3 rounded-lg border-2 transition-all duration-200"
                                     :class="getLabel().bg + ' ' + getLabel().border">
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm font-medium text-gray-700">Tingkat Kesulitan:</span>
                                        <div class="flex items-center gap-2">
                                            <span x-text="weight" 
                                                  class="text-2xl font-bold"
                                                  :class="getLabel().color"></span>
                                            <span class="text-lg font-semibold"
                                                  :class="getLabel().color"
                                                  x-text="'- ' + getLabel().text"></span>
                                        </div>
                                    </div>
                                </div>
                                
                                {{-- Slider --}}
                                <div class="flex items-center gap-3">
                                    <span class="text-sm text-gray-500 font-medium">1</span>
                                    <input type="range" 
                                           name="weight" 
                                           min="1" 
                                           max="10" 
                                           x-model="weight"
                                           class="flex-1 h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-blue-600">
                                    <span class="text-sm text-gray-500 font-medium">10</span>
                                </div>
                                
                                <p class="mt-2 text-xs text-gray-500">
                                    <span class="font-medium text-green-600">1-3:</span> Ringan • 
                                    <span class="font-medium text-yellow-600">4-6:</span> Sedang • 
                                    <span class="font-medium text-red-600">7-10:</span> Berat
                                </p>
                                
                                @error('weight')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Batas Waktu</label>
                                <input type="date" 
                                       name="due_date"
                                       value="{{ old('due_date') }}"
                                       min="{{ date('Y-m-d') }}"
                                       class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <p class="mt-1 text-xs text-gray-500">Opsional - kosongkan jika tidak ada deadline</p>
                                @error('due_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="flex gap-3 pt-4">
                            <button type="submit" 
                                    class="inline-flex items-center rounded-lg bg-blue-600 px-6 py-3 text-white hover:bg-blue-700">
                                <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Buat Tiket Umum
                            </button>
                            <a href="{{ route('tickets.overview') }}" 
                               class="inline-flex items-center rounded-lg bg-gray-300 px-6 py-3 text-gray-700 hover:bg-gray-400">
                                Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Example Box -->
            <div class="mt-6 rounded-lg bg-gray-50 p-6">
                <h3 class="mb-3 font-semibold text-gray-900">💡 Contoh Penggunaan:</h3>
                <div class="space-y-2 text-sm text-gray-700">
                    <p><strong>Scenario 1:</strong> PM ingin semua Member membantu persiapan event → Pilih role "Member"</p>
                    <p><strong>Scenario 2:</strong> Butuh koordinasi Bendahara & Sekretaris untuk laporan keuangan → Pilih kedua role</p>
                    <p><strong>Scenario 3:</strong> Broadcast ke semua role utama untuk rapat umum → Pilih semua role</p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
