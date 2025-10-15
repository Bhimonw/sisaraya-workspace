@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    @include('partials.flash')
    <h1 class="text-xl font-semibold mb-4">Edit RAB</h1>
    <form action="{{ route('rabs.update', $rab) }}" method="POST" enctype="multipart/form-data">
        @csrf @method('PUT')
        <div class="mb-2">
            <label class="block">Title</label>
            <input name="title" value="{{ $rab->title }}" class="w-full border p-2" required>
        </div>
        <div class="mb-2">
            <label class="block">Project (opsional)</label>
            <select name="project_id" class="w-full border p-2">
                <option value="">-</option>
                @foreach($projects as $p)
                <option value="{{ $p->id }}" {{ $rab->project_id == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-2">
            <label class="block">Amount</label>
            <input type="number" name="amount" value="{{ $rab->amount }}" class="w-full border p-2" required>
        </div>
        <div class="mb-2">
            <label class="block">Description</label>
            <textarea name="description" class="w-full border p-2">{{ $rab->description }}</textarea>
        </div>
        <div class="mb-2">
            <label class="block">File (pdf/jpg/png)</label>
            <input type="file" name="file">
            @if($rab->file_path)
            <div class="text-sm">Current: <a href="{{ url('storage/'.$rab->file_path) }}" target="_blank">Download</a></div>
            @endif
        </div>
        <div>
            <button class="inline-block bg-yellow-500 text-white px-3 py-2 rounded">Update</button>
        </div>
    </form>
</div>
@endsection
