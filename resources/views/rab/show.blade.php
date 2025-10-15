@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    @include('partials.flash')
    <h1 class="text-2xl font-semibold">{{ $rab->title }}</h1>
    <div class="text-sm text-gray-600">Project: {{ optional($rab->project)->name ?? '-' }}</div>
    <div class="mt-2 text-xl font-bold text-green-700">Rp {{ number_format($rab->amount, 0, ',', '.') }}</div>
    <div class="mt-2">{{ $rab->description }}</div>
    <div class="mt-2 text-sm">Status: <span class="font-semibold">{{ ucfirst($rab->funds_status ?? 'draft') }}</span></div>
    @if($rab->approver)
        <div class="text-xs text-gray-500">Approved by: {{ $rab->approver->name }} at {{ $rab->approved_at?->format('Y-m-d H:i') }}</div>
    @endif
    @if($rab->file_path)
    <div class="mt-4"><a href="{{ url('storage/'.$rab->file_path) }}" target="_blank" class="text-blue-600">Download attachment</a></div>
    @endif
    <div class="mt-4">
        <a href="{{ route('rabs.index') }}" class="inline-block text-gray-600">← Back to RAB list</a>
        @can('tickets.create')
            <a href="{{ route('tickets.createFromRab', $rab) }}" class="inline-block ml-4 bg-indigo-600 text-white px-3 py-2 rounded">Buat Ticket Permintaan Dana</a>
        @endcan

        @can('finance.manage_rab')
            @if($rab->funds_status !== 'approved')
                <form action="{{ route('rabs.approve', $rab) }}" method="POST" class="inline-block ml-2">
                    @csrf
                    <button class="bg-green-600 text-white px-3 py-2 rounded">Approve</button>
                </form>
            @endif

            @if($rab->funds_status !== 'rejected')
                <form action="{{ route('rabs.reject', $rab) }}" method="POST" class="inline-block ml-2">
                    @csrf
                    <button class="bg-red-600 text-white px-3 py-2 rounded">Reject</button>
                </form>
            @endif
        @endcan
    </div>
</div>
@endsection
