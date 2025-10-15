@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold">{{ $business->name }}</h1>
    <div class="text-sm text-gray-600">Status: {{ ucfirst($business->status) }}</div>
    <div class="mt-4">{{ $business->description }}</div>
    <div class="mt-4"><a href="{{ route('businesses.index') }}" class="text-gray-600">← Back to businesses</a></div>
</div>
@endsection
