<section>
    <header class="mb-6">
        <div class="flex items-center gap-3 mb-2">
            <div class="bg-gradient-to-r from-blue-600 to-purple-600 p-3 rounded-xl shadow-lg">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
            </div>
            <div>
                <h2 class="text-2xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                    {{ __('Profile Information') }}
                </h2>
                <p class="text-sm text-gray-600">
                    {{ __("Update your account's profile information and username.") }}
                </p>
            </div>
        </div>
    </header>

    <form method="post" action="{{ route('profile.update') }}" class="space-y-6" enctype="multipart/form-data" 
          x-data="{ 
              previewUrl: '{{ $user->photo_path ? asset('storage/' . $user->photo_path) : '' }}',
              userName: '{{ $user->name }}',
              handleFileChange(event) {
                  const file = event.target.files[0];
                  if (file) {
                      const reader = new FileReader();
                      reader.onload = (e) => {
                          this.previewUrl = e.target.result;
                      };
                      reader.readAsDataURL(file);
                  }
              }
          }">
        @csrf
        @method('patch')

        <!-- Photo Upload with Preview -->
        <div class="bg-gradient-to-br from-white to-blue-50 rounded-2xl p-6 border-2 border-blue-100 shadow-xl">
            <label class="block text-sm font-bold text-gray-700 mb-4">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Foto Profil
                </div>
            </label>
            
            <div class="flex flex-col md:flex-row items-center gap-6">
                <!-- Preview Photo -->
                <div class="flex-shrink-0">
                    <div class="relative group">
                        <template x-if="previewUrl">
                            <img :src="previewUrl" alt="Preview foto profil" 
                                 class="w-32 h-32 rounded-2xl object-cover border-4 border-white shadow-xl ring-2 ring-blue-200 group-hover:ring-blue-400 transition-all duration-300">
                        </template>
                        <template x-if="!previewUrl">
                            <div class="w-32 h-32 rounded-2xl bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white text-4xl font-bold shadow-xl ring-2 ring-blue-200 group-hover:ring-blue-400 transition-all duration-300">
                                <span x-text="userName.charAt(0).toUpperCase()"></span>
                            </div>
                        </template>
                        
                        <!-- Upload overlay on hover -->
                        <div class="absolute inset-0 bg-black bg-opacity-50 rounded-2xl flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                    </div>
                </div>
                
                <!-- Upload Input -->
                <div class="flex-1 w-full">
                    <label for="photo" class="relative cursor-pointer">
                        <div class="flex items-center gap-3 p-4 bg-white rounded-xl border-2 border-dashed border-blue-300 hover:border-blue-500 hover:bg-blue-50 transition-all duration-300 group">
                            <div class="bg-gradient-to-r from-blue-500 to-purple-600 p-3 rounded-lg group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-semibold text-gray-700 group-hover:text-blue-600 transition-colors">
                                    Pilih foto baru atau drag & drop
                                </p>
                                <p class="text-xs text-gray-500 mt-1">
                                    JPG, PNG, GIF • Maksimal 2MB
                                </p>
                            </div>
                        </div>
                        <input type="file" id="photo" name="photo" accept="image/*" 
                               @change="handleFileChange($event)"
                               class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                    </label>
                    
                    <x-input-error class="mt-2" :messages="$errors->get('photo')" />
                </div>
            </div>
        </div>

        <!-- Name & Username -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="name" class="block text-sm font-bold text-gray-700 mb-2">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        {{ __('Name') }}
                    </div>
                </label>
                <input id="name" name="name" type="text" 
                       x-model="userName"
                       value="{{ old('name', $user->name) }}" required autofocus autocomplete="name"
                       class="block w-full rounded-xl border-2 border-gray-200 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all duration-300 px-4 py-3 text-gray-900 placeholder-gray-400">
                <x-input-error class="mt-2" :messages="$errors->get('name')" />
            </div>

            <div>
                <label for="username" class="block text-sm font-bold text-gray-700 mb-2">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                        </svg>
                        {{ __('Username') }}
                    </div>
                </label>
                <input id="username" name="username" type="text" value="{{ old('username', $user->username) }}" required autocomplete="username"
                       class="block w-full rounded-xl border-2 border-gray-200 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all duration-300 px-4 py-3 text-gray-900 placeholder-gray-400">
                <x-input-error class="mt-2" :messages="$errors->get('username')" />
            </div>
        </div>

        <!-- Bio -->
        <div>
            <label for="bio" class="block text-sm font-bold text-gray-700 mb-2">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Bio
                </div>
            </label>
            <textarea id="bio" name="bio" rows="4" 
                      class="block w-full rounded-xl border-2 border-gray-200 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all duration-300 px-4 py-3 text-gray-900 placeholder-gray-400"
                      placeholder="Ceritakan sedikit tentang diri Anda...">{{ old('bio', $user->bio) }}</textarea>
            <x-input-error class="mt-2" :messages="$errors->get('bio')" />
        </div>

        <!-- Contact Info -->
        <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-2xl p-6 border-2 border-green-200 shadow-lg">
            <h3 class="text-sm font-bold text-gray-700 mb-4 flex items-center gap-2">
                <div class="bg-gradient-to-r from-green-500 to-emerald-600 p-2 rounded-lg">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                    </svg>
                </div>
                Informasi Kontak
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="phone" class="block text-xs font-semibold text-gray-600 mb-2">Nomor Telepon</label>
                    <input id="phone" name="phone" type="text" value="{{ old('phone', $user->phone) }}"
                           class="block w-full rounded-xl border-2 border-green-200 focus:border-green-500 focus:ring focus:ring-green-200 focus:ring-opacity-50 transition-all duration-300 px-4 py-2.5 text-gray-900 placeholder-gray-400"
                           placeholder="08xx xxxx xxxx">
                    <x-input-error class="mt-2" :messages="$errors->get('phone')" />
                </div>

                <div>
                    <label for="whatsapp" class="block text-xs font-semibold text-gray-600 mb-2">WhatsApp</label>
                    <input id="whatsapp" name="whatsapp" type="text" value="{{ old('whatsapp', $user->whatsapp) }}"
                           class="block w-full rounded-xl border-2 border-green-200 focus:border-green-500 focus:ring focus:ring-green-200 focus:ring-opacity-50 transition-all duration-300 px-4 py-2.5 text-gray-900 placeholder-gray-400"
                           placeholder="08xx xxxx xxxx">
                    <x-input-error class="mt-2" :messages="$errors->get('whatsapp')" />
                </div>
            </div>
        </div>

        <!-- Role (Read Only) -->
        <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-2xl p-6 border-2 border-purple-200 shadow-lg">
            <div class="flex items-center justify-between mb-3">
                <label for="role" class="block text-sm font-bold text-gray-700">
                    <div class="flex items-center gap-2">
                        <div class="bg-gradient-to-r from-purple-500 to-pink-600 p-2 rounded-lg">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                            </svg>
                        </div>
                        {{ __('Role') }}
                    </div>
                </label>
                
                <!-- Request Role Change Button -->
                <button type="button" 
                        @click="$dispatch('open-role-request-modal')"
                        class="px-4 py-2 bg-gradient-to-r from-purple-500 to-pink-600 text-white text-xs font-semibold rounded-lg shadow-md hover:shadow-xl hover:scale-105 transition-all duration-300 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Request Role
                </button>
            </div>
            
            <div class="flex flex-wrap gap-2 mb-3">
                @if(method_exists($user, 'getRoleNames') && $user->getRoleNames()->count() > 0)
                    @foreach($user->getRoleNames() as $role)
                        <span class="px-4 py-2 bg-gradient-to-r from-purple-500 to-pink-500 text-white rounded-full text-sm font-semibold shadow-lg">
                            {{ ucfirst($role) }}
                        </span>
                    @endforeach
                @else
                    <span class="px-4 py-2 bg-gray-300 text-gray-600 rounded-full text-sm font-semibold shadow-lg">
                        Belum ada role
                    </span>
                @endif
            </div>
            
            <p class="text-xs text-gray-500 flex items-center gap-2">
                <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Klik "Request Role" untuk mengajukan perubahan role. HR akan meninjau permintaan Anda.
            </p>
        </div>

        <!-- Save Button -->
        <div class="flex items-center gap-4 pt-4">
            <button type="submit" class="px-6 py-3 bg-gradient-to-r from-blue-500 to-purple-600 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl hover:scale-105 transition-all duration-300 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                {{ __('Save') }}
            </button>

            @if (session('status') === 'profile-updated')
                <div
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 transform scale-90"
                    x-transition:enter-end="opacity-100 transform scale-100"
                    x-transition:leave="transition ease-in duration-300"
                    x-transition:leave-start="opacity-100 transform scale-100"
                    x-transition:leave-end="opacity-0 transform scale-90"
                    x-init="setTimeout(() => show = false, 3000)"
                    class="flex items-center gap-2 px-4 py-2 bg-green-100 text-green-700 rounded-xl border-2 border-green-200 shadow-lg"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="font-semibold">{{ __('Saved.') }}</span>
                </div>
            @endif
        </div>
    </form>
</section>
