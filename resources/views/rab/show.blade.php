@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto">
    <!-- Back Button -->
    <div class="mb-6">
        <a href="{{ route('rabs.index') }}" 
           class="inline-flex items-center gap-2 text-gray-600 hover:text-emerald-600 transition-colors">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            <span class="font-medium">Kembali ke Daftar RAB</span>
        </a>
    </div>

    <!-- Header Section -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden mb-6">
        <div class="h-2 bg-gradient-to-r from-emerald-600 via-teal-600 to-cyan-500"></div>
        
        <div class="p-8">
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 mb-6">
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="p-3 bg-gradient-to-br from-emerald-100 to-teal-100 rounded-xl">
                            <svg class="h-8 w-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900">{{ $rab->title }}</h1>
                            <div class="mt-2">
                                <x-rab-status-badge :status="$rab->funds_status ?? 'draft'" size="md" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Amount Card -->
            <div class="mb-6 p-6 bg-gradient-to-br from-emerald-50 to-teal-50 rounded-2xl border-2 border-emerald-200">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-sm text-emerald-700 font-medium mb-1">Total Anggaran</div>
                        <div class="text-4xl font-bold text-emerald-700">
                            Rp {{ number_format($rab->amount, 0, ',', '.') }}
                        </div>
                    </div>
                    <div class="p-4 bg-white/50 rounded-xl">
                        <svg class="h-12 w-12 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Project Info -->
            @if($rab->project)
            <div class="mb-6 p-4 bg-gray-50 rounded-xl border border-gray-200">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-violet-100 rounded-lg">
                        <svg class="h-5 w-5 text-violet-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                        </svg>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500 font-medium">Terkait Proyek</div>
                        <a href="{{ route('projects.show', $rab->project) }}" 
                           class="text-lg font-semibold text-gray-900 hover:text-violet-600 transition-colors">
                            {{ $rab->project->name }}
                        </a>
                    </div>
                </div>
            </div>
            @endif

            <!-- Description -->
            @if($rab->description)
            <div class="mb-6">
                <h3 class="text-sm font-semibold text-gray-700 mb-2">Deskripsi</h3>
                <div class="p-4 bg-gray-50 rounded-xl border border-gray-200">
                    <p class="text-gray-700 leading-relaxed whitespace-pre-line">{{ $rab->description }}</p>
                </div>
            </div>
            @endif

            <!-- Approval Info -->
            @if($rab->approver)
            <div class="mb-6 p-4 bg-green-50 rounded-xl border border-green-200">
                <div class="flex items-start gap-3">
                    <div class="p-2 bg-green-100 rounded-lg">
                        <svg class="h-5 w-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <div class="text-sm font-semibold text-green-700">Disetujui oleh</div>
                        <div class="text-lg font-bold text-green-900">{{ $rab->approver->name }}</div>
                        <div class="text-sm text-green-600">{{ $rab->approved_at?->format('d F Y, H:i') }} WIB</div>
                    </div>
                </div>
            </div>
            @endif

            <!-- File Attachment -->
            @if($rab->file_path)
            <div class="mb-6">
                <h3 class="text-sm font-semibold text-gray-700 mb-2">Lampiran</h3>
                <a href="{{ url('storage/'.$rab->file_path) }}" 
                   target="_blank"
                   class="inline-flex items-center gap-3 p-4 bg-blue-50 hover:bg-blue-100 rounded-xl border border-blue-200 transition-colors group">
                    <div class="p-2 bg-blue-100 rounded-lg">
                        <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <div>
                        <div class="text-sm font-semibold text-blue-900 group-hover:text-blue-700">Download Dokumen</div>
                        <div class="text-xs text-blue-600">Klik untuk mengunduh file lampiran</div>
                    </div>
                </a>
            </div>
            @endif
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Aksi</h3>
        
        <div class="flex flex-wrap gap-3">
            <!-- Create Ticket -->
            @can('tickets.create')
            <a href="{{ route('tickets.createFromRab', $rab) }}" 
               class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white text-sm font-semibold rounded-xl hover:shadow-lg hover:scale-105 active:scale-95 transition-all duration-300">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                </svg>
                <span>Buat Tiket Permintaan Dana</span>
            </a>
            @endcan

            <!-- Edit -->
            @can('finance.manage_rab')
            <a href="{{ route('rabs.edit', $rab) }}" 
               class="inline-flex items-center gap-2 px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-xl transition-all duration-300">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                <span>Edit RAB</span>
            </a>

            <!-- Approve -->
            @if($rab->funds_status !== 'approved')
            <form action="{{ route('rabs.approve', $rab) }}" method="POST" class="inline-block">
                @csrf
                <button type="submit" 
                        class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-green-600 to-emerald-600 text-white text-sm font-semibold rounded-xl hover:shadow-lg hover:scale-105 active:scale-95 transition-all duration-300">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Setujui RAB</span>
                </button>
            </form>
            @endif

            <!-- Reject -->
            @if($rab->funds_status !== 'rejected')
            <form action="{{ route('rabs.reject', $rab) }}" method="POST" class="inline-block">
                @csrf
                <button type="submit" 
                        onclick="return confirm('Apakah Anda yakin ingin menolak RAB ini?')"
                        class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-red-600 to-pink-600 text-white text-sm font-semibold rounded-xl hover:shadow-lg hover:scale-105 active:scale-95 transition-all duration-300">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Tolak RAB</span>
                </button>
            </form>
            @endif

            <!-- Delete -->
            <form action="{{ route('rabs.destroy', $rab) }}" method="POST" class="inline-block">
                @csrf
                @method('DELETE')
                <button type="submit" 
                        onclick="return confirm('Apakah Anda yakin ingin menghapus RAB ini? Tindakan ini tidak dapat dibatalkan.')"
                        class="inline-flex items-center gap-2 px-6 py-3 bg-gray-200 hover:bg-red-100 text-gray-700 hover:text-red-600 text-sm font-semibold rounded-xl transition-all duration-300">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    <span>Hapus RAB</span>
                </button>
            </form>
            @endcan
        </div>
    </div>
</div>
@endsection
