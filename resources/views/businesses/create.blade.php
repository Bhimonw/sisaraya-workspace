@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-xl font-semibold mb-4">Buat Usaha Baru</h1>

    <form action="{{ route('businesses.store') }}" method="POST">
        @csrf
        <div class="mb-2">
            <label>Nama Usaha</label>
            <input name="name" class="w-full border p-2" required>
        </div>
        <div class="mb-2">
            <label>Deskripsi</label>
            <textarea name="description" class="w-full border p-2"></textarea>
        </div>
        <div>
            <button class="inline-block bg-blue-600 text-white px-3 py-2 rounded">Buat</button>
        </div>
    </form>
</div>
@endsection
