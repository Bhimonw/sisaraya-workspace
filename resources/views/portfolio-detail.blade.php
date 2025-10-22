<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Detail Portofolio' }} - SISARAYA</title>
    <meta name="description" content="{{ $description ?? 'Detail portofolio SISARAYA' }}">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet" />
    
    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        .font-display {
            font-family: 'Figtree', sans-serif;
        }
    </style>
</head>
<body class="antialiased font-display bg-gray-50" x-data="{ mobileMenuOpen: false }">
    
    <!-- Navigation -->
    <nav class="fixed w-full bg-white/95 backdrop-blur-sm shadow-md z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <a href="{{ url('/') }}" class="flex items-center">
                    <img src="{{ asset('logo-no-bg.png') }}" alt="SISARAYA Logo" class="h-10 w-auto">
                    <span class="ml-3 text-xl font-bold bg-gradient-to-r from-violet-600 via-blue-600 to-emerald-500 bg-clip-text text-transparent">SISARAYA</span>
                </a>
                
                <!-- Desktop Menu -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="{{ url('/') }}" class="text-gray-700 hover:text-violet-600 transition-colors font-medium">Beranda</a>
                    <a href="{{ url('/')}}#about" class="text-gray-700 hover:text-violet-600 transition-colors font-medium">Tentang</a>
                    <a href="{{ route('portfolio') }}" class="text-violet-600 font-semibold">Portofolio</a>
                    <a href="{{ url('/')}}#collaboration" class="text-gray-700 hover:text-violet-600 transition-colors font-medium">Kolaborasi</a>
                </div>
                
                <!-- Mobile Menu Button -->
                <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden p-2 rounded-lg hover:bg-gray-100 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path x-show="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        <path x-show="mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
        
        <!-- Mobile Menu -->
        <div x-show="mobileMenuOpen" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-1"
             x-transition:enter-end="opacity-100 translate-y-0"
             class="md:hidden border-t border-gray-200 bg-white">
            <div class="px-4 py-3 space-y-3">
                <a href="{{ url('/') }}" @click="mobileMenuOpen = false" class="block py-2 text-gray-700 hover:text-violet-600 font-medium">Beranda</a>
                <a href="{{ url('/')}}#about" @click="mobileMenuOpen = false" class="block py-2 text-gray-700 hover:text-violet-600 font-medium">Tentang</a>
                <a href="{{ route('portfolio') }}" @click="mobileMenuOpen = false" class="block py-2 text-violet-600 font-semibold">Portofolio</a>
                <a href="{{ url('/')}}#collaboration" @click="mobileMenuOpen = false" class="block py-2 text-gray-700 hover:text-violet-600 font-medium">Kolaborasi</a>
            </div>
        </div>
    </nav>

    <!-- Breadcrumb -->
    <section class="pt-24 pb-6 bg-white border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav class="flex items-center space-x-2 text-sm">
                <a href="{{ url('/') }}" class="text-gray-500 hover:text-violet-600 transition-colors">Beranda</a>
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
                <a href="{{ route('portfolio') }}" class="text-gray-500 hover:text-violet-600 transition-colors">Portofolio</a>
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
                <span class="text-violet-600 font-semibold">{{ $title ?? 'Detail' }}</span>
            </nav>
        </div>
    </section>

    <!-- Hero Image -->
    <section class="relative h-96 bg-gradient-to-br from-violet-500 to-blue-500 overflow-hidden">
        @if(isset($headerImage) && $headerImage)
            <img src="{{ $headerImage }}" alt="{{ $title }}" class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
        @else
            <div class="absolute inset-0 flex items-center justify-center">
                <svg class="w-32 h-32 text-white/30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
        @endif
        
        <!-- Category Badge -->
        <div class="absolute top-8 left-8">
            <span class="px-6 py-3 bg-white/20 backdrop-blur-md text-white text-lg font-bold rounded-full border-2 border-white/30 shadow-xl">
                {{ $category ?? 'Event' }}
            </span>
        </div>

        <!-- Back Button -->
        <div class="absolute top-8 right-8">
            <a href="{{ route('portfolio') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-white/90 hover:bg-white text-gray-900 font-semibold rounded-full shadow-xl hover:shadow-2xl transition-all duration-200 group">
                <svg class="w-5 h-5 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                <span>Kembali</span>
            </a>
        </div>
    </section>

    <!-- Content -->
    <section class="py-12 bg-white">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Title & Date -->
            <div class="mb-8">
                <h1 class="text-4xl sm:text-5xl font-bold text-gray-900 mb-4 leading-tight">{{ $title ?? 'Judul Portofolio' }}</h1>
                <div class="flex items-center text-lg text-gray-600 bg-gray-50 px-6 py-3 rounded-xl w-fit">
                    <svg class="w-6 h-6 mr-3 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span class="font-semibold">{{ $date ?? 'Tanggal' }}</span>
                </div>
            </div>

            <!-- Tags -->
            @if(isset($tags) && count($tags) > 0)
            <div class="flex flex-wrap gap-3 mb-8">
                @foreach($tags as $index => $tag)
                <span class="px-4 py-2 {{ $index % 4 == 0 ? 'bg-violet-100 text-violet-700' : ($index % 4 == 1 ? 'bg-blue-100 text-blue-700' : ($index % 4 == 2 ? 'bg-emerald-100 text-emerald-700' : 'bg-purple-100 text-purple-700')) }} text-sm font-semibold rounded-full">
                    {{ $tag }}
                </span>
                @endforeach
            </div>
            @endif

            <!-- Description -->
            <div class="mb-12 bg-gradient-to-br from-violet-50 to-blue-50 p-8 rounded-2xl border-2 border-violet-100">
                <div class="flex items-start gap-4">
                    <svg class="w-8 h-8 text-violet-600 flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 mb-3">Deskripsi</h2>
                        <p class="text-lg text-gray-700 leading-relaxed">{{ $description ?? 'Deskripsi portofolio' }}</p>
                    </div>
                </div>
            </div>

            <!-- Details -->
            @if(isset($details) && $details)
            <div class="mb-12 bg-white border-2 border-gray-200 p-8 rounded-2xl">
                <div class="flex items-start gap-4">
                    <svg class="w-8 h-8 text-blue-600 flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <div class="flex-1">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4">Detail Event</h2>
                        <div class="prose prose-lg max-w-none text-gray-700 leading-relaxed whitespace-pre-line">{{ $details }}</div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Image Gallery -->
            @if(isset($images) && count($images) > 0)
            <div class="mb-12">
                <div class="flex items-center gap-4 mb-6">
                    <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <h2 class="text-2xl font-bold text-gray-900">Galeri</h2>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($images as $image)
                    <div class="aspect-square rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition-shadow cursor-pointer group">
                        <img src="{{ $image }}" alt="Gallery image" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- CTA Section -->
            <div class="bg-gradient-to-br from-violet-600 via-blue-600 to-emerald-600 rounded-2xl p-8 sm:p-12 text-white text-center">
                <h3 class="text-2xl sm:text-3xl font-bold mb-4">Tertarik Berkolaborasi?</h3>
                <p class="text-lg text-white/90 mb-8 max-w-2xl mx-auto">Mari ciptakan karya berdampak bersama SISARAYA seperti event ini</p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('portfolio') }}" class="inline-flex items-center justify-center gap-2 px-8 py-4 bg-white/20 backdrop-blur-sm text-white font-semibold rounded-full hover:bg-white/30 transition-all duration-200 border-2 border-white/30 group">
                        <svg class="w-5 h-5 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        <span>Lihat Portofolio Lain</span>
                    </a>
                    <a href="{{ url('/') }}#contact" class="inline-flex items-center justify-center gap-2 px-8 py-4 bg-white text-violet-600 font-semibold rounded-full hover:bg-gray-50 hover:scale-105 transition-all duration-200 shadow-xl group">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                        <span>Hubungi Kami</span>
                        <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>
            </div>

        </div>
    </section>

    @include('landing.contact')

</body>
</html>
