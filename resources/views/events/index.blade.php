@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold">Events</h1>
        <a href="{{ route('events.create') }}" class="bg-blue-600 text-white px-3 py-2 rounded">Create Event</a>
    </div>

    <div class="mt-4 space-y-3">
        @foreach($events as $e)
            <div class="bg-white p-4 rounded shadow">
                <div class="flex justify-between">
                    <div>
                        <div class="font-semibold"><a href="{{ route('events.show', $e) }}">{{ $e->title }}</a></div>
                        <div class="text-xs text-gray-500">{{ optional($e->start_date)->format('Y-m-d') }} – {{ optional($e->end_date)->format('Y-m-d') }}</div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    <div class="mt-4">{{ $events->links() }}</div>
</div>
@endsection
