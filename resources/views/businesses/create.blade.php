@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4 max-w-4xl">
    <!-- Header with Back Button -->
    <div class="mb-8">
        <a href="{{ route('businesses.index') }}" 
           class="inline-flex items-center gap-2 text-gray-600 hover:text-gray-900 mb-4 transition">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            <span class="font-medium">Kembali ke Daftar Usaha</span>
        </a>
        
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-2xl shadow-xl p-8 text-white">
            <div class="flex items-center gap-4">
                <div class="bg-white bg-opacity-20 p-4 rounded-xl">
                    <svg class="h-10 w-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold">Buat Usaha Baru</h1>
                    <p class="text-blue-100 mt-1">Ajukan proposal usaha untuk persetujuan PM</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Info Alert -->
    <div class="bg-blue-50 border-2 border-blue-200 rounded-xl p-6 mb-8 flex gap-4">
        <svg class="h-6 w-6 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <div>
            <h3 class="text-blue-900 font-bold text-lg mb-2">Informasi Penting</h3>
            <ul class="text-blue-800 space-y-1 text-sm">
                <li class="flex items-start gap-2">
                    <span class="text-blue-600 font-bold">•</span>
                    <span>Usaha yang dibuat akan berstatus <strong>pending</strong> dan memerlukan persetujuan dari PM</span>
                </li>
                <li class="flex items-start gap-2">
                    <span class="text-blue-600 font-bold">•</span>
                    <span>Setelah disetujui, proyek akan <strong>otomatis dibuat</strong> dengan PM sebagai owner</span>
                </li>
                <li class="flex items-start gap-2">
                    <span class="text-blue-600 font-bold">•</span>
                    <span>Anda akan ditambahkan sebagai <strong>admin member</strong> di proyek tersebut</span>
                </li>
            </ul>
        </div>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-200">
        <form action="{{ route('businesses.store') }}" method="POST" class="p-8 space-y-8">
            @csrf

            <!-- Nama Usaha -->
            <div>
                <label for="name" class="block text-sm font-bold text-gray-900 mb-3">
                    <span class="flex items-center gap-2">
                        <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                        </svg>
                        Nama Usaha
                        <span class="text-red-500 text-lg">*</span>
                    </span>
                </label>
                <input type="text" 
                       id="name"
                       name="name" 
                       required
                       value="{{ old('name') }}"
                       placeholder="Contoh: Kaos Custom SISARAYA, Warung Kopi Kreatif, dll."
                       class="w-full px-5 py-4 text-lg border-2 border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-500 focus:ring-opacity-30 focus:border-blue-500 transition @error('name') border-red-500 ring-4 ring-red-500 ring-opacity-20 @enderror">
                @error('name')
                    <p class="text-red-600 text-sm mt-2 flex items-center gap-2 font-medium">
                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- Deskripsi -->
            <div>
                <label for="description" class="block text-sm font-bold text-gray-900 mb-3">
                    <span class="flex items-center gap-2">
                        <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Deskripsi Usaha
                        <span class="text-gray-500 text-xs font-normal">(Optional, tapi sangat direkomendasikan)</span>
                    </span>
                </label>
                <textarea id="description"
                          name="description" 
                          rows="8"
                          placeholder="Jelaskan dengan detail:
• Konsep dan visi usaha
• Produk/jasa yang ditawarkan
• Target pasar
• Rencana operasional
• Estimasi kebutuhan modal
• Tim yang terlibat

Semakin lengkap deskripsi, semakin cepat proses persetujuan."
                          class="w-full px-5 py-4 text-lg border-2 border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-500 focus:ring-opacity-30 focus:border-blue-500 transition resize-none @error('description') border-red-500 ring-4 ring-red-500 ring-opacity-20 @enderror">{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-red-600 text-sm mt-2 flex items-center gap-2 font-medium">
                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                        {{ $message }}
                    </p>
                @enderror
                <div class="flex items-start gap-2 mt-3 text-sm text-gray-600">
                    <svg class="h-4 w-4 mt-0.5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                    </svg>
                    <span><strong>Tips:</strong> Deskripsi yang detail dan jelas akan mempercepat proses review dan persetujuan dari PM.</span>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-between gap-4 pt-6 border-t-2 border-gray-200">
                <a href="{{ route('businesses.index') }}"
                   class="inline-flex items-center gap-2 px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition font-semibold text-lg">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    Batal
                </a>
                <button type="submit"
                        class="inline-flex items-center gap-3 px-8 py-4 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-xl hover:from-blue-700 hover:to-blue-800 transition font-bold shadow-xl hover:shadow-2xl transform hover:scale-105 text-lg">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Ajukan Usaha Baru
                </button>
            </div>
        </form>
    </div>

    <!-- Additional Info -->
    <div class="mt-8 bg-gray-50 rounded-xl p-6 border border-gray-200">
        <h3 class="font-bold text-gray-900 mb-3 flex items-center gap-2">
            <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Proses Selanjutnya
        </h3>
        <ol class="space-y-2 text-sm text-gray-700">
            <li class="flex items-start gap-3">
                <span class="flex items-center justify-center w-6 h-6 rounded-full bg-blue-600 text-white text-xs font-bold flex-shrink-0">1</span>
                <span>PM akan menerima notifikasi tentang pengajuan usaha baru</span>
            </li>
            <li class="flex items-start gap-3">
                <span class="flex items-center justify-center w-6 h-6 rounded-full bg-blue-600 text-white text-xs font-bold flex-shrink-0">2</span>
                <span>PM akan melakukan review dan evaluasi proposal usaha</span>
            </li>
            <li class="flex items-start gap-3">
                <span class="flex items-center justify-center w-6 h-6 rounded-full bg-blue-600 text-white text-xs font-bold flex-shrink-0">3</span>
                <span>Jika disetujui, proyek akan otomatis dibuat dan Anda bisa mulai bekerja</span>
            </li>
            <li class="flex items-start gap-3">
                <span class="flex items-center justify-center w-6 h-6 rounded-full bg-blue-600 text-white text-xs font-bold flex-shrink-0">4</span>
                <span>Jika ditolak, Anda akan menerima feedback dan bisa mengajukan kembali dengan perbaikan</span>
            </li>
        </ol>
    </div>
</div>
@endsection
