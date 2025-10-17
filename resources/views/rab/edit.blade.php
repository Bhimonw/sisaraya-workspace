@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Back Button -->
    <div class="mb-6">
        <a href="{{ route('rabs.show', $rab) }}" 
           class="inline-flex items-center gap-2 text-gray-600 hover:text-emerald-600 transition-colors">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            <span class="font-medium">Kembali ke Detail RAB</span>
        </a>
    </div>

    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center gap-4">
            <div class="p-3 bg-gradient-to-br from-amber-600 to-orange-600 rounded-xl shadow-lg">
                <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
            </div>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Edit RAB</h1>
                <p class="text-gray-600 mt-1">Perbarui informasi Rencana Anggaran Biaya</p>
            </div>
        </div>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="h-2 bg-gradient-to-r from-amber-600 via-orange-600 to-red-500"></div>
        
        <form action="{{ route('rabs.update', $rab) }}" method="POST" enctype="multipart/form-data" class="p-8">
            @csrf 
            @method('PUT')

            <!-- Current Status Badge -->
            <div class="mb-6 p-4 bg-gray-50 rounded-xl border border-gray-200">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-gray-700">Status Saat Ini:</span>
                    <x-rab-status-badge :status="$rab->funds_status ?? 'draft'" size="md" />
                </div>
            </div>

            <!-- Title -->
            <div class="mb-6">
                <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                    Judul RAB <span class="text-red-500">*</span>
                </label>
                <input 
                    type="text" 
                    name="title" 
                    id="title" 
                    value="{{ old('title', $rab->title) }}"
                    required
                    class="w-full border border-gray-300 rounded-xl py-3 px-4 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all @error('title') border-red-500 @enderror"
                    placeholder="Contoh: RAB Kegiatan Workshop 2025"
                >
                @error('title')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Project Selection -->
            <div class="mb-6">
                <label for="project_id" class="block text-sm font-medium text-gray-700 mb-2">
                    Proyek <span class="text-gray-400">(Opsional)</span>
                </label>
                <div class="relative">
                    <select 
                        name="project_id" 
                        id="project_id"
                        class="w-full border border-gray-300 rounded-xl py-3 px-4 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all appearance-none @error('project_id') border-red-500 @enderror"
                    >
                        <option value="">- Pilih Proyek (Jika Ada) -</option>
                        @foreach($projects as $project)
                        <option value="{{ $project->id }}" {{ old('project_id', $rab->project_id) == $project->id ? 'selected' : '' }}>
                            {{ $project->name }}
                        </option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </div>
                @error('project_id')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Amount using component -->
            <x-currency-input 
                name="amount" 
                label="Jumlah Anggaran" 
                :value="old('amount', $rab->amount)"
                :required="true"
                help-text="Masukkan nominal tanpa titik atau koma, sistem akan memformatnya otomatis"
            />

            <!-- Description -->
            <div class="mb-6">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                    Deskripsi
                </label>
                <textarea 
                    name="description" 
                    id="description" 
                    rows="6"
                    class="w-full border border-gray-300 rounded-xl py-3 px-4 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all @error('description') border-red-500 @enderror"
                    placeholder="Jelaskan rincian anggaran dan tujuan penggunaan dana..."
                >{{ old('description', $rab->description) }}</textarea>
                @error('description')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- File Upload -->
            <div class="mb-8">
                <label for="file" class="block text-sm font-medium text-gray-700 mb-2">
                    Lampiran Dokumen <span class="text-gray-400">(PDF, JPG, PNG - Max 2MB)</span>
                </label>
                
                @if($rab->file_path)
                <div class="mb-3 p-4 bg-blue-50 rounded-xl border border-blue-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-blue-100 rounded-lg">
                                <svg class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div>
                                <div class="text-sm font-medium text-blue-900">Dokumen Saat Ini</div>
                                <div class="text-xs text-blue-600">Sudah ada file terupload</div>
                            </div>
                        </div>
                        <a href="{{ url('storage/'.$rab->file_path) }}" 
                           target="_blank"
                           class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-100 hover:bg-blue-200 text-blue-700 text-xs font-semibold rounded-lg transition-colors">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            Download
                        </a>
                    </div>
                </div>
                @endif
                
                <div class="relative">
                    <input 
                        type="file" 
                        name="file" 
                        id="file"
                        accept=".pdf,.jpg,.jpeg,.png"
                        class="w-full border border-gray-300 rounded-xl py-3 px-4 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-amber-50 file:text-amber-700 hover:file:bg-amber-100 @error('file') border-red-500 @enderror"
                    >
                </div>
                @error('file')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
                <p class="text-xs text-gray-500 mt-2">
                    @if($rab->file_path)
                        Upload file baru untuk mengganti dokumen yang sudah ada
                    @else
                        Upload dokumen pendukung seperti rincian RAB, proposal, atau dokumen terkait lainnya
                    @endif
                </p>
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-3 pt-6 border-t border-gray-200">
                <button 
                    type="submit"
                    class="inline-flex items-center justify-center gap-2 px-8 py-3 bg-gradient-to-r from-amber-600 to-orange-600 text-white text-sm font-semibold rounded-xl hover:shadow-lg hover:scale-105 active:scale-95 transition-all duration-300">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>Simpan Perubahan</span>
                </button>
                
                <a href="{{ route('rabs.show', $rab) }}" 
                   class="inline-flex items-center justify-center gap-2 px-8 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-xl transition-all duration-300">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    <span>Batal</span>
                </a>
            </div>
        </form>
    </div>

    <!-- Warning Card -->
    <div class="mt-6 bg-yellow-50 rounded-xl border border-yellow-200 p-6">
        <div class="flex items-start gap-3">
            <div class="p-2 bg-yellow-100 rounded-lg flex-shrink-0">
                <svg class="h-5 w-5 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <div>
                <h4 class="text-sm font-semibold text-yellow-900 mb-2">Perhatian</h4>
                <ul class="text-sm text-yellow-800 space-y-1">
                    <li>• Pastikan data yang diinput sudah benar sebelum menyimpan</li>
                    <li>• Jika RAB sudah disetujui, perubahan mungkin memerlukan persetujuan ulang</li>
                    <li>• File lama akan digantikan jika Anda upload file baru</li>
                </ul>
            </div>
        </div>
    </div>
</div>

@push('scripts')
@endpush
@endsection
