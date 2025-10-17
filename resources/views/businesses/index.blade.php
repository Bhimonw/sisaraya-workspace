@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4 max-w-7xl">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">
                @if(auth()->user()->hasRole('pm'))
                    Manajemen Usaha
                @else
                    Usaha Komunitas
                @endif
            </h1>
            <p class="text-gray-600 text-sm mt-1">
                @if(auth()->user()->hasRole('pm'))
                    Kelola dan monitoring seluruh usaha komunitas
                @else
                    Daftar usaha yang dikelola oleh komunitas
                @endif
            </p>
        </div>
        @can('business.create')
            <a href="{{ route('businesses.create') }}" 
               class="inline-flex items-center gap-2 bg-blue-600 text-white px-4 py-2.5 rounded-lg hover:bg-blue-700 transition shadow-sm font-semibold">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Buat Usaha Baru
            </a>
        @endcan
    </div>

    <!-- Filter Tabs -->
    <div class="mb-6 bg-white rounded-lg shadow-sm p-1 inline-flex gap-1">
        <a href="{{ route('businesses.index') }}" 
           class="px-4 py-2 rounded-md text-sm font-medium transition {{ !request('status') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100' }}">
            <span class="inline-flex items-center gap-2">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
                Semua
                <span class="px-2 py-0.5 bg-white/20 rounded-full text-xs">{{ \App\Models\Business::count() }}</span>
            </span>
        </a>
        <a href="{{ route('businesses.index', ['status' => 'pending']) }}" 
           class="px-4 py-2 rounded-md text-sm font-medium transition {{ request('status') === 'pending' ? 'bg-yellow-600 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100' }}">
            <span class="inline-flex items-center gap-2">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Menunggu
                <span class="px-2 py-0.5 bg-white/20 rounded-full text-xs">{{ \App\Models\Business::where('status', 'pending')->count() }}</span>
            </span>
        </a>
        <a href="{{ route('businesses.index', ['status' => 'approved']) }}" 
           class="px-4 py-2 rounded-md text-sm font-medium transition {{ request('status') === 'approved' ? 'bg-green-600 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100' }}">
            <span class="inline-flex items-center gap-2">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Disetujui
                <span class="px-2 py-0.5 bg-white/20 rounded-full text-xs">{{ \App\Models\Business::where('status', 'approved')->count() }}</span>
            </span>
        </a>
        <a href="{{ route('businesses.index', ['status' => 'rejected']) }}" 
           class="px-4 py-2 rounded-md text-sm font-medium transition {{ request('status') === 'rejected' ? 'bg-red-600 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100' }}">
            <span class="inline-flex items-center gap-2">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
                Ditolak
                <span class="px-2 py-0.5 bg-white/20 rounded-full text-xs">{{ \App\Models\Business::where('status', 'rejected')->count() }}</span>
            </span>
        </a>
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
                        <a href="{{ route('businesses.create') }}" 
                           class="inline-flex items-center gap-2 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition font-semibold">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Buat Usaha Pertama
                        </a>
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
</div>
@endsection
