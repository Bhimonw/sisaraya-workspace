@props([
    'name' => 'amount',
    'value' => 0,
    'label' => 'Jumlah',
    'placeholder' => '0',
    'required' => false,
    'helpText' => 'Format: Rupiah Indonesia'
])

@php
    $displayId = $name . '-display';
    $valueId = $name . '-value';
    $formattedValue = $value ? number_format($value, 0, ',', '.') : '';
@endphp

<div {{ $attributes->merge(['class' => 'mb-4']) }}>
    <label for="{{ $displayId }}" class="block text-sm font-medium text-gray-700 mb-2">
        {{ $label }}
        @if($required)
            <span class="text-red-500">*</span>
        @endif
    </label>
    <div class="relative">
        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-600 font-medium">Rp</span>
        <input 
            type="text" 
            id="{{ $displayId }}" 
            value="{{ $formattedValue }}"
            placeholder="{{ $placeholder }}"
            {{ $required ? 'required' : '' }}
            class="w-full border border-gray-300 rounded-xl py-3 pl-12 pr-4 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-all"
        >
        <input 
            type="hidden" 
            name="{{ $name }}" 
            id="{{ $valueId }}"
            value="{{ $value }}"
        >
    </div>
    @if($helpText)
        <p class="text-xs text-gray-500 mt-2">{{ $helpText }}</p>
    @endif
    @error($name)
        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
    @enderror
</div>

@once
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
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
    
    // Initialize all currency inputs
    document.querySelectorAll('[id$="-display"]').forEach(function(displayInput) {
        const inputId = displayInput.id;
        if (!inputId.endsWith('-display')) return;
        
        const baseName = inputId.replace('-display', '');
        const valueInput = document.getElementById(baseName + '-value');
        
        if (!valueInput) return;
        
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
});
</script>
@endpush
@endonce
