@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4 max-w-7xl" x-data="{ showCreateModal: @json($errors->any() || session('openCreateModal')) }">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">
                @can('business.approve')
                    Manajemen Usaha
                @else
                    Usaha Komunitas
                @endcan
            </h1>
            <p class="text-gray-600 text-sm mt-1">
                @can('business.approve')
                    Kelola dan monitoring seluruh usaha komunitas
                @else
                    Daftar usaha yang dikelola oleh komunitas
                @endcan
            </p>
        </div>
        @can('business.create')
            <button @click="showCreateModal = true"
               class="inline-flex items-center gap-2 bg-blue-600 text-white px-4 py-2.5 rounded-lg hover:bg-blue-700 transition shadow-sm font-semibold hover:shadow-md transform hover:scale-105">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Buat Usaha Baru
            </button>
        @endcan
    </div>

    <!-- Filter Tabs - Modern & Dynamic -->
    <div class="mb-8">
        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
            <!-- Tabs Container -->
            <div class="grid grid-cols-4 divide-x divide-gray-200">
                <!-- Tab: Semua -->
                <a href="{{ route('businesses.index') }}" 
                   class="group relative overflow-hidden transition-all duration-300 {{ !request('status') ? 'bg-gradient-to-br from-blue-600 to-blue-700' : 'bg-white hover:bg-gray-50' }}">
                    <div class="px-6 py-5 relative z-10">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center gap-3">
                                <div class="p-2 rounded-lg {{ !request('status') ? 'bg-white/20' : 'bg-blue-100' }} transition-colors">
                                    <svg class="h-5 w-5 {{ !request('status') ? 'text-white' : 'text-blue-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                    </svg>
                                </div>
                                <span class="text-lg font-bold {{ !request('status') ? 'text-white' : 'text-gray-900' }}">Semua Usaha</span>
                            </div>
                        </div>
                        <div class="flex items-baseline gap-2">
                            <span class="text-3xl font-extrabold {{ !request('status') ? 'text-white' : 'text-blue-600' }}">
                                {{ \App\Models\Business::count() }}
                            </span>
                            <span class="text-sm {{ !request('status') ? 'text-blue-100' : 'text-gray-500' }}">total</span>
                        </div>
                    </div>
                    <!-- Animated Border Bottom -->
                    @if(!request('status'))
                    <div class="absolute bottom-0 left-0 right-0 h-1 bg-white/30"></div>
                    @endif
                </a>

                <!-- Tab: Menunggu -->
                <a href="{{ route('businesses.index', ['status' => 'pending']) }}" 
                   class="group relative overflow-hidden transition-all duration-300 {{ request('status') === 'pending' ? 'bg-gradient-to-br from-yellow-500 to-amber-600' : 'bg-white hover:bg-gray-50' }}">
                    <div class="px-6 py-5 relative z-10">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center gap-3">
                                <div class="p-2 rounded-lg {{ request('status') === 'pending' ? 'bg-white/20' : 'bg-yellow-100' }} transition-colors">
                                    <svg class="h-5 w-5 {{ request('status') === 'pending' ? 'text-white' : 'text-yellow-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <span class="text-lg font-bold {{ request('status') === 'pending' ? 'text-white' : 'text-gray-900' }}">Menunggu</span>
                            </div>
                        </div>
                        <div class="flex items-baseline gap-2">
                            <span class="text-3xl font-extrabold {{ request('status') === 'pending' ? 'text-white' : 'text-yellow-600' }}">
                                {{ \App\Models\Business::where('status', 'pending')->count() }}
                            </span>
                            <span class="text-sm {{ request('status') === 'pending' ? 'text-yellow-100' : 'text-gray-500' }}">pending</span>
                        </div>
                        @if(\App\Models\Business::where('status', 'pending')->count() > 0 && request('status') === 'pending')
                        <div class="mt-2">
                            <span class="inline-flex items-center gap-1 px-2 py-1 bg-white/20 rounded-full text-xs font-medium text-white">
                                <svg class="h-3 w-3 animate-pulse" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                </svg>
                                Perlu Review
                            </span>
                        </div>
                        @endif
                    </div>
                    @if(request('status') === 'pending')
                    <div class="absolute bottom-0 left-0 right-0 h-1 bg-white/30"></div>
                    @endif
                </a>

                <!-- Tab: Disetujui -->
                <a href="{{ route('businesses.index', ['status' => 'approved']) }}" 
                   class="group relative overflow-hidden transition-all duration-300 {{ request('status') === 'approved' ? 'bg-gradient-to-br from-green-600 to-emerald-700' : 'bg-white hover:bg-gray-50' }}">
                    <div class="px-6 py-5 relative z-10">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center gap-3">
                                <div class="p-2 rounded-lg {{ request('status') === 'approved' ? 'bg-white/20' : 'bg-green-100' }} transition-colors">
                                    <svg class="h-5 w-5 {{ request('status') === 'approved' ? 'text-white' : 'text-green-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <span class="text-lg font-bold {{ request('status') === 'approved' ? 'text-white' : 'text-gray-900' }}">Disetujui</span>
                            </div>
                        </div>
                        <div class="flex items-baseline gap-2">
                            <span class="text-3xl font-extrabold {{ request('status') === 'approved' ? 'text-white' : 'text-green-600' }}">
                                {{ \App\Models\Business::where('status', 'approved')->count() }}
                            </span>
                            <span class="text-sm {{ request('status') === 'approved' ? 'text-green-100' : 'text-gray-500' }}">aktif</span>
                        </div>
                        @if(\App\Models\Business::where('status', 'approved')->count() > 0 && request('status') === 'approved')
                        <div class="mt-2">
                            <span class="inline-flex items-center gap-1 px-2 py-1 bg-white/20 rounded-full text-xs font-medium text-white">
                                <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M2 10.5a1.5 1.5 0 113 0v6a1.5 1.5 0 01-3 0v-6zM6 10.333v5.43a2 2 0 001.106 1.79l.05.025A4 4 0 008.943 18h5.416a2 2 0 001.962-1.608l1.2-6A2 2 0 0015.56 8H12V4a2 2 0 00-2-2 1 1 0 00-1 1v.667a4 4 0 01-.8 2.4L6.8 7.933a4 4 0 00-.8 2.4z" />
                                </svg>
                                Sukses
                            </span>
                        </div>
                        @endif
                    </div>
                    @if(request('status') === 'approved')
                    <div class="absolute bottom-0 left-0 right-0 h-1 bg-white/30"></div>
                    @endif
                </a>

                <!-- Tab: Ditolak -->
                <a href="{{ route('businesses.index', ['status' => 'rejected']) }}" 
                   class="group relative overflow-hidden transition-all duration-300 {{ request('status') === 'rejected' ? 'bg-gradient-to-br from-red-600 to-rose-700' : 'bg-white hover:bg-gray-50' }}">
                    <div class="px-6 py-5 relative z-10">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center gap-3">
                                <div class="p-2 rounded-lg {{ request('status') === 'rejected' ? 'bg-white/20' : 'bg-red-100' }} transition-colors">
                                    <svg class="h-5 w-5 {{ request('status') === 'rejected' ? 'text-white' : 'text-red-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </div>
                                <span class="text-lg font-bold {{ request('status') === 'rejected' ? 'text-white' : 'text-gray-900' }}">Ditolak</span>
                            </div>
                        </div>
                        <div class="flex items-baseline gap-2">
                            <span class="text-3xl font-extrabold {{ request('status') === 'rejected' ? 'text-white' : 'text-red-600' }}">
                                {{ \App\Models\Business::where('status', 'rejected')->count() }}
                            </span>
                            <span class="text-sm {{ request('status') === 'rejected' ? 'text-red-100' : 'text-gray-500' }}">rejected</span>
                        </div>
                        @if(\App\Models\Business::where('status', 'rejected')->count() > 0 && request('status') === 'rejected')
                        <div class="mt-2">
                            <span class="inline-flex items-center gap-1 px-2 py-1 bg-white/20 rounded-full text-xs font-medium text-white">
                                <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                                Perlu Revisi
                            </span>
                        </div>
                        @endif
                    </div>
                    @if(request('status') === 'rejected')
                    <div class="absolute bottom-0 left-0 right-0 h-1 bg-white/30"></div>
                    @endif
                </a>
            </div>
        </div>

        <!-- Quick Stats Summary (optional - shows when "Semua" is selected) -->
        @if(!request('status'))
        <div class="mt-4 grid grid-cols-3 gap-4">
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-4 border border-blue-200">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-blue-600 rounded-lg">
                        <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-blue-700 font-medium">Tingkat Approval</p>
                        <p class="text-2xl font-bold text-blue-900">
                            @php
                                $total = \App\Models\Business::count();
                                $approved = \App\Models\Business::where('status', 'approved')->count();
                                $rate = $total > 0 ? round(($approved / $total) * 100) : 0;
                            @endphp
                            {{ $rate }}%
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl p-4 border border-purple-200">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-purple-600 rounded-lg">
                        <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-purple-700 font-medium">Dengan Proyek</p>
                        <p class="text-2xl font-bold text-purple-900">
                            {{ \App\Models\Business::whereNotNull('project_id')->count() }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-amber-50 to-amber-100 rounded-xl p-4 border border-amber-200">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-amber-600 rounded-lg">
                        <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-amber-700 font-medium">Perlu Review</p>
                        <p class="text-2xl font-bold text-amber-900">
                            {{ \App\Models\Business::where('status', 'pending')->count() }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Business Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($businesses as $b)
            <a href="{{ route('businesses.show', $b) }}" 
               class="bg-white rounded-lg shadow-sm hover:shadow-md transition-all duration-200 overflow-hidden group border border-gray-200 hover:border-{{ $b->getStatusColor() }}-300">
                <!-- Card Header -->
                <div class="bg-gradient-to-r from-{{ $b->getStatusColor() }}-50 to-{{ $b->getStatusColor() }}-100 px-4 py-3 border-b border-{{ $b->getStatusColor() }}-200">
                    <div class="flex items-start justify-between">
                        <div class="flex-1 min-w-0">
                            <h3 class="font-bold text-gray-900 text-lg group-hover:text-{{ $b->getStatusColor() }}-700 transition truncate">
                                {{ $b->name }}
                            </h3>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-full bg-{{ $b->getStatusColor() }}-100 text-{{ $b->getStatusColor() }}-800 border border-{{ $b->getStatusColor() }}-200">
                                    @if($b->isPending())
                                        <svg class="h-3 w-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    @elseif($b->isApproved())
                                        <svg class="h-3 w-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    @elseif($b->isRejected())
                                        <svg class="h-3 w-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    @endif
                                    {{ $b->getStatusLabel() }}
                                </span>
                            </div>
                        </div>
                        @if($b->project_id)
                            <div class="ml-2">
                                <span class="inline-flex items-center px-2 py-1 text-xs bg-blue-100 text-blue-700 rounded-md border border-blue-200 font-medium"
                                      title="Memiliki proyek terkait">
                                    <svg class="h-3 w-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                                    </svg>
                                    Proyek
                                </span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Card Body -->
                <div class="p-4">
                    <p class="text-sm text-gray-600 line-clamp-3 mb-4">
                        {{ $b->description ?: 'Tidak ada deskripsi.' }}
                    </p>

                    <!-- Meta Info -->
                    <div class="space-y-2 text-xs">
                        <div class="flex items-center gap-2 text-gray-500">
                            <svg class="h-4 w-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <span class="truncate">
                                <span class="font-medium text-gray-700">{{ $b->creator->name }}</span>
                            </span>
                        </div>
                        
                        <div class="flex items-center gap-2 text-gray-500">
                            <svg class="h-4 w-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span>{{ $b->created_at->format('d M Y') }}</span>
                        </div>

                        @if($b->approver)
                            <div class="flex items-center gap-2 text-gray-500 pt-2 border-t border-gray-100">
                                <svg class="h-4 w-4 flex-shrink-0 text-{{ $b->isApproved() ? 'green' : 'red' }}-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span class="truncate">
                                    <span class="text-{{ $b->isApproved() ? 'green' : 'red' }}-700 font-medium">{{ $b->approver->name }}</span>
                                    <span class="text-gray-500">• {{ $b->approved_at->format('d M Y') }}</span>
                                </span>
                            </div>
                        @endif

                        @if($b->reports_count > 0)
                            <div class="flex items-center gap-2 text-gray-500 pt-2 border-t border-gray-100">
                                <svg class="h-4 w-4 flex-shrink-0 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <span class="font-medium text-blue-700">{{ $b->reports_count }} Laporan</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Card Footer -->
                <div class="px-4 py-3 bg-gray-50 border-t border-gray-100">
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-gray-500">Klik untuk detail</span>
                        <svg class="h-4 w-4 text-gray-400 group-hover:text-{{ $b->getStatusColor() }}-600 group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </div>
                </div>
            </a>
        @empty
            <div class="col-span-full">
                <div class="bg-white rounded-lg shadow-sm p-12 text-center">
                    <svg class="h-16 w-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Belum Ada Usaha</h3>
                    <p class="text-gray-600 mb-4">
                        @if(request('status'))
                            Tidak ada usaha dengan status "{{ ucfirst(request('status')) }}".
                        @else
                            Belum ada usaha yang terdaftar di komunitas.
                        @endif
                    </p>
                    @can('business.create')
                        <button @click="showCreateModal = true"
                           class="inline-flex items-center gap-2 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition font-semibold hover:shadow-md transform hover:scale-105">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Buat Usaha Pertama
                        </button>
                    @endcan
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($businesses->hasPages())
        <div class="mt-8">
            {{ $businesses->links() }}
        </div>
    @endif

    <!-- Create Business Modal -->
    @can('business.create')
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
         aria-modal="true"
         aria-labelledby="modal-title">
        
        <div @click.stop
             x-show="showCreateModal"
             x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="opacity-0 scale-95 translate-y-4"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200 transform"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-95 translate-y-4"
             class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-hidden">
            
            <!-- Modal Header -->
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="bg-white bg-opacity-20 p-2 rounded-lg">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <div>
                        <h2 id="modal-title" class="text-xl font-bold text-white">Buat Usaha Baru</h2>
                        <p class="text-blue-100 text-sm">Ajukan proposal usaha untuk persetujuan PM</p>
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
            <form method="POST" action="{{ route('businesses.store') }}" class="p-6 space-y-5">
                @csrf

                <!-- Info Alert -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 flex gap-3">
                    <svg class="h-5 w-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div class="text-sm">
                        <p class="text-blue-900 font-medium">Informasi Penting</p>
                        <p class="text-blue-700 mt-1">Usaha yang dibuat akan berstatus <strong>pending</strong> dan memerlukan persetujuan dari PM. Setelah disetujui, proyek akan otomatis dibuat.</p>
                    </div>
                </div>

                <!-- Nama Usaha -->
                <div>
                    <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                        <span class="flex items-center gap-2">
                            <svg class="h-4 w-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                            </svg>
                            Nama Usaha
                            <span class="text-red-500">*</span>
                        </span>
                    </label>
                    <input type="text" 
                           id="name"
                           name="name" 
                           required
                           value="{{ old('name') }}"
                           placeholder="Contoh: Kaos Custom SISARAYA"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition @error('name') border-red-500 @enderror">
                    @error('name')
                        <p class="text-red-500 text-xs mt-1 flex items-center gap-1">
                            <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Deskripsi -->
                <div>
                    <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">
                        <span class="flex items-center gap-2">
                            <svg class="h-4 w-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Deskripsi Usaha
                        </span>
                    </label>
                    <textarea id="description"
                              name="description" 
                              rows="5"
                              placeholder="Jelaskan konsep usaha, target pasar, produk/jasa yang ditawarkan, dan rencana operasional..."
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition resize-none @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-xs mt-1 flex items-center gap-1">
                            <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                    <p class="text-xs text-gray-500 mt-1 flex items-center gap-1">
                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Berikan deskripsi sejelas mungkin untuk mempercepat proses persetujuan
                    </p>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
                    <button type="button" 
                            @click="showCreateModal = false"
                            class="px-5 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition font-medium">
                        <span class="flex items-center gap-2">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            Batal
                        </span>
                    </button>
                    <button type="submit"
                            class="px-5 py-2.5 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg hover:from-blue-700 hover:to-blue-800 transition font-semibold shadow-lg hover:shadow-xl transform hover:scale-105">
                        <span class="flex items-center gap-2">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Buat Usaha
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endcan
</div>
@endsection
