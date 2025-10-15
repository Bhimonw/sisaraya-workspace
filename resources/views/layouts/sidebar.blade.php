<!-- Header (Fixed) -->
<div class="p-4 border-b border-gray-100 flex-shrink-0">
    <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
        <img src="{{ asset('logo-no-bg.png') }}" alt="SISARAYA" class="h-8 w-auto">
        <span class="font-semibold text-lg">SISARAYA</span>
    </a>
</div>

<!-- Menu (Scrollable) -->
<nav class="flex-1 overflow-y-auto p-4">
    @include('layouts._menu')
</nav>

<!-- Footer (Fixed) -->
<div class="p-4 border-t border-gray-100 flex-shrink-0">
    @auth
        <div class="text-sm text-gray-700">
            <div class="font-medium truncate">{{ Auth::user()->name }}</div>
            <div class="text-xs text-gray-500 truncate">{{ Auth::user()->email }}</div>
        </div>

        <div class="mt-3 space-y-1">
            <a href="{{ route('profile.edit') }}" class="block text-sm text-gray-600 hover:text-gray-800">{{ __('Profile') }}</a>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-sm text-red-600 hover:underline">{{ __('Log Out') }}</button>
            </form>
        </div>
    @else
        <div>
            <a href="{{ route('login') }}" class="text-sm text-gray-700">{{ __('Log in') }}</a>
        </div>
    @endauth
</div>
