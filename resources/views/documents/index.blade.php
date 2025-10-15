<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                @if($type === 'confidential')
                    Dokumen Rahasia
                @else
                    Dokumen Umum
                @endif
            </h2>
            <a href="{{ route('documents.create', ['type' => $type]) }}" 
               class="inline-flex items-center rounded-lg bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">
                <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                </svg>
                Upload Dokumen
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 rounded-lg bg-green-50 p-4 text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Tab Navigation -->
            <div class="mb-6 flex gap-2 border-b border-gray-200">
                <a href="{{ route('documents.index', ['type' => 'public']) }}" 
                   class="px-4 py-2 -mb-px {{ $type === 'public' ? 'border-b-2 border-blue-600 text-blue-600' : 'text-gray-600 hover:text-gray-900' }}">
                    Dokumen Umum
                </a>
                @role('sekretaris|hr')
                    <a href="{{ route('documents.index', ['type' => 'confidential']) }}" 
                       class="px-4 py-2 -mb-px {{ $type === 'confidential' ? 'border-b-2 border-red-600 text-red-600' : 'text-gray-600 hover:text-gray-900' }}">
                        Dokumen Rahasia
                    </a>
                @endrole
            </div>

            @if($docs->isEmpty())
                <div class="rounded-lg bg-white p-12 text-center shadow-sm">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada dokumen</h3>
                    <p class="mt-1 text-sm text-gray-500">Mulai dengan mengupload dokumen pertama.</p>
                </div>
            @else
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach($docs as $doc)
                        <div class="rounded-lg bg-white p-6 shadow-sm hover:shadow-md transition-shadow">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-2">
                                        <!-- File Icon -->
                                        <svg class="h-8 w-8 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/>
                                        </svg>
                                        
                                        @if($doc->is_confidential)
                                            <span class="rounded-full bg-red-100 px-2 py-0.5 text-xs font-medium text-red-800">
                                                RAHASIA
                                            </span>
                                        @endif
                                    </div>
                                    
                                    <h3 class="font-semibold text-gray-900 mb-1 break-words">{{ $doc->name }}</h3>
                                    
                                    @if($doc->description)
                                        <p class="text-sm text-gray-600 mb-2">{{ Str::limit($doc->description, 80) }}</p>
                                    @endif
                                    
                                    <div class="text-xs text-gray-500 space-y-1">
                                        <div>Diupload: {{ $doc->user->name ?? 'Unknown' }}</div>
                                        <div>{{ $doc->created_at->format('d M Y H:i') }}</div>
                                        @if($doc->project)
                                            <div class="flex items-center gap-1 text-blue-600">
                                                <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                                                </svg>
                                                {{ $doc->project->name }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-4">
                                <a href="{{ asset('storage/'.$doc->path) }}" 
                                   target="_blank"
                                   download
                                   class="inline-flex w-full items-center justify-center rounded-lg bg-blue-600 px-4 py-2 text-sm text-white hover:bg-blue-700">
                                    <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                    </svg>
                                    Download
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
