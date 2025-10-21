<!-- Modal untuk Tambah User Baru -->
<div x-data="{ 
        showCreateModal: {{ $errors->any() ? 'true' : 'false' }},
        selectedRoles: {{ json_encode(old('roles', [])) }},
        isSubmitting: false,
        get isGuestSelected() {
            return this.selectedRoles.includes('guest');
        },
        get hasOtherRoles() {
            return this.selectedRoles.some(role => role !== 'guest');
        },
        toggleRole(roleName) {
            if (this.selectedRoles.includes(roleName)) {
                this.selectedRoles = this.selectedRoles.filter(r => r !== roleName);
            } else {
                // If selecting guest, clear other roles
                if (roleName === 'guest') {
                    this.selectedRoles = ['guest'];
                } 
                // If selecting other role and guest is selected, remove guest
                else if (this.selectedRoles.includes('guest')) {
                    this.selectedRoles = [roleName];
                } 
                // Normal addition
                else {
                    this.selectedRoles.push(roleName);
                }
            }
        },
        resetForm() {
            this.selectedRoles = [];
            this.isSubmitting = false;
        }
    }" 
     @open-create-user-modal.window="showCreateModal = true; if ($event.detail && $event.detail.reset !== false) { resetForm(); }"
     @keydown.escape.window="showCreateModal = false"
     x-show="showCreateModal"
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto"
     style="display: none;">
    
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity" 
         @click="showCreateModal = false"></div>

    <!-- Modal Container -->
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="relative bg-white rounded-2xl shadow-2xl max-w-3xl w-full max-h-[90vh] overflow-hidden"
             @click.stop>
            
            <!-- Modal Header -->
            <div class="bg-gradient-to-r from-blue-600 to-cyan-600 px-6 py-5 flex items-center justify-between sticky top-0 z-10">
                <h3 class="text-xl font-bold text-white flex items-center gap-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                    </svg>
                    Tambah User Baru
                </h3>
                <button @click="showCreateModal = false; resetForm();" 
                        class="text-white/80 hover:text-white hover:bg-white/10 p-2 rounded-lg transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Modal Content -->
            <div class="overflow-y-auto max-h-[calc(90vh-140px)]">
                <form method="POST" action="{{ route('admin.users.store') }}" class="p-6 space-y-5"
                      @submit="isSubmitting = true">
                    @csrf

                    {{-- Error Summary --}}
                    @if ($errors->any())
                        <div class="bg-gradient-to-r from-red-50 to-pink-50 border-l-4 border-red-500 p-4 rounded-lg"
                             x-data="{ show: true }"
                             x-show="show"
                             x-transition>
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-2">
                                        <svg class="h-5 w-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <span class="text-sm font-bold text-red-800">Terdapat kesalahan dalam pengisian form:</span>
                                    </div>
                                    <ul class="list-disc list-inside text-red-700 space-y-1 text-sm ml-7">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                                <button @click="show = false" type="button" class="text-red-600 hover:text-red-800">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    @endif

                    <!-- Name -->
                    <div>
                        <label for="modal-name" class="block text-sm font-semibold text-gray-700 mb-2">
                            Nama Lengkap <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" id="modal-name" required
                               value="{{ old('name') }}"
                               placeholder="Contoh: John Doe"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition @error('name') border-red-500 @enderror">
                        @error('name')
                            <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Username -->
                    <div>
                        <label for="modal-username" class="block text-sm font-semibold text-gray-700 mb-2">
                            Username <span class="text-red-500">*</span>
                        </label>
                        <div class="flex">
                            <span class="inline-flex items-center px-4 rounded-l-lg border border-r-0 border-gray-300 bg-gradient-to-br from-gray-50 to-gray-100 text-gray-600 text-sm font-semibold">
                                @
                            </span>
                            <input type="text" name="username" id="modal-username" required
                                   value="{{ old('username') }}"
                                   placeholder="johndoe"
                                   class="flex-1 px-4 py-2.5 border border-gray-300 rounded-r-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition @error('username') border-red-500 @enderror">
                        </div>
                        <p class="mt-1.5 text-xs text-gray-500 flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Username untuk login, gunakan huruf kecil tanpa spasi
                        </p>
                        @error('username')
                            <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email (Optional) -->
                    <div>
                        <label for="modal-email" class="block text-sm font-semibold text-gray-700 mb-2">
                            Email <span class="text-gray-400 font-normal">(Opsional)</span>
                        </label>
                        <input type="email" name="email" id="modal-email"
                               value="{{ old('email') }}"
                               placeholder="john@example.com"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition @error('email') border-red-500 @enderror">
                        @error('email')
                            <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="modal-password" class="block text-sm font-semibold text-gray-700 mb-2">
                            Password <span class="text-red-500">*</span>
                        </label>
                        <input type="password" name="password" id="modal-password" required
                               placeholder="••••••••"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                        <p class="mt-1.5 text-xs text-gray-500">Minimal 8 karakter</p>
                    </div>

                    <!-- Password Confirmation -->
                    <div>
                        <label for="modal-password-confirmation" class="block text-sm font-semibold text-gray-700 mb-2">
                            Konfirmasi Password <span class="text-red-500">*</span>
                        </label>
                        <input type="password" name="password_confirmation" id="modal-password-confirmation" required
                               placeholder="••••••••"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                    </div>

                    <!-- Roles -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-3">
                            Role(s) <span class="text-gray-400 font-normal">(Pilih satu atau lebih)</span>
                        </label>
                        
                        <!-- Warning Box -->
                        <div class="mb-4 p-4 bg-gradient-to-r from-amber-50 to-yellow-50 border-l-4 border-amber-500 rounded-lg">
                            <div class="flex items-start gap-3">
                                <svg class="h-5 w-5 text-amber-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                <div class="flex-1">
                                    <p class="text-sm text-amber-800 font-semibold">Penting tentang Role Guest:</p>
                                    <p class="text-xs text-amber-700 mt-1">Role <strong>Guest</strong> tidak dapat digabung dengan role lainnya. Guest adalah role khusus dengan akses terbatas hanya ke proyek tertentu.</p>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            @foreach($roles as $role)
                                <label 
                                    class="relative flex items-center p-4 border-2 rounded-xl cursor-pointer transition-all duration-200"
                                    :class="{
                                        'bg-blue-50 border-blue-500 shadow-md': selectedRoles.includes('{{ $role->name }}'),
                                        'border-gray-200 hover:border-gray-300 hover:bg-gray-50': !selectedRoles.includes('{{ $role->name }}'),
                                        'opacity-50 cursor-not-allowed': (isGuestSelected && '{{ $role->name }}' !== 'guest') || (hasOtherRoles && '{{ $role->name }}' === 'guest')
                                    }"
                                >
                                    <input 
                                        type="checkbox" 
                                        name="roles[]" 
                                        value="{{ $role->name }}" 
                                        :checked="selectedRoles.includes('{{ $role->name }}')"
                                        :disabled="(isGuestSelected && '{{ $role->name }}' !== 'guest') || (hasOtherRoles && '{{ $role->name }}' === 'guest')"
                                        @change="toggleRole('{{ $role->name }}')"
                                        class="h-5 w-5 text-blue-600 focus:ring-blue-500 border-gray-300 rounded disabled:opacity-50 disabled:cursor-not-allowed"
                                    >
                                    <div class="ml-3 flex-1">
                                        <span class="text-sm font-semibold text-gray-900 capitalize block">
                                            {{ $role->name }}
                                        </span>
                                        @if($role->name === 'guest')
                                            <span class="text-xs text-amber-600 mt-0.5 block flex items-center gap-1">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                                </svg>
                                                Eksklusif, tidak bisa dicampur
                                            </span>
                                        @endif
                                    </div>
                                    <!-- Checkmark indicator -->
                                    <svg x-show="selectedRoles.includes('{{ $role->name }}')" 
                                         class="absolute top-2 right-2 w-5 h-5 text-blue-600" 
                                         fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                </label>
                            @endforeach
                        </div>
                        @error('roles')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Projects Selection (shown when guest role is selected) -->
                    <div x-show="isGuestSelected" 
                         x-transition
                         class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl p-5 border-2 border-blue-200">
                        <label class="block text-sm font-semibold text-gray-900 mb-3 flex items-center gap-2">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                            Proyek yang Diikuti <span class="text-red-500">*</span>
                        </label>
                        <p class="text-xs text-blue-700 mb-3 flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Wajib untuk role Guest - hanya proyek aktif yang ditampilkan
                        </p>
                        <div class="bg-white rounded-lg border border-blue-200 p-3 max-h-64 overflow-y-auto space-y-2">
                            @forelse($projects as $project)
                                <label class="flex items-start p-3 hover:bg-blue-50 rounded-lg cursor-pointer transition group">
                                    <input type="checkbox" name="projects[]" value="{{ $project->id }}" 
                                        {{ in_array($project->id, old('projects', [])) ? 'checked' : '' }}
                                        class="h-5 w-5 text-blue-600 focus:ring-blue-500 border-gray-300 rounded mt-0.5">
                                    <div class="ml-3 flex-1">
                                        <div class="flex items-center gap-2">
                                            <span class="text-sm font-semibold text-gray-900 group-hover:text-blue-700 transition">{{ $project->name }}</span>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                </svg>
                                                Aktif
                                            </span>
                                        </div>
                                        @if($project->description)
                                            <p class="text-xs text-gray-600 mt-1">{{ Str::limit($project->description, 80) }}</p>
                                        @endif
                                    </div>
                                </label>
                            @empty
                                <div class="text-center py-8">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                    </svg>
                                    <p class="mt-3 text-sm font-medium text-gray-900">Belum ada proyek aktif</p>
                                    <p class="text-xs text-gray-500 mt-1">Buat proyek baru dengan status "Aktif" terlebih dahulu</p>
                                </div>
                            @endforelse
                        </div>
                        @error('projects')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex gap-3 pt-4 border-t border-gray-200">
                        <button type="button" 
                                @click="showCreateModal = false; resetForm();"
                                :disabled="isSubmitting"
                                class="flex-1 px-6 py-3 border-2 border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition disabled:opacity-50 disabled:cursor-not-allowed">
                            Batal
                        </button>
                        <button type="submit"
                                :disabled="isSubmitting"
                                class="flex-1 px-6 py-3 bg-gradient-to-r from-blue-600 to-cyan-600 text-white font-semibold rounded-lg hover:from-blue-700 hover:to-cyan-700 transition shadow-lg disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                            <svg x-show="isSubmitting" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span x-text="isSubmitting ? 'Membuat User...' : 'Buat User'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
