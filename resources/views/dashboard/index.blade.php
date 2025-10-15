@extends('layouts.app')

@section('content')
    <h1 class="text-2xl font-bold mb-4">Dashboard</h1>
    <div class="grid md:grid-cols-2 gap-4">
        @foreach($projects as $project)
            <div class="bg-white p-4 rounded shadow">
                <h2 class="font-semibold">{{ $project->name }}</h2>
                <p class="text-sm text-gray-600">{{ $project->description }}</p>
                <a href="{{ route('projects.show', $project) }}" class="text-blue-600 text-sm">Open</a>
            </div>
        @endforeach
    </div>
@endsection
