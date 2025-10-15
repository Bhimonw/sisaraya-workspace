@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold">Vote Tally for {{ $candidateId }}</h1>
    <div class="mt-4">Yes: {{ $yes }}</div>
    <div>No: {{ $no }}</div>
    <div>Total Votes: {{ $total }}</div>
    <div>Eligible Members: {{ $eligible }}</div>
    <div>Quorum: <span class="font-bold {{ $quorum ? 'text-green-600' : 'text-red-600' }}">{{ $quorum ? 'Reached' : 'Not Reached' }}</span></div>
    @if($result)
        <div class="mt-4 p-2 border rounded bg-gray-100">
            <div>Finalized At: {{ $result->finalized_at }}</div>
            <div>Finalized By: {{ $result->finalizedBy?->name ?? $result->finalized_by }}</div>
            <div>Result: <span class="font-bold {{ $result->accepted ? 'text-green-600' : 'text-red-600' }}">{{ $result->accepted ? 'Accepted' : 'Rejected' }}</span></div>
        </div>
    @else
        @if($quorum && auth()->user()->hasRole(['pm','bendahara']))
            <form method="POST" action="{{ route('votes.finalize', $candidateId) }}" class="mt-4">
                @csrf
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Finalize Voting</button>
            </form>
        @endif
    @endif
    @if(session('error'))
        <div class="mt-2 text-red-600">{{ session('error') }}</div>
    @endif
    @if(session('success'))
        <div class="mt-2 text-green-600">{{ session('success') }}</div>
    @endif
</div>
@endsection
