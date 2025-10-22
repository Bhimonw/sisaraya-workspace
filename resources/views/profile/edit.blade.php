<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-2xl text-gray-800 leading-tight flex items-center gap-3">
                    <div class="bg-gradient-to-r from-blue-600 to-purple-600 p-2 rounded-xl">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    {{ __('Profile') }}
                </h2>
                <p class="text-sm text-gray-600 mt-1">Kelola informasi akun dan pengaturan profil Anda</p>
            </div>
        </div>
    </x-slot>

    <div class="py-6 sm:py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Desktop Grid Layout: 2 Columns -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">
                <!-- Left Column: Main Profile (Spans 2 columns on desktop) -->
                <div class="lg:col-span-2 space-y-4 sm:space-y-6">
                    @include('profile.cards.profile-information-card')
                </div>

                <!-- Right Column: Security & Danger Zone -->
                <div class="lg:col-span-1 space-y-4 sm:space-y-6">
                    @include('profile.cards.update-password-card')
                    @include('profile.cards.delete-account-card')
                </div>
            </div>
        </div>
    </div>

    <!-- Role Change Request Modal -->
    @include('profile.partials.role-change-request-modal')
</x-app-layout>
