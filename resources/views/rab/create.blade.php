@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto">
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

    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center gap-4">
            <div class="p-3 bg-gradient-to-br from-emerald-600 to-teal-600 rounded-xl shadow-lg">
                <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                </svg>
            </div>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Buat RAB Baru</h1>
                <p class="text-gray-600 mt-1">Rencana Anggaran Biaya untuk proyek atau kegiatan</p>
            </div>
        </div>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="h-2 bg-gradient-to-r from-emerald-600 via-teal-600 to-cyan-500"></div>
        
        <form action="{{ route('rabs.store') }}" method="POST" enctype="multipart/form-data" class="p-8">
            @csrf

            <!-- Title -->
            <div class="mb-6">
                <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                    Judul RAB <span class="text-red-500">*</span>
                </label>
                <input 
                    type="text" 
                    name="title" 
                    id="title" 
                    value="{{ old('title') }}"
                    required
                    class="w-full border border-gray-300 rounded-xl py-3 px-4 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all @error('title') border-red-500 @enderror"
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
                        class="w-full border border-gray-300 rounded-xl py-3 px-4 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all appearance-none @error('project_id') border-red-500 @enderror"
                    >
                        <option value="">- Pilih Proyek (Jika Ada) -</option>
                        @foreach($projects as $project)
                        <option value="{{ $project->id }}" {{ old('project_id') == $project->id ? 'selected' : '' }}>
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
                <p class="text-xs text-gray-500 mt-2">RAB dapat dikaitkan dengan proyek tertentu atau berdiri sendiri</p>
            </div>

            <!-- Amount using component -->
            <x-currency-input 
                name="amount" 
                label="Jumlah Anggaran" 
                :value="old('amount', 0)"
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
                    class="w-full border border-gray-300 rounded-xl py-3 px-4 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all @error('description') border-red-500 @enderror"
                    placeholder="Jelaskan rincian anggaran dan tujuan penggunaan dana..."
                >{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- File Upload -->
            <div class="mb-8">
                <label for="file" class="block text-sm font-medium text-gray-700 mb-2">
                    Lampiran Dokumen <span class="text-gray-400">(PDF, JPG, PNG - Max 2MB)</span>
                </label>
                <div class="relative">
                    <input 
                        type="file" 
                        name="file" 
                        id="file"
                        accept=".pdf,.jpg,.jpeg,.png"
                        class="w-full border border-gray-300 rounded-xl py-3 px-4 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 @error('file') border-red-500 @enderror"
                    >
                </div>
                @error('file')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
                <p class="text-xs text-gray-500 mt-2">Upload dokumen pendukung seperti rincian RAB, proposal, atau dokumen terkait lainnya</p>
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-3 pt-6 border-t border-gray-200">
                <button 
                    type="submit"
                    class="inline-flex items-center justify-center gap-2 px-8 py-3 bg-gradient-to-r from-emerald-600 to-teal-600 text-white text-sm font-semibold rounded-xl hover:shadow-lg hover:scale-105 active:scale-95 transition-all duration-300">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>Simpan RAB</span>
                </button>
                
                <a href="{{ route('rabs.index') }}" 
                   class="inline-flex items-center justify-center gap-2 px-8 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-xl transition-all duration-300">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    <span>Batal</span>
                </a>
            </div>
        </form>
    </div>

    <!-- Help Card -->
    <div class="mt-6 bg-blue-50 rounded-xl border border-blue-200 p-6">
        <div class="flex items-start gap-3">
            <div class="p-2 bg-blue-100 rounded-lg flex-shrink-0">
                <svg class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <h4 class="text-sm font-semibold text-blue-900 mb-2">Tips Membuat RAB</h4>
                <ul class="text-sm text-blue-800 space-y-1">
                    <li>• Berikan judul yang jelas dan deskriptif</li>
                    <li>• Rincikan anggaran secara detail di bagian deskripsi</li>
                    <li>• Upload dokumen pendukung untuk transparansi</li>
                    <li>• RAB akan berstatus "Draft" sampai disetujui oleh bendahara</li>
                </ul>
            </div>
        </div>
    </div>
</div>

@push('scripts')
@endpush
@endsection
