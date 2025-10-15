@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-xl font-semibold mb-4">Buat Ticket Permintaan Dana</h1>
    <form action="{{ route('projects.tickets.store', $project) }}" method="POST">
        @csrf
        <input type="hidden" name="rab_id" value="{{ $rab->id }}">
        <input type="hidden" name="type" value="permintaan_dana">
        <div class="mb-2">
            <label>Title</label>
            <input name="title" value="Permintaan Dana: {{ $rab->title }}" class="w-full border p-2" required>
        </div>
        <div class="mb-2">
            <label>Description</label>
            <textarea name="description" class="w-full border p-2">Permintaan dana untuk RAB: {{ $rab->title }} (Rp {{ number_format($rab->amount) }})</textarea>
        </div>
        <div class="mb-2">
            <label>Status</label>
            <select name="status" class="w-full border p-2">
                <option value="todo">To Do</option>
                <option value="doing">Doing</option>
                <option value="done">Done</option>
            </select>
        </div>
        <div>
            <button class="inline-block bg-blue-600 text-white px-3 py-2 rounded">Create Ticket</button>
        </div>
    </form>
</div>
@endsection
