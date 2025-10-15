@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-xl font-semibold mb-4">Create Event</h1>

    <form action="{{ route('events.store') }}" method="POST">
        @csrf
        <div class="mb-2">
            <label>Title</label>
            <input name="title" class="w-full border p-2" required>
        </div>
        <div class="mb-2">
            <label>Start Date</label>
            <input type="date" name="start_date" class="w-full border p-2">
        </div>
        <div class="mb-2">
            <label>End Date</label>
            <input type="date" name="end_date" class="w-full border p-2">
        </div>
        <div class="mb-2">
            <label>Description</label>
            <textarea name="description" class="w-full border p-2"></textarea>
        </div>
        <div>
            <button class="inline-block bg-blue-600 text-white px-3 py-2 rounded">Create</button>
        </div>
    </form>
</div>
@endsection
