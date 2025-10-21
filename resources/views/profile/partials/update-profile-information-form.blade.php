<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("Update your account's profile information and username.") }}
        </p>
    </header>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6" enctype="multipart/form-data">
        @csrf
        @method('patch')

        <!-- Photo Upload -->
        <div>
            <x-input-label for="photo" value="Foto Profil" />
            <div class="mt-2 flex items-center gap-4">
                @if($user->photo_path)
                    <img src="{{ asset('storage/' . $user->photo_path) }}" alt="Foto profil" class="w-20 h-20 rounded-full object-cover">
                @else
                    <div class="w-20 h-20 rounded-full bg-gray-200 flex items-center justify-center text-gray-500 text-2xl font-bold">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                @endif
                <input type="file" id="photo" name="photo" accept="image/*" class="block text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
            </div>
            <p class="mt-1 text-xs text-gray-500">Format: JPG, PNG, GIF. Maksimal 2MB.</p>
            <x-input-error class="mt-2" :messages="$errors->get('photo')" />
        </div>

        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="username" :value="__('Username')" />
            <x-text-input id="username" name="username" type="text" class="mt-1 block w-full" :value="old('username', $user->username)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('username')" />
        </div>

        <div>
            <x-input-label for="bio" value="Bio" />
            <textarea id="bio" name="bio" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('bio', $user->bio) }}</textarea>
            <x-input-error class="mt-2" :messages="$errors->get('bio')" />
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <x-input-label for="phone" value="Nomor Telepon" />
                <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full" :value="old('phone', $user->phone)" />
                <x-input-error class="mt-2" :messages="$errors->get('phone')" />
            </div>

            <div>
                <x-input-label for="whatsapp" value="WhatsApp" />
                <x-text-input id="whatsapp" name="whatsapp" type="text" class="mt-1 block w-full" :value="old('whatsapp', $user->whatsapp)" />
                <x-input-error class="mt-2" :messages="$errors->get('whatsapp')" />
            </div>
        </div>

        <div>
            <x-input-label for="role" :value="__('Role')" />
            <input id="role" disabled class="mt-1 block w-full border-gray-300 rounded" value="@if(method_exists($user, 'getRoleNames')){{ $user->getRoleNames()->implode(', ') ?? 'User' }}@else{{ $user->role ?? 'User' }}@endif">
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
