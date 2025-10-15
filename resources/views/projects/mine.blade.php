@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Header --}}
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-gray-900 mb-2">Proyekku</h1>
            <p class="text-lg text-gray-600">Kelola proyek yang sedang aktif</p>
        </div>

        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-800 rounded-r-lg shadow-sm">
                <div class="flex items-center">
                    <svg class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-800 rounded-r-lg shadow-sm">
                <div class="flex items-center">
                    <svg class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    <span class="font-medium">{{ session('error') }}</span>
                </div>
            </div>
        @endif

        {{-- Statistics Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            {{-- Total Proyek --}}
            <div class="bg-white rounded-xl shadow-md border border-gray-200 p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-2">Total Proyekku</p>
                        <p class="text-5xl font-bold text-blue-600">{{ $myProjects->count() }}</p>
                    </div>
                    <div class="bg-blue-100 rounded-full p-4">
                        <svg class="h-10 w-10 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Planning --}}
            <div class="bg-white rounded-xl shadow-md border border-gray-200 p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-2">Planning</p>
                        <p class="text-5xl font-bold text-amber-600">{{ $myProjects->where('status', 'planning')->count() }}</p>
                    </div>
                    <div class="bg-amber-100 rounded-full p-4">
                        <svg class="h-10 w-10 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Active --}}
            <div class="bg-white rounded-xl shadow-md border border-gray-200 p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-2">Active</p>
                        <p class="text-5xl font-bold text-green-600">{{ $myProjects->where('status', 'active')->count() }}</p>
                    </div>
                    <div class="bg-green-100 rounded-full p-4">
                        <svg class="h-10 w-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        {{-- 2 Column Layout: Proyekku | Tersedia --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- PROYEKKU COLUMN --}}
            <div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden">
                {{-- Header --}}
                <div class="bg-gradient-to-r from-blue-500 to-cyan-500 px-5 py-4 border-b-4 border-blue-600">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="bg-blue-600 bg-opacity-30 rounded-lg p-2 border border-blue-700 border-opacity-30">
                                <svg class="h-6 w-6 text-white drop-shadow-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <h2 class="text-xl font-bold text-white drop-shadow-sm">Proyekku</h2>
                        </div>
                        <div class="bg-white px-4 py-1.5 rounded-full shadow-sm">
                            <span class="text-lg font-bold text-blue-600">{{ $myProjects->count() }}</span>
                        </div>
                    </div>
                </div>

                {{-- My Projects List --}}
                <div class="bg-gray-50 p-4 space-y-3 max-h-[800px] overflow-y-auto">
                    @forelse($myProjects as $project)
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 hover:shadow-md hover:border-blue-300 transition-all">
                            <a href="{{ route('projects.show', $project) }}" class="block">
                                <div class="mb-3">
                                    <h3 class="text-base font-bold text-gray-900 mb-2 leading-tight">{{ $project->name }}</h3>
                                    
                                    @if($project->description)
                                        <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ $project->description }}</p>
                                    @endif
                                    
                                    <div class="flex flex-wrap gap-2 mb-3">
                                        {{-- Status Badge --}}
                                        <span class="inline-flex items-center px-2.5 py-1 text-xs font-bold rounded-md
                                            {{ $project->status === 'planning' ? 'bg-amber-200 text-amber-800' : '' }}
                                            {{ $project->status === 'active' ? 'bg-green-200 text-green-800' : '' }}
                                            {{ $project->status === 'on_hold' ? 'bg-gray-200 text-gray-800' : '' }}
                                            {{ $project->status === 'completed' ? 'bg-blue-200 text-blue-800' : '' }}">
                                            @if($project->status === 'planning') 📋 Planning
                                            @elseif($project->status === 'active') ⚡ Active
                                            @elseif($project->status === 'on_hold') ⏸️ On Hold
                                            @elseif($project->status === 'completed') ✅ Completed
                                            @endif
                                        </span>

                                        {{-- Ticket Count --}}
                                        <span class="inline-flex items-center px-2.5 py-1 text-xs font-bold rounded-md bg-purple-200 text-purple-800">
                                            🎫 {{ $project->tickets_count }} tiket
                                        </span>

                                        {{-- Member Count --}}
                                        <span class="inline-flex items-center px-2.5 py-1 text-xs font-bold rounded-md bg-indigo-200 text-indigo-800">
                                            👥 {{ $project->members->count() }} anggota
                                        </span>

                                        {{-- Public/Private Badge --}}
                                        @if($project->is_public)
                                            <span class="inline-flex items-center px-2.5 py-1 text-xs font-bold rounded-md bg-green-200 text-green-800">
                                                🌐 Publik
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-1 text-xs font-bold rounded-md bg-red-200 text-red-800">
                                                🔒 Privat
                                            </span>
                                        @endif
                                    </div>

                                    {{-- Dates --}}
                                    <div class="text-xs text-gray-500 space-y-1">
                                        @if($project->start_date && $project->end_date)
                                            <div class="flex items-center gap-1">
                                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                </svg>
                                                <span>{{ $project->start_date->format('d M Y') }} - {{ $project->end_date->format('d M Y') }}</span>
                                            </div>
                                        @endif
                                        <div class="flex items-center gap-1">
                                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                            </svg>
                                            <span>Owner: {{ $project->owner->name }}</span>
                                        </div>
                                    </div>
                                </div>
                            </a>

                            {{-- Action Buttons --}}
                            <div class="flex gap-2 pt-3 border-t border-gray-200">
                                <a href="{{ route('projects.show', $project) }}" 
                                   class="flex-1 px-3 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors text-center">
                                    👁️ Lihat Detail
                                </a>
                                @if($project->owner_id === auth()->id())
                                    <a href="{{ route('projects.edit', $project) }}" 
                                       class="flex-1 px-3 py-2 bg-amber-600 text-white text-sm font-medium rounded-lg hover:bg-amber-700 transition-colors text-center">
                                        ✏️ Edit
                                    </a>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12">
                            <div class="bg-gray-200 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-3">
                                <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <p class="text-sm font-semibold text-gray-500">Belum ada proyek aktif</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- TERSEDIA COLUMN --}}
            <div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden">
                {{-- Header --}}
                <div class="bg-gradient-to-r from-green-500 to-teal-500 px-5 py-4 border-b-4 border-green-600">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="bg-green-600 bg-opacity-30 rounded-lg p-2 border border-green-700 border-opacity-30">
                                <svg class="h-6 w-6 text-white drop-shadow-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                            </div>
                            <h2 class="text-xl font-bold text-white drop-shadow-sm">Proyek Tersedia</h2>
                        </div>
                        <div class="bg-white px-4 py-1.5 rounded-full shadow-sm">
                            <span class="text-lg font-bold text-green-600">{{ $availableProjects->count() }}</span>
                        </div>
                    </div>
                </div>

                {{-- Available Projects List --}}
                <div class="bg-gray-50 p-4 space-y-3 max-h-[800px] overflow-y-auto">
                    @forelse($availableProjects as $project)
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 hover:shadow-md hover:border-green-300 transition-all">
                            <div class="mb-3">
                                <h3 class="text-base font-bold text-gray-900 mb-2 leading-tight">{{ $project->name }}</h3>
                                
                                @if($project->description)
                                    <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ $project->description }}</p>
                                @endif
                                
                                <div class="flex flex-wrap gap-2 mb-3">
                                    {{-- Status Badge --}}
                                    <span class="inline-flex items-center px-2.5 py-1 text-xs font-bold rounded-md
                                        {{ $project->status === 'planning' ? 'bg-amber-200 text-amber-800' : '' }}
                                        {{ $project->status === 'active' ? 'bg-green-200 text-green-800' : '' }}">
                                        @if($project->status === 'planning') 📋 Planning
                                        @elseif($project->status === 'active') ⚡ Active
                                        @endif
                                    </span>

                                    {{-- Ticket Count --}}
                                    <span class="inline-flex items-center px-2.5 py-1 text-xs font-bold rounded-md bg-purple-200 text-purple-800">
                                        🎫 {{ $project->tickets_count }} tiket
                                    </span>

                                    {{-- Member Count --}}
                                    <span class="inline-flex items-center px-2.5 py-1 text-xs font-bold rounded-md bg-indigo-200 text-indigo-800">
                                        👥 {{ $project->members->count() }} anggota
                                    </span>

                                    {{-- Public Badge --}}
                                    <span class="inline-flex items-center px-2.5 py-1 text-xs font-bold rounded-md bg-green-200 text-green-800">
                                        🌐 Publik
                                    </span>
                                </div>

                                {{-- Dates --}}
                                <div class="text-xs text-gray-500 space-y-1">
                                    @if($project->start_date && $project->end_date)
                                        <div class="flex items-center gap-1">
                                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                            <span>{{ $project->start_date->format('d M Y') }} - {{ $project->end_date->format('d M Y') }}</span>
                                        </div>
                                    @endif
                                    <div class="flex items-center gap-1">
                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                        <span>Owner: {{ $project->owner->name }}</span>
                                    </div>
                                </div>
                            </div>

                            {{-- Action Button --}}
                            <div class="pt-3 border-t border-gray-200">
                                <a href="{{ route('projects.show', $project) }}" 
                                   class="block w-full px-3 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors text-center">
                                    👁️ Lihat Detail
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12">
                            <div class="bg-gray-200 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-3">
                                <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <p class="text-sm font-semibold text-gray-500">Tidak ada proyek publik tersedia</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
