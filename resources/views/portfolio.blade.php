<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Portofolio - SISARAYA</title>
    <meta name="description" content="Portofolio karya dan event yang telah diselenggarakan oleh SISARAYA.">
    
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

    <!-- Hero Section -->
    <section class="pt-32 pb-16 bg-gradient-to-br from-violet-600 via-blue-600 to-emerald-600 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-display font-black mb-6">
                Portofolio Kami
            </h1>
            <p class="text-xl sm:text-2xl text-white/90 max-w-3xl mx-auto">
                Karya dan event yang telah kami selenggarakan
            </p>
        </div>
    </section>

    <!-- Portfolio Content -->
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Filter Tabs -->
            <div class="flex flex-wrap justify-center gap-4 mb-12">
                <button class="px-6 py-3 bg-violet-600 text-white rounded-full font-semibold shadow-lg">Semua</button>
                <button class="px-6 py-3 bg-gray-200 text-gray-700 hover:bg-gray-300 rounded-full font-semibold transition-colors">Event</button>
                <button class="px-6 py-3 bg-gray-200 text-gray-700 hover:bg-gray-300 rounded-full font-semibold transition-colors">Musik</button>
                <button class="px-6 py-3 bg-gray-200 text-gray-700 hover:bg-gray-300 rounded-full font-semibold transition-colors">Media</button>
                <button class="px-6 py-3 bg-gray-200 text-gray-700 hover:bg-gray-300 rounded-full font-semibold transition-colors">Bisnis</button>
            </div>

            <!-- Portfolio Grid -->
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                
                <!-- Portfolio Item 1 -->
                <div class="group bg-white rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2">
                    <div class="relative h-64 bg-gradient-to-br from-violet-500 to-blue-500 overflow-hidden">
                        <div class="absolute inset-0 flex items-center justify-center">
                            <svg class="w-24 h-24 text-white/30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div class="absolute top-4 right-4">
                            <span class="px-4 py-2 bg-white/20 backdrop-blur-sm text-white text-sm font-semibold rounded-full border border-white/30">Event</span>
                        </div>
                    </div>
                    <div class="p-6">
                        <h3 class="text-2xl font-bold text-gray-900 mb-2 group-hover:text-violet-600 transition-colors">Sample Event 2024</h3>
                        <p class="text-gray-600 mb-4">Event kolaboratif pertama SISARAYA yang menampilkan berbagai talenta dari berbagai bidang kreatif.</p>
                        
                        <div class="flex items-center text-sm text-gray-500 mb-4">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            Desember 2024
                        </div>
                        
                        <div class="flex flex-wrap gap-2 mb-4">
                            <span class="px-3 py-1 bg-violet-100 text-violet-600 text-xs font-semibold rounded-full">Musik</span>
                            <span class="px-3 py-1 bg-blue-100 text-blue-600 text-xs font-semibold rounded-full">Performance</span>
                            <span class="px-3 py-1 bg-emerald-100 text-emerald-600 text-xs font-semibold rounded-full">Kolaborasi</span>
                        </div>
                        
                        <button class="w-full px-6 py-3 bg-gradient-to-r from-violet-600 to-blue-600 text-white font-semibold rounded-lg hover:shadow-lg transition-all duration-300">
                            Lihat Detail
                        </button>
                    </div>
                </div>

                <!-- Placeholder for future items -->
                <div class="bg-gray-100 rounded-2xl overflow-hidden shadow-lg border-2 border-dashed border-gray-300 flex items-center justify-center h-full min-h-[400px]">
                    <div class="text-center p-8">
                        <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        <p class="text-gray-500 font-medium">Portofolio Selanjutnya</p>
                        <p class="text-sm text-gray-400 mt-2">Segera hadir</p>
                    </div>
                </div>

                <div class="bg-gray-100 rounded-2xl overflow-hidden shadow-lg border-2 border-dashed border-gray-300 flex items-center justify-center h-full min-h-[400px]">
                    <div class="text-center p-8">
                        <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        <p class="text-gray-500 font-medium">Portofolio Selanjutnya</p>
                        <p class="text-sm text-gray-400 mt-2">Segera hadir</p>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-16 bg-gradient-to-br from-violet-600 via-blue-600 to-emerald-600 text-white">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl sm:text-4xl font-bold mb-6">Ingin Berkolaborasi?</h2>
            <p class="text-xl text-white/90 mb-8">Mari ciptakan karya berdampak bersama SISARAYA</p>
            <a href="{{ url('/') }}#contact" class="inline-flex items-center px-8 py-4 bg-white text-violet-600 text-lg font-bold rounded-full hover:bg-gray-50 hover:scale-105 transition-all duration-300 shadow-2xl">
                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                </svg>
                Hubungi Kami
            </a>
        </div>
    </section>

    @include('landing.contact')

</body>
</html>
