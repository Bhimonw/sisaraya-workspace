@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    @include('partials.flash')
    <h1 class="text-xl font-semibold mb-4">Buat RAB</h1>
    <form action="{{ route('rabs.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="mb-2">
            <label class="block">Title</label>
            <input name="title" class="w-full border p-2" required>
        </div>
        <div class="mb-2">
            <label class="block">Project (opsional)</label>
            <select name="project_id" class="w-full border p-2">
                <option value="">-</option>
                @foreach($projects as $p)
                <option value="{{ $p->id }}">{{ $p->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-2">
            <label class="block">Amount</label>
            <div class="relative">
                <span class="absolute left-3 top-2 text-gray-600">Rp</span>
                <input type="text" id="amount-display" class="w-full border p-2 pl-10" placeholder="0" required>
                <input type="hidden" name="amount" id="amount-value">
            </div>
            <p class="text-xs text-gray-500 mt-1">Format: Rupiah Indonesia</p>
        </div>
        <div class="mb-2">
            <label class="block">Description</label>
            <textarea name="description" class="w-full border p-2"></textarea>
        </div>
        <div class="mb-2">
            <label class="block">File (pdf/jpg/png)</label>
            <input type="file" name="file">
        </div>
        <div>
            <button class="inline-block bg-green-600 text-white px-3 py-2 rounded">Create</button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const displayInput = document.getElementById('amount-display');
    const valueInput = document.getElementById('amount-value');
    
    // Format number to Indonesian Rupiah
    function formatRupiah(angka) {
        const numberString = angka.replace(/[^,\d]/g, '').toString();
        const split = numberString.split(',');
        const sisa = split[0].length % 3;
        let rupiah = split[0].substr(0, sisa);
        const ribuan = split[0].substr(sisa).match(/\d{3}/gi);
        
        if (ribuan) {
            const separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }
        
        rupiah = split[1] !== undefined ? rupiah + ',' + split[1] : rupiah;
        return rupiah;
    }
    
    // Parse formatted rupiah back to number
    function parseRupiah(rupiah) {
        return parseInt(rupiah.replace(/\./g, '').replace(/,/g, '')) || 0;
    }
    
    displayInput.addEventListener('input', function(e) {
        const formatted = formatRupiah(e.target.value);
        e.target.value = formatted;
        valueInput.value = parseRupiah(formatted);
    });
    
    displayInput.addEventListener('keypress', function(e) {
        // Only allow numbers
        if (e.which < 48 || e.which > 57) {
            e.preventDefault();
        }
    });
});
</script>

@endsection
