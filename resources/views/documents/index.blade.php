<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-gradient-to-br from-{{ $type === 'confidential' ? 'red-600 to-pink-600' : 'blue-600 to-cyan-600' }} rounded-xl shadow-lg">
                    <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        @if($type === 'confidential')
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        @else
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        @endif
                    </svg>
                </div>
                <div>
                    <h2 class="text-3xl font-bold text-gray-900">
                        @if($type === 'confidential')
                            Dokumen Rahasia
                        @else
                            Dokumen Umum
                        @endif
                    </h2>
                    <p class="text-gray-600 mt-1">
                        @if($type === 'confidential')
                            Dokumen rahasia yang hanya dapat diakses oleh pihak tertentu
                        @else
                            Dokumen yang dapat diakses oleh semua anggota komunitas
                        @endif
                    </p>
                </div>
            </div>
            
            <a href="{{ route('documents.create', ['type' => $type]) }}" 
               class="inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-{{ $type === 'confidential' ? 'red-600 to-pink-600' : 'blue-600 to-cyan-600' }} text-white text-sm font-semibold rounded-full hover:shadow-lg hover:scale-105 active:scale-95 transition-all duration-300 shadow-md">
                <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                </svg>
                Upload Dokumen
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-6 rounded-xl bg-green-50 border border-green-200 p-4 text-green-800 flex items-center gap-3">
                    <svg class="h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    {{ session('success') }}
                </div>
            @endif

            <!-- Tab Navigation -->
            <div class="mb-6 bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <nav class="flex">
                    <a href="{{ route('documents.index', ['type' => 'public']) }}" 
                       class="flex-1 text-center py-4 px-6 border-b-2 font-medium text-sm transition-colors
                              {{ $type === 'public' ? 'border-blue-600 text-blue-600 bg-blue-50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50' }}">
                        <div class="flex items-center justify-center gap-2">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                            <span>Dokumen Umum</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                         {{ $type === 'public' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-600' }}">
                                {{ \App\Models\Document::where('is_confidential', false)->count() }}
                            </span>
                        </div>
                    </a>
                    @role('sekretaris|hr')
                        <a href="{{ route('documents.index', ['type' => 'confidential']) }}" 
                           class="flex-1 text-center py-4 px-6 border-b-2 font-medium text-sm transition-colors
                                  {{ $type === 'confidential' ? 'border-red-600 text-red-600 bg-red-50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50' }}">
                            <div class="flex items-center justify-center gap-2">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                                <span>Dokumen Rahasia</span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                             {{ $type === 'confidential' ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-600' }}">
                                    {{ \App\Models\Document::where('is_confidential', true)->count() }}
                                </span>
                            </div>
                        </a>
                    @endrole
                </nav>
            </div>

            @if($docs->isEmpty())
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-16 text-center">
                    <div class="max-w-md mx-auto">
                        <div class="w-20 h-20 mx-auto mb-6 bg-gradient-to-br from-{{ $type === 'confidential' ? 'red-100 to-pink-100' : 'blue-100 to-cyan-100' }} rounded-full flex items-center justify-center">
                            <svg class="h-10 w-10 text-{{ $type === 'confidential' ? 'red' : 'blue' }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Belum Ada Dokumen</h3>
                        <p class="text-gray-600 mb-6">Mulai dengan mengupload dokumen pertama Anda</p>
                        <a href="{{ route('documents.create', ['type' => $type]) }}" 
                           class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-{{ $type === 'confidential' ? 'red-600 to-pink-600' : 'blue-600 to-cyan-600' }} text-white text-sm font-semibold rounded-full hover:shadow-lg transition-all duration-300">
                            <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                            </svg>
                            Upload Dokumen Pertama
                        </a>
                    </div>
                </div>
            @else
                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach($docs as $doc)
                        <div class="group bg-white rounded-2xl shadow-sm border border-gray-200 hover:shadow-xl hover:border-{{ $doc->is_confidential ? 'red' : 'blue' }}-200 transition-all duration-300 overflow-hidden">
                            <!-- Card Header dengan Gradient -->
                            <div class="bg-gradient-to-r from-{{ $doc->is_confidential ? 'red-50 to-pink-100' : 'blue-50 to-cyan-100' }} px-6 py-4 border-b border-{{ $doc->is_confidential ? 'red' : 'blue' }}-200">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex-shrink-0 p-2 bg-white rounded-lg shadow-sm">
                                        <svg class="h-8 w-8 text-{{ $doc->is_confidential ? 'red' : 'blue' }}-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    @if($doc->is_confidential)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-red-600 text-white">
                                            <svg class="h-3 w-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                            </svg>
                                            RAHASIA
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700 border border-blue-200">
                                            <svg class="h-3 w-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                            </svg>
                                            PUBLIK
                                        </span>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="p-6">
                                <h3 class="font-bold text-gray-900 mb-2 group-hover:text-{{ $doc->is_confidential ? 'red' : 'blue' }}-600 transition-colors line-clamp-2">
                                    {{ $doc->name }}
                                </h3>
                                
                                @if($doc->description)
                                    <p class="text-sm text-gray-600 mb-4 line-clamp-2 leading-relaxed">{{ $doc->description }}</p>
                                @else
                                    <p class="text-sm text-gray-400 italic mb-4">Tidak ada deskripsi</p>
                                @endif
                                
                                <!-- Meta Info -->
                                <div class="space-y-2 mb-4 pb-4 border-b border-gray-100">
                                    <div class="flex items-center gap-2 text-sm">
                                        <div class="p-1.5 bg-gray-50 rounded-lg">
                                            <svg class="h-4 w-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                            </svg>
                                        </div>
                                        <span class="text-gray-600">{{ $doc->user->name ?? 'Unknown' }}</span>
                                    </div>
                                    <div class="flex items-center gap-2 text-sm">
                                        <div class="p-1.5 bg-gray-50 rounded-lg">
                                            <svg class="h-4 w-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                        <span class="text-gray-600">{{ $doc->created_at->format('d M Y H:i') }}</span>
                                    </div>
                                    @if($doc->project)
                                        <div class="flex items-center gap-2 text-sm">
                                            <div class="p-1.5 bg-blue-50 rounded-lg">
                                                <svg class="h-4 w-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                                                </svg>
                                            </div>
                                            <span class="text-blue-600 font-medium">{{ $doc->project->name }}</span>
                                        </div>
                                    @endif
                                </div>
                                
                                <!-- Action Button -->
                                <a href="{{ asset('storage/'.$doc->path) }}" 
                                   target="_blank"
                                   download
                                   class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-gradient-to-r from-{{ $doc->is_confidential ? 'red-600 to-pink-600' : 'blue-600 to-cyan-600' }} text-white text-sm font-semibold rounded-xl hover:shadow-lg hover:scale-105 active:scale-95 transition-all duration-300">
                                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                    </svg>
                                    Download Dokumen
                                    <svg class="ml-auto h-4 w-4 group-hover:translate-y-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
