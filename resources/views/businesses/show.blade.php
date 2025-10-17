@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4 max-w-6xl">
    <!-- Success Message -->
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Business Info Card -->
            <div class="bg-white rounded-lg shadow p-6">
                <!-- Header with status badge -->
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">{{ $business->name }}</h1>
                        <span class="inline-block mt-2 px-3 py-1 text-sm rounded bg-{{ $business->getStatusColor() }}-100 text-{{ $business->getStatusColor() }}-800 font-semibold">
                            {{ $business->getStatusLabel() }}
                        </span>
                    </div>

                    <!-- Approval buttons for PM -->
                    @can('approve', $business)
                        <div class="flex gap-2">
                            <button onclick="document.getElementById('approveForm').submit()" 
                                    class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition">
                                ✓ Setujui
                            </button>
                            <button onclick="showRejectModal()" 
                                    class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition">
                                ✕ Tolak
                            </button>
                        </div>

                        <!-- Hidden approve form -->
                        <form id="approveForm" method="POST" action="{{ route('businesses.approve', $business) }}" class="hidden">
                            @csrf
                        </form>
                    @endcan
                </div>

                <!-- Business details -->
                <div class="space-y-4">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Deskripsi Usaha
                        </h3>
                        <p class="text-gray-800 bg-gray-50 p-3 rounded">{{ $business->description ?? 'Tidak ada deskripsi' }}</p>
                    </div>

                    <div class="grid grid-cols-2 gap-4 text-sm border-t pt-4">
                        <div class="flex items-center gap-2">
                            <svg class="h-4 w-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <div>
                                <span class="text-gray-600">Dibuat oleh:</span>
                                <span class="font-medium block">{{ $business->creator->name }}</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="h-4 w-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <div>
                                <span class="text-gray-600">Tanggal dibuat:</span>
                                <span class="font-medium block">{{ $business->created_at->format('d M Y') }}</span>
                            </div>
                        </div>
                    </div>

                    @if($business->approver)
                        <div class="grid grid-cols-2 gap-4 text-sm bg-blue-50 p-4 rounded border border-blue-200">
                            <div class="flex items-center gap-2">
                                <svg class="h-4 w-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <div>
                                    <span class="text-gray-600">{{ $business->isApproved() ? 'Disetujui' : 'Ditolak' }} oleh:</span>
                                    <span class="font-medium block">{{ $business->approver->name }}</span>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <svg class="h-4 w-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <div>
                                    <span class="text-gray-600">Tanggal:</span>
                                    <span class="font-medium block">{{ $business->approved_at->format('d M Y, H:i') }}</span>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($business->isApproved() && $business->project)
                        <div class="bg-green-50 border-2 border-green-200 rounded-lg p-4">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-2">
                                        <svg class="h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <h4 class="text-sm font-bold text-green-800">Proyek Terkait</h4>
                                    </div>
                                    <p class="text-green-700 text-sm mb-3">Usaha ini telah disetujui dan proyek telah dibuat.</p>
                                    <div class="bg-white border border-green-200 rounded px-3 py-2">
                                        <span class="text-xs text-gray-600">Nama Proyek:</span>
                                        <p class="font-semibold text-gray-900">{{ $business->project->name }}</p>
                                    </div>
                                </div>
                                <a href="{{ route('projects.show', $business->project) }}" 
                                   class="ml-4 inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-semibold rounded-lg hover:bg-green-700 transition shadow">
                                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                    </svg>
                                    Buka Proyek
                                </a>
                            </div>
                        </div>
                    @endif

                    @if($business->isRejected() && $business->rejection_reason)
                        <div class="bg-red-50 border-2 border-red-200 rounded-lg p-4">
                            <div class="flex items-start gap-3">
                                <svg class="h-5 w-5 text-red-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <div class="flex-1">
                                    <h4 class="text-sm font-bold text-red-800 mb-2">Alasan Penolakan:</h4>
                                    <p class="text-red-700">{{ $business->rejection_reason }}</p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="mt-6 pt-4 border-t">
                    <a href="{{ route('businesses.index') }}" class="text-blue-600 hover:text-blue-800 inline-flex items-center gap-2 text-sm font-medium">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Kembali ke daftar usaha
                    </a>
                </div>
            </div>

            <!-- Reports List -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Laporan Usaha ({{ $business->reports->count() }})
                </h2>

                @if($business->reports->isEmpty())
                    <div class="text-center py-8 bg-gray-50 rounded">
                        <svg class="h-12 w-12 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <p class="text-gray-500">Belum ada laporan</p>
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach($business->reports()->latest()->get() as $report)
                            <div class="border rounded-lg p-4 hover:bg-gray-50 transition">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-1">
                                            <h3 class="font-semibold text-gray-900">{{ $report->title }}</h3>
                                            <span class="px-2 py-0.5 text-xs rounded bg-{{ $report->report_type_color }}-100 text-{{ $report->report_type_color }}-700">
                                                {{ $report->report_type_label }}
                                            </span>
                                        </div>
                                        @if($report->description)
                                            <p class="text-sm text-gray-600 mb-2">{{ $report->description }}</p>
                                        @endif
                                        <div class="flex items-center gap-4 text-xs text-gray-500">
                                            <span class="flex items-center gap-1">
                                                <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                                {{ $report->report_date->format('d M Y') }}
                                            </span>
                                            <span class="flex items-center gap-1">
                                                <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                </svg>
                                                {{ $report->uploader->name }}
                                            </span>
                                            <span class="flex items-center gap-1">
                                                <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                                </svg>
                                                {{ $report->formatted_file_size }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex gap-2 ml-4">
                                        <a href="{{ route('businesses.reports.download', [$business, $report]) }}" 
                                           class="px-3 py-1.5 bg-blue-600 text-white text-sm rounded hover:bg-blue-700 transition flex items-center gap-1">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                            </svg>
                                            Download
                                        </a>
                                        @if($report->user_id === auth()->id() || auth()->user()->hasRole('pm'))
                                            <form method="POST" action="{{ route('businesses.reports.destroy', [$business, $report]) }}" 
                                                  onsubmit="return confirm('Hapus laporan ini?')" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="px-3 py-1.5 bg-red-600 text-white text-sm rounded hover:bg-red-700 transition">
                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1">
            <!-- Upload Report Card -->
            @canany(['update'], $business)
            <div class="bg-white rounded-lg shadow p-6 sticky top-4">
                <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                    </svg>
                    Upload Laporan
                </h2>

                <form method="POST" action="{{ route('businesses.reports.store', $business) }}" 
                      enctype="multipart/form-data" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Judul Laporan *</label>
                        <input type="text" name="title" required
                               class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="Laporan Penjualan Bulan Ini">
                        @error('title')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Laporan *</label>
                        <select name="report_type" required
                                class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="penjualan">Laporan Penjualan</option>
                            <option value="keuangan">Laporan Keuangan</option>
                            <option value="operasional">Laporan Operasional</option>
                            <option value="lainnya">Laporan Lainnya</option>
                        </select>
                        @error('report_type')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Laporan *</label>
                        <input type="date" name="report_date" required
                               value="{{ date('Y-m-d') }}"
                               class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('report_date')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                        <textarea name="description" rows="3"
                                  class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                  placeholder="Keterangan tambahan (opsional)"></textarea>
                        @error('description')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">File Laporan * (Max 10MB)</label>
                        <input type="file" name="file" required
                               accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png"
                               class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <p class="text-xs text-gray-500 mt-1">Format: PDF, Word, Excel, atau Gambar</p>
                        @error('file')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" 
                            class="w-full bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition font-semibold flex items-center justify-center gap-2">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                        </svg>
                        Upload Laporan
                    </button>
                </form>
            </div>
            @endcanany
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl p-6 max-w-md w-full mx-4">
        <h3 class="text-lg font-bold mb-4">Tolak Usaha</h3>
        <form method="POST" action="{{ route('businesses.reject', $business) }}">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Alasan Penolakan *</label>
                <textarea name="rejection_reason" rows="4" required
                          class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-500"
                          placeholder="Jelaskan alasan penolakan..."></textarea>
            </div>
            <div class="flex gap-2 justify-end">
                <button type="button" onclick="hideRejectModal()" 
                        class="px-4 py-2 border rounded hover:bg-gray-100">
                    Batal
                </button>
                <button type="submit" 
                        class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                    Tolak Usaha
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function showRejectModal() {
    document.getElementById('rejectModal').classList.remove('hidden');
}

function hideRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
}

// Close modal on outside click
document.getElementById('rejectModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        hideRejectModal();
    }
});
</script>
@endsection
