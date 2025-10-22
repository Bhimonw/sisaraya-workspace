<!-- Footer / Kontak Section -->
<footer id="contact" class="py-12 bg-gradient-to-br from-gray-900 to-gray-800 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row items-center justify-between gap-6">
            <!-- Logo & Tagline -->
            <div class="flex items-center space-x-3">
                <img src="{{ asset('logo-no-bg.png') }}" alt="SISARAYA Logo" class="h-12 w-auto">
                <div>
                    <h3 class="text-xl font-bold bg-gradient-to-r from-violet-400 via-blue-400 to-emerald-400 bg-clip-text text-transparent">SISARAYA</h3>
                    <p class="text-sm text-gray-400">Komunitas Kreatif Kolaboratif</p>
                </div>
            </div>
            
            <!-- Contact Info -->
            <div class="flex items-center space-x-3 bg-white/10 backdrop-blur-sm rounded-lg px-6 py-3 border border-white/20">
                <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                </svg>
                <div>
                    <p class="text-xs text-gray-400">Hubungi Kami</p>
                    <a href="tel:+6281356019609" class="text-base font-semibold hover:text-emerald-400 transition-colors">+62 813-5601-9609</a>
                </div>
            </div>
        </div>
        
        <!-- Copyright -->
        <div class="mt-8 pt-6 border-t border-gray-700 text-center">
            <p class="text-sm text-gray-400">&copy; {{ date('Y') }} SISARAYA. Semua hak dilindungi.</p>
        </div>
    </div>
</footer>
