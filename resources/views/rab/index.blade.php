@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header Section -->
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="p-3 bg-gradient-to-br from-emerald-600 to-teal-600 rounded-xl shadow-lg">
                <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                </svg>
            </div>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Rencana Anggaran Biaya</h1>
                <p class="text-gray-600 mt-1">Kelola RAB proyek dan dana kegiatan</p>
            </div>
        </div>
        
        @can('finance.manage_rab')
        <a href="{{ route('rabs.create') }}" 
           class="inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-emerald-600 to-teal-600 text-white text-sm font-semibold rounded-full hover:shadow-lg hover:scale-105 active:scale-95 transition-all duration-300 shadow-md">
            <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Buat RAB Baru
        </a>
        @endcan
    </div>

    <!-- Status Filter Tabs -->
    <div class="mb-6 bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <nav class="flex flex-wrap">
            <a href="{{ route('rabs.index', ['status' => 'all']) }}" 
               class="flex-1 min-w-[120px] text-center py-4 px-4 border-b-2 font-medium text-sm transition-colors
                      @if(request('status', 'all') === 'all') 
                          border-emerald-600 text-emerald-600 bg-emerald-50 
                      @else 
                          border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50 
                      @endif">
                <div class="flex items-center justify-center gap-2">
                    <span>Semua</span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                 @if(request('status', 'all') === 'all') bg-emerald-100 text-emerald-700 @else bg-gray-100 text-gray-600 @endif">
                        {{ $rabs->total() }}
                    </span>
                </div>
            </a>
            
            <a href="{{ route('rabs.index', ['status' => 'draft']) }}" 
               class="flex-1 min-w-[120px] text-center py-4 px-4 border-b-2 font-medium text-sm transition-colors
                      @if(request('status') === 'draft') 
                          border-gray-600 text-gray-600 bg-gray-50 
                      @else 
                          border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50 
                      @endif">
                <div class="flex items-center justify-center gap-2">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span>Draft</span>
                </div>
            </a>
            
            <a href="{{ route('rabs.index', ['status' => 'pending']) }}" 
               class="flex-1 min-w-[120px] text-center py-4 px-4 border-b-2 font-medium text-sm transition-colors
                      @if(request('status') === 'pending') 
                          border-yellow-600 text-yellow-600 bg-yellow-50 
                      @else 
                          border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50 
                      @endif">
                <div class="flex items-center justify-center gap-2">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Menunggu</span>
                </div>
            </a>
            
            <a href="{{ route('rabs.index', ['status' => 'approved']) }}" 
               class="flex-1 min-w-[120px] text-center py-4 px-4 border-b-2 font-medium text-sm transition-colors
                      @if(request('status') === 'approved') 
                          border-green-600 text-green-600 bg-green-50 
                      @else 
                          border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50 
                      @endif">
                <div class="flex items-center justify-center gap-2">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Disetujui</span>
                </div>
            </a>
            
            <a href="{{ route('rabs.index', ['status' => 'rejected']) }}" 
               class="flex-1 min-w-[120px] text-center py-4 px-4 border-b-2 font-medium text-sm transition-colors
                      @if(request('status') === 'rejected') 
                          border-red-600 text-red-600 bg-red-50 
                      @else 
                          border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50 
                      @endif">
                <div class="flex items-center justify-center gap-2">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Ditolak</span>
                </div>
            </a>
        </nav>
    </div>

    @if($rabs->isEmpty())
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-16 text-center">
            <div class="max-w-md mx-auto">
                <div class="w-20 h-20 mx-auto mb-6 bg-gradient-to-br from-emerald-100 to-teal-100 rounded-full flex items-center justify-center">
                    <svg class="h-10 w-10 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Belum Ada RAB</h3>
                <p class="text-gray-600 mb-6">
                    @if(request('status', 'all') === 'all')
                        Belum ada Rencana Anggaran Biaya yang dibuat.
                    @else
                        Belum ada RAB dengan status "{{ ucfirst(request('status')) }}".
                    @endif
                </p>
                @can('finance.manage_rab')
                <a href="{{ route('rabs.create') }}" 
                   class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-emerald-600 to-teal-600 text-white text-sm font-semibold rounded-full hover:shadow-lg transition-all duration-300">
                    <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Buat RAB Pertama
                </a>
                @endcan
            </div>
        </div>
    @else
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($rabs as $rab)
                <div class="group bg-white rounded-2xl shadow-sm border border-gray-200 hover:shadow-xl hover:border-emerald-200 transition-all duration-300 overflow-hidden">
                    <!-- Card Header dengan Gradient -->
                    <div class="h-2 bg-gradient-to-r from-emerald-600 via-teal-600 to-cyan-500"></div>
                    
                    <div class="p-6">
                        <!-- RAB Title & Status -->
                        <div class="mb-4">
                            <div class="flex items-start justify-between gap-3 mb-3">
                                <h3 class="text-lg font-bold text-gray-900 group-hover:text-emerald-600 transition-colors line-clamp-2 flex-1">
                                    {{ $rab->title }}
                                </h3>
                            </div>
                            <x-rab-status-badge :status="$rab->funds_status ?? 'draft'" size="sm" />
                        </div>

                        <!-- Project Info -->
                        @if($rab->project)
                            <div class="mb-4 p-3 bg-gray-50 rounded-xl">
                                <div class="flex items-center gap-2 text-sm">
                                    <svg class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                                    </svg>
                                    <span class="text-gray-600">Proyek:</span>
                                    <span class="font-semibold text-gray-900 truncate">{{ $rab->project->name }}</span>
                                </div>
                            </div>
                        @endif

                        <!-- Amount -->
                        <div class="mb-4 p-4 bg-gradient-to-br from-emerald-50 to-teal-50 rounded-xl border border-emerald-200">
                            <div class="text-xs text-emerald-700 font-medium mb-1">Total Anggaran</div>
                            <div class="text-2xl font-bold text-emerald-700">
                                Rp {{ number_format($rab->amount, 0, ',', '.') }}
                            </div>
                        </div>

                        <!-- Description Preview -->
                        @if($rab->description)
                            <p class="text-sm text-gray-600 mb-4 line-clamp-2 leading-relaxed">
                                {{ $rab->description }}
                            </p>
                        @endif

                        <!-- Approval Info -->
                        @if($rab->approver)
                            <div class="mb-4 flex items-start gap-2 text-xs text-gray-500 bg-green-50 p-2 rounded-lg">
                                <svg class="h-4 w-4 text-green-600 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <div>
                                    <div class="font-medium text-green-700">Disetujui oleh {{ $rab->approver->name }}</div>
                                    <div>{{ $rab->approved_at?->format('d M Y H:i') }}</div>
                                </div>
                            </div>
                        @endif

                        <!-- Action Buttons -->
                        <div class="flex gap-2">
                            <a href="{{ route('rabs.show', $rab) }}" 
                               class="flex-1 inline-flex items-center justify-center px-4 py-2.5 bg-gradient-to-r from-emerald-600 to-teal-600 text-white text-sm font-semibold rounded-xl hover:shadow-lg hover:scale-105 active:scale-95 transition-all duration-300">
                                <span>Lihat Detail</span>
                                <svg class="ml-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                </svg>
                            </a>
                            
                            @can('finance.manage_rab')
                            <a href="{{ route('rabs.edit', $rab) }}" 
                               class="inline-flex items-center justify-center px-4 py-2.5 bg-gray-100 text-gray-700 text-sm font-semibold rounded-xl hover:bg-gray-200 transition-all duration-300">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </a>
                            @endcan
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $rabs->links() }}
        </div>
    @endif
</div>
@endsection
