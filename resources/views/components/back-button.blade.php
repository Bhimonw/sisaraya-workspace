@props(['url' => null, 'text' => 'Kembali'])

@php
    $backUrl = $url ?? url()->previous();
@endphp

<a href="{{ $backUrl }}" 
   class="inline-flex items-center gap-2 px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 shadow-sm hover:shadow group">
    <svg class="h-5 w-5 text-gray-500 group-hover:text-gray-700 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
    </svg>
    <span class="font-medium">{{ $text }}</span>
</a>
