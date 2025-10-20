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
                    <div class="mb-6 rounded-lg bg-gradient-to-r from-blue-50 to-indigo-50 border-2 border-blue-200 p-5">
                        <div class="flex gap-4">
                            <div class="flex-shrink-0">
                                <div class="p-2 bg-blue-600 rounded-lg">
                                    <svg class="h-6 w-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-base font-bold text-blue-900 mb-2">Cara Kerja Tiket Umum (Broadcast)</h3>
                                <div class="space-y-2 text-sm text-blue-800">
                                    <div class="flex items-start gap-2">
                                        <span class="font-bold text-blue-600">1️⃣</span>
                                        <p><strong>1 tiket per role</strong> - Jika pilih 2 role, akan dibuat 2 tiket (bukan per orang)</p>
                                    </div>
                                    <div class="flex items-start gap-2">
                                        <span class="font-bold text-blue-600">2️⃣</span>
                                        <p><strong>Visible untuk semua</strong> - Tiket muncul di "Tiketku" untuk SEMUA anggota dengan role tersebut</p>
                                    </div>
                                    <div class="flex items-start gap-2">
                                        <span class="font-bold text-blue-600">3️⃣</span>
                                        <p><strong>Claim untuk ambil</strong> - Anggota bisa klik "Take" untuk assign tiket ke diri sendiri</p>
                                    </div>
                                    <div class="flex items-start gap-2">
                                        <span class="font-bold text-blue-600">4️⃣</span>
                                        <p><strong>Notifikasi 1x</strong> - Setiap orang dapat 1 notifikasi saja (meski punya multiple roles)</p>
                                    </div>
                                </div>
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
                                <label class="flex items-center rounded-lg border-2 border-blue-300 bg-blue-50 p-3 hover:bg-blue-100 cursor-pointer">
                                    <input type="checkbox" 
                                           name="target_roles[]" 
                                           value="member"
                                           x-model="selectedRoles"
                                           checked
                                           class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <div class="ml-3 flex-1">
                                        <div class="flex items-center gap-2">
                                            <span class="font-bold text-gray-900">Member (Semua Anggota)</span>
                                            <span class="px-2 py-0.5 bg-blue-600 text-white text-xs font-semibold rounded-full">Recommended</span>
                                        </div>
                                        <p class="text-xs text-gray-600 mt-1">
                                            <strong>Universal role</strong> - Mencakup SEMUA anggota aktif termasuk PM, HR, Bendahara, Media, PR, dll.
                                        </p>
                                    </div>
                                </label>

                                <div class="border-t border-gray-300 my-3 pt-3">
                                    <p class="text-sm font-medium text-gray-700 mb-2">Atau pilih role spesifik:</p>
                                </div>

                                <label class="flex items-center rounded-lg border border-gray-300 p-3 hover:bg-gray-50 cursor-pointer">
                                    <input type="checkbox" 
                                           name="target_roles[]" 
                                           value="talent"
                                           x-model="selectedRoles"
                                           class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <div class="ml-3">
                                        <span class="font-medium text-gray-900">Talent</span>
                                        <p class="text-xs text-gray-500">Anggota tanpa role khusus</p>
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

                                <label class="flex items-center rounded-lg border border-gray-300 p-3 hover:bg-gray-50 cursor-pointer">
                                    <input type="checkbox" 
                                           name="target_roles[]" 
                                           value="media"
                                           x-model="selectedRoles"
                                           class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <div class="ml-3">
                                        <span class="font-medium text-gray-900">Media</span>
                                        <p class="text-xs text-gray-500">Pengelola konten dan media</p>
                                    </div>
                                </label>

                                <label class="flex items-center rounded-lg border border-gray-300 p-3 hover:bg-gray-50 cursor-pointer">
                                    <input type="checkbox" 
                                           name="target_roles[]" 
                                           value="pr"
                                           x-model="selectedRoles"
                                           class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <div class="ml-3">
                                        <span class="font-medium text-gray-900">Public Relations</span>
                                        <p class="text-xs text-gray-500">Hubungan masyarakat</p>
                                    </div>
                                </label>

                                <label class="flex items-center rounded-lg border border-gray-300 p-3 hover:bg-gray-50 cursor-pointer">
                                    <input type="checkbox" 
                                           name="target_roles[]" 
                                           value="talent_manager"
                                           x-model="selectedRoles"
                                           class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <div class="ml-3">
                                        <span class="font-medium text-gray-900">Talent Manager</span>
                                        <p class="text-xs text-gray-500">Pengelola talent</p>
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
                                        Rendah
                                    </option>
                                    <option value="medium" {{ old('priority', 'medium') == 'medium' ? 'selected' : '' }}>
                                        Sedang
                                    </option>
                                    <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>
                                        Tinggi
                                    </option>
                                    <option value="urgent" {{ old('priority') == 'urgent' ? 'selected' : '' }}>
                                        Mendesak
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
            <div class="mt-6 rounded-lg bg-gradient-to-r from-green-50 to-emerald-50 border-2 border-green-200 p-6">
                <h3 class="mb-4 font-bold text-gray-900 flex items-center gap-2 text-lg">
                    <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                    </svg>
                    Contoh Penggunaan
                </h3>
                <div class="space-y-3 text-sm">
                    <!-- Example 1 -->
                    <div class="bg-white rounded-xl p-4 border-2 border-blue-200 shadow-sm">
                        <div class="flex items-start gap-3">
                            <div class="flex-shrink-0 w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center font-bold">1</div>
                            <div class="flex-1">
                                <p class="font-bold text-blue-900 mb-2">� Broadcast ke Semua Anggota</p>
                                <p class="text-gray-700 mb-2"><strong>Kebutuhan:</strong> Rapat koordinasi bulanan - semua anggota harus hadir</p>
                                <p class="text-gray-700 mb-2"><strong>Pilih role:</strong> <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded font-medium">Member (Semua Anggota)</span></p>
                                <p class="text-gray-700"><strong>Hasil:</strong> <span class="text-green-600 font-semibold">1 tiket</span> dibuat, visible untuk <span class="text-green-600 font-semibold">14 orang</span>, notifikasi ke semua</p>
                            </div>
                        </div>
                    </div>

                    <!-- Example 2 -->
                    <div class="bg-white rounded-xl p-4 border-2 border-orange-200 shadow-sm">
                        <div class="flex items-start gap-3">
                            <div class="flex-shrink-0 w-8 h-8 bg-orange-600 text-white rounded-full flex items-center justify-center font-bold">2</div>
                            <div class="flex-1">
                                <p class="font-bold text-orange-900 mb-2 flex items-center gap-2">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                    Target Role Spesifik
                                </p>
                                <p class="text-gray-700 mb-2"><strong>Kebutuhan:</strong> Butuh koordinasi Bendahara & Sekretaris untuk laporan</p>
                                <p class="text-gray-700 mb-2"><strong>Pilih role:</strong> 
                                    <span class="px-2 py-1 bg-orange-100 text-orange-800 rounded font-medium">Bendahara</span> + 
                                    <span class="px-2 py-1 bg-orange-100 text-orange-800 rounded font-medium">Sekretaris</span>
                                </p>
                                <p class="text-gray-700"><strong>Hasil:</strong> <span class="text-green-600 font-semibold">2 tiket</span> dibuat, Dijah lihat tiket bendahara, Bhimo lihat tiket sekretaris</p>
                            </div>
                        </div>
                    </div>

                    <!-- Example 3 -->
                    <div class="bg-white rounded-xl p-4 border-2 border-purple-200 shadow-sm">
                        <div class="flex items-start gap-3">
                            <div class="flex-shrink-0 w-8 h-8 bg-purple-600 text-white rounded-full flex items-center justify-center font-bold">3</div>
                            <div class="flex-1">
                                <p class="font-bold text-purple-900 mb-2 flex items-center gap-2">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                    </svg>
                                    Target Divisi Spesifik
                                </p>
                                <p class="text-gray-700 mb-2"><strong>Kebutuhan:</strong> Butuh konten untuk campaign dari tim Media</p>
                                <p class="text-gray-700 mb-2"><strong>Pilih role:</strong> <span class="px-2 py-1 bg-purple-100 text-purple-800 rounded font-medium">Media</span></p>
                                <p class="text-gray-700"><strong>Hasil:</strong> <span class="text-green-600 font-semibold">1 tiket</span> dibuat, visible untuk <span class="text-green-600 font-semibold">7 staff media</span>, siapa cepat dia yang "Take"</p>
                            </div>
                        </div>
                    </div>

                    <!-- Warning -->
                    <div class="bg-yellow-50 border-2 border-yellow-300 rounded-xl p-4 mt-4">
                        <div class="flex gap-3">
                            <svg class="h-6 w-6 text-yellow-600 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <div>
                                <p class="font-bold text-yellow-900 mb-1 flex items-center gap-2">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                    </svg>
                                    Perbedaan dengan Assignment Langsung
                                </p>
                                <p class="text-sm text-yellow-800">Jika ingin <strong>assign tiket langsung ke orang tertentu</strong> (bukan role), gunakan form buat tiket biasa dengan pilih assignee. Tiket general ini untuk <strong>tiket yang bisa dikerjakan siapa saja</strong> dalam role/divisi tersebut.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
