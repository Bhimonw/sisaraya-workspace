@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-2xl font-bold">Usaha Komunitas</h1>
        @can('business.create')
            <a href="{{ route('businesses.create') }}" class="bg-blue-600 text-white px-3 py-2 rounded">Buat Usaha Baru</a>
        @endcan
    </div>

    <!-- Filter by status -->
    <div class="mb-4 flex gap-2">
        <a href="{{ route('businesses.index') }}" 
           class="px-3 py-1 rounded {{ !request('status') ? 'bg-blue-600 text-white' : 'bg-gray-200' }}">
            Semua
        </a>
        <a href="{{ route('businesses.index', ['status' => 'pending']) }}" 
           class="px-3 py-1 rounded {{ request('status') === 'pending' ? 'bg-yellow-600 text-white' : 'bg-gray-200' }}">
            Menunggu Persetujuan
        </a>
        <a href="{{ route('businesses.index', ['status' => 'approved']) }}" 
           class="px-3 py-1 rounded {{ request('status') === 'approved' ? 'bg-green-600 text-white' : 'bg-gray-200' }}">
            Disetujui
        </a>
        <a href="{{ route('businesses.index', ['status' => 'rejected']) }}" 
           class="px-3 py-1 rounded {{ request('status') === 'rejected' ? 'bg-red-600 text-white' : 'bg-gray-200' }}">
            Ditolak
        </a>
    </div>

    <div class="mt-4 space-y-3">
        @forelse($businesses as $b)
            <div class="bg-white p-4 rounded shadow hover:shadow-md transition">
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <div class="flex items-center gap-2">
                            <div class="font-semibold"><a href="{{ route('businesses.show', $b) }}" class="hover:text-blue-600">{{ $b->name }}</a></div>
                            <span class="px-2 py-1 text-xs rounded bg-{{ $b->getStatusColor() }}-100 text-{{ $b->getStatusColor() }}-800">
                                {{ $b->getStatusLabel() }}
                            </span>
                            @if($b->project_id)
                                <a href="{{ route('projects.show', $b->project_id) }}" 
                                   class="inline-flex items-center px-2 py-1 text-xs bg-blue-100 text-blue-700 rounded hover:bg-blue-200"
                                   title="Lihat proyek terkait">
                                    <svg class="h-3 w-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                                    </svg>
                                    Proyek
                                </a>
                            @endif
                        </div>
                        <div class="text-xs text-gray-500 mt-1">{{ Str::limit($b->description, 120) }}</div>
                        <div class="text-xs text-gray-400 mt-2">
                            Dibuat oleh: {{ $b->creator->name }}
                            @if($b->approver)
                                | Disetujui oleh: {{ $b->approver->name }} pada {{ $b->approved_at->format('d M Y') }}
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-gray-50 p-8 rounded text-center text-gray-500">
                Belum ada usaha yang terdaftar.
            </div>
        @endforelse
    </div>

    <div class="mt-4">{{ $businesses->links() }}</div>
</div>
@endsection
