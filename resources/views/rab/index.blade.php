@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    @include('partials.flash')
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-2xl font-semibold">RAB</h1>
        <a href="{{ route('rabs.create') }}" class="inline-block bg-blue-600 text-white px-4 py-2 rounded">Buat RAB</a>
    </div>

    <div class="grid grid-cols-1 gap-4">
        @foreach($rabs as $rab)
        <div class="p-4 border rounded shadow-sm">
            <div class="flex justify-between items-start">
                <div>
                    <h3 class="font-bold">{{ $rab->title }}</h3>
                    <div class="text-sm text-gray-600">Project: {{ optional($rab->project)->name ?? '-' }}</div>
                    <div class="text-sm font-medium text-green-700">Rp {{ number_format($rab->amount, 0, ',', '.') }}</div>
                </div>
                <div class="space-x-2 text-right">
                    <a href="{{ route('rabs.show', $rab) }}" class="text-blue-600">View</a>
                    <a href="{{ route('rabs.edit', $rab) }}" class="text-yellow-600">Edit</a>
                    <form action="{{ route('rabs.destroy', $rab) }}" method="POST" style="display:inline">@csrf @method('DELETE')<button class="text-red-600">Delete</button></form>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="mt-4">{{ $rabs->links() }}</div>
</div>
@endsection
