<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SISARAYA - Komunitas Kreatif, Kolaboratif, dan Inovatif</title>
    <meta name="description" content="Tempat para kreator, musisi, dan pelaku media berkumpul untuk berkolaborasi dan menciptakan karya yang berdampak.">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet" />
    
    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        .font-display {
            font-family: 'Figtree', sans-serif;
        }
        
        .gradient-hero {
            background: linear-gradient(135deg, rgba(124, 58, 237, 0.9) 0%, rgba(37, 99, 235, 0.8) 50%, rgba(16, 185, 129, 0.7) 100%);
        }
        
        .pattern-dots {
            background-image: radial-gradient(circle, rgba(255, 255, 255, 0.1) 1px, transparent 1px);
            background-size: 20px 20px;
        }
        
        .text-shadow-strong {
            text-shadow: 2px 4px 12px rgba(0, 0, 0, 0.5), 0 2px 8px rgba(0, 0, 0, 0.3);
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        
        .animate-float {
            animation: float 6s ease-in-out infinite;
        }
    </style>
</head>
<body class="antialiased font-display" x-data="{ mobileMenuOpen: false }">
    
    <!-- Navigation -->
    <nav class="fixed w-full bg-white/95 backdrop-blur-sm shadow-md z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <div class="flex items-center">
                    <img src="{{ asset('logo-no-bg.png') }}" alt="SISARAYA Logo" class="h-10 w-auto">
                    <span class="ml-3 text-xl font-bold bg-gradient-to-r from-violet-600 via-blue-600 to-emerald-500 bg-clip-text text-transparent">SISARAYA</span>
                </div>
                
                <!-- Desktop Menu -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="#about" class="text-gray-700 hover:text-violet-600 transition-colors font-medium">Tentang</a>
                    <a href="#values" class="text-gray-700 hover:text-violet-600 transition-colors font-medium">Nilai Kami</a>
                    <a href="#portfolio" class="text-gray-700 hover:text-violet-600 transition-colors font-medium">Pilar</a>
                    @if(!app()->environment('production') && Route::has('portfolio'))
                    <a href="{{ route('portfolio') }}" class="text-gray-700 hover:text-violet-600 transition-colors font-medium">Portofolio</a>
                    @endif
                    <a href="#collaboration" class="text-gray-700 hover:text-violet-600 transition-colors font-medium">Kolaborasi</a>
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
                <a href="#about" @click="mobileMenuOpen = false" class="block py-2 text-gray-700 hover:text-violet-600 font-medium">Tentang</a>
                <a href="#values" @click="mobileMenuOpen = false" class="block py-2 text-gray-700 hover:text-violet-600 font-medium">Nilai Kami</a>
                <a href="#portfolio" @click="mobileMenuOpen = false" class="block py-2 text-gray-700 hover:text-violet-600 font-medium">Pilar</a>
                @if(!app()->environment('production') && Route::has('portfolio'))
                <a href="{{ route('portfolio') }}" @click="mobileMenuOpen = false" class="block py-2 text-gray-700 hover:text-violet-600 font-medium">Portofolio</a>
                @endif
                <a href="#collaboration" @click="mobileMenuOpen = false" class="block py-2 text-gray-700 hover:text-violet-600 font-medium">Kolaborasi</a>
            </div>
        </div>
    </nav>

    @include('landing.hero')
    @include('landing.about')
    @include('landing.values')
    @include('landing.portfolio')
    @include('landing.collaboration')
    @include('landing.contact')

</body>
</html>
