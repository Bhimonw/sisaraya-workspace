@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold">Usaha Komunitas</h1>
        @can('business.create')
            <a href="{{ route('businesses.create') }}" class="bg-blue-600 text-white px-3 py-2 rounded">Buat Usaha Baru</a>
        @endcan
    </div>

    <div class="mt-4 space-y-3">
        @foreach($businesses as $b)
            <div class="bg-white p-4 rounded shadow">
                <div class="flex justify-between items-center">
                    <div>
                        <div class="font-semibold"><a href="{{ route('businesses.show', $b) }}">{{ $b->name }}</a></div>
                        <div class="text-xs text-gray-500">{{ Str::limit($b->description, 120) }}</div>
                    </div>
                    <div class="text-sm text-gray-600">{{ ucfirst($b->status) }}</div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-4">{{ $businesses->links() }}</div>
</div>
@endsection
