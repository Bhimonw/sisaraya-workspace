<!-- Hero Section -->
<section class="relative min-h-screen flex items-center justify-center overflow-hidden pt-20">
    <div class="absolute inset-0">
        <img src="{{ asset('Asset.jpg') }}" alt="SISARAYA Background" class="w-full h-full object-cover">
        <div class="absolute inset-0 gradient-hero pattern-dots"></div>
    </div>
    
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-32 text-center">
        <div class="animate-float">
            <h1 class="text-5xl sm:text-6xl lg:text-8xl font-display font-black text-white mb-6 text-shadow-strong drop-shadow-2xl leading-tight">
                Komunitas Kreatif<br>
                <span class="bg-gradient-to-r from-violet-300 via-blue-300 to-emerald-300 bg-clip-text text-transparent">Kolaboratif & Inovatif</span>
            </h1>
        </div>
        
        <p class="text-xl sm:text-2xl lg:text-3xl text-white mb-6 font-bold text-shadow-strong drop-shadow-lg">
            Tempat para kreator, musisi, media, dan wirausahawan<br class="hidden sm:block"> berkumpul, berkolaborasi, dan menciptakan karya berdampak
        </p>
        
        <p class="text-base sm:text-lg text-white/90 max-w-3xl mx-auto mb-12 font-medium text-shadow-strong drop-shadow-lg">
            Satu semangat, banyak ekspresi — dari Teman Event, musik & band,<br class="hidden sm:block"> hingga kewirausahaan dan media kreatif
        </p>
        
        <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
            <a href="#about" class="inline-flex items-center px-8 py-4 bg-white text-violet-600 text-base font-bold rounded-full hover:bg-gray-50 hover:scale-105 transition-all duration-300 shadow-2xl">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Kenali Kami
            </a>
            @auth
            <a href="{{ url('/dashboard') }}" class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-violet-600 to-blue-600 text-white text-base font-bold rounded-full hover:shadow-2xl hover:scale-105 transition-all duration-300">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Buka Dashboard
            </a>
            @else
            <a href="{{ route('login') }}" class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-violet-600 to-blue-600 text-white text-base font-bold rounded-full hover:shadow-2xl hover:scale-105 transition-all duration-300">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                </svg>
                Login Sekarang
            </a>
            @endauth
        </div>
    </div>
    
    <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 animate-bounce">
        <a href="#about" class="text-white/80 hover:text-white transition-colors duration-200">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
            </svg>
        </a>
    </div>
</section>
