@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <div class="bg-white rounded-lg shadow p-6">
        <!-- Header with status badge -->
        <div class="flex items-start justify-between mb-4">
            <div>
                <h1 class="text-2xl font-bold">{{ $business->name }}</h1>
                <span class="inline-block mt-2 px-3 py-1 text-sm rounded bg-{{ $business->getStatusColor() }}-100 text-{{ $business->getStatusColor() }}-800">
                    {{ $business->getStatusLabel() }}
                </span>
            </div>

            <!-- Approval buttons for PM -->
            @can('approve', $business)
                <div class="flex gap-2">
                    <button onclick="document.getElementById('approveForm').submit()" 
                            class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                        Setujui
                    </button>
                    <button onclick="showRejectModal()" 
                            class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
                        Tolak
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
                <h3 class="text-sm font-semibold text-gray-700 mb-1">Deskripsi</h3>
                <p class="text-gray-800">{{ $business->description ?? 'Tidak ada deskripsi' }}</p>
            </div>

            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <span class="text-gray-600">Dibuat oleh:</span>
                    <span class="font-medium">{{ $business->creator->name }}</span>
                </div>
                <div>
                    <span class="text-gray-600">Tanggal dibuat:</span>
                    <span class="font-medium">{{ $business->created_at->format('d M Y') }}</span>
                </div>
            </div>

            @if($business->approver)
                <div class="grid grid-cols-2 gap-4 text-sm bg-gray-50 p-3 rounded">
                    <div>
                        <span class="text-gray-600">Disetujui/Ditolak oleh:</span>
                        <span class="font-medium">{{ $business->approver->name }}</span>
                    </div>
                    <div>
                        <span class="text-gray-600">Tanggal:</span>
                        <span class="font-medium">{{ $business->approved_at->format('d M Y, H:i') }}</span>
                    </div>
                </div>
            @endif

            @if($business->isApproved() && $business->project)
                <div class="bg-green-50 border border-green-200 rounded p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="text-sm font-semibold text-green-800 mb-1">Proyek Terkait</h4>
                            <p class="text-green-700 mb-2">Usaha ini telah disetujui dan proyek telah dibuat.</p>
                            <p class="text-sm text-gray-700">
                                <span class="font-medium">Nama Proyek:</span> {{ $business->project->name }}
                            </p>
                        </div>
                        <a href="{{ route('projects.show', $business->project) }}" 
                           class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-semibold rounded-lg hover:bg-green-700 transition">
                            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                            Buka Proyek
                        </a>
                    </div>
                </div>
            @endif

            @if($business->isRejected() && $business->rejection_reason)
                <div class="bg-red-50 border border-red-200 rounded p-3">
                    <h4 class="text-sm font-semibold text-red-800 mb-1">Alasan Penolakan:</h4>
                    <p class="text-red-700">{{ $business->rejection_reason }}</p>
                </div>
            @endif
        </div>

        <div class="mt-6">
            <a href="{{ route('businesses.index') }}" class="text-blue-600 hover:text-blue-800">← Kembali ke daftar usaha</a>
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
