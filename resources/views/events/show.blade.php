@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    @include('partials.flash')
    <h1 class="text-xl sm:text-2xl font-bold">{{ $event->title }}</h1>
    <div class="text-xs sm:text-sm text-gray-600 mt-1">{{ $event->start_date }} – {{ $event->end_date }}</div>
    <div class="mt-4 text-sm sm:text-base">{{ $event->description }}</div>

    <div class="mt-6 bg-white p-4 sm:p-6 rounded shadow">
        <h3 class="font-semibold text-base sm:text-lg mb-3">Participants</h3>
        <form action="{{ route('events.attachParticipant', $event) }}" method="POST" class="mt-2">
            @csrf
            <div class="space-y-2 sm:space-y-0 sm:flex sm:gap-2">
                <select name="user_id" class="border border-gray-300 rounded p-2 w-full sm:flex-1 text-sm sm:text-base">
                    @foreach($users as $u)
                        <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->email }})</option>
                    @endforeach
                </select>
                <input type="text" name="role" placeholder="role (guest/staff)" 
                    class="border border-gray-300 rounded p-2 w-full sm:w-auto text-sm sm:text-base">
                <button class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm sm:text-base">
                    Add
                </button>
            </div>
        </form>

        <div class="mt-4 space-y-2">
            @foreach($event->participants as $p)
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 p-2 sm:p-0">
                    <div class="text-sm sm:text-base">
                        {{ $p->name }} <span class="text-xs text-gray-500">({{ $p->pivot->role }})</span>
                    </div>
                    <form action="{{ route('events.detachParticipant', [$event, $p]) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button class="text-red-600 hover:text-red-800 text-sm sm:text-base w-full sm:w-auto">
                            Remove
                        </button>
                    </form>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Projects Associated with Event --}}
    <div class="mt-6 bg-white p-4 sm:p-6 rounded shadow">
        <h3 class="font-semibold text-base sm:text-lg mb-3">
            <svg class="inline-block h-5 w-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            Proyek Terkait
        </h3>
        
        <form action="{{ route('events.attachProject', $event) }}" method="POST" class="mt-2">
            @csrf
            <div class="flex flex-col sm:flex-row gap-2">
                <select name="project_id" class="border border-gray-300 rounded px-3 py-2 flex-1 text-sm sm:text-base">
                    <option value="">-- Pilih Proyek --</option>
                    @foreach($projects as $proj)
                        @if(!$event->projects->contains($proj->id))
                            <option value="{{ $proj->id }}">{{ $proj->name }}</option>
                        @endif
                    @endforeach
                </select>
                <button type="submit" class="w-full sm:w-auto bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded text-sm sm:text-base">
                    Tambah Proyek
                </button>
            </div>
        </form>

        <div class="mt-4 space-y-2">
            @forelse($event->projects as $project)
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 p-3 bg-gray-50 rounded-lg">
                    <div class="flex items-start sm:items-center gap-2 flex-1 min-w-0">
                        <svg class="h-5 w-5 text-indigo-600 flex-shrink-0 mt-0.5 sm:mt-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <div class="flex-1 min-w-0">
                            <a href="{{ route('projects.show', $project) }}" class="font-medium text-sm sm:text-base text-gray-900 hover:text-indigo-600 block truncate">
                                {{ $project->name }}
                            </a>
                            <p class="text-xs text-gray-500">{{ Str::limit($project->description, 50) }}</p>
                        </div>
                    </div>
                    <form action="{{ route('events.detachProject', [$event, $project]) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full sm:w-auto text-red-600 hover:text-red-800 text-sm px-3 py-1.5 rounded border border-red-300 hover:bg-red-50" 
                                onclick="return confirm('Lepaskan proyek dari event ini?')">
                            Hapus
                        </button>
                    </form>
                </div>
            @empty
                <p class="text-gray-500 text-xs sm:text-sm italic">Belum ada proyek terkait dengan event ini.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
