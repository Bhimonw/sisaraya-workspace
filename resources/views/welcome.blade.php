<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>SISARAYA - Kolektif Kreatif Lintas Bidang</title>
<link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=inter:400,500,600,700|playfair-display:700" rel="stylesheet" />
@vite(['resources/css/app.css', 'resources/js/app.js'])
<style>
.font-display { font-family: 'Playfair Display', serif; }
.font-body { font-family: 'Inter', sans-serif; }
.gradient-overlay { background: linear-gradient(135deg, rgba(0,0,0,0.80) 0%, rgba(0,0,0,0.75) 20%, rgba(139,92,246,0.75) 45%, rgba(59,130,246,0.75) 70%, rgba(16,185,129,0.70) 100%); }
.pattern-dots { background-image: radial-gradient(rgba(255,255,255,0.2) 1px, transparent 1px); background-size: 20px 20px; }
.text-shadow-strong { text-shadow: 0 2px 8px rgba(0,0,0,0.5), 0 4px 16px rgba(0,0,0,0.4), 0 8px 24px rgba(0,0,0,0.3); }
</style>
</head>
<body class="font-body antialiased">
<nav class="fixed top-0 left-0 right-0 z-50 bg-white/90 backdrop-blur-md shadow-sm">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
<div class="flex items-center justify-between h-20">
<div class="flex items-center space-x-3">
<img src="{{ asset('logo-no-bg.png') }}" alt="SISARAYA Logo" class="h-12 w-auto">
<span class="text-2xl font-bold bg-gradient-to-r from-violet-600 via-blue-600 to-emerald-500 bg-clip-text text-transparent">SISARAYA</span>
</div>
@if (Route::has('login'))
<div class="flex items-center space-x-4">
@auth
<a href="{{ url('/dashboard') }}" class="inline-flex items-center px-6 py-2.5 bg-gradient-to-r from-violet-600 to-blue-600 text-white text-sm font-semibold rounded-full hover:shadow-lg transition-all duration-300">Dashboard</a>
@else
<a href="{{ route('login') }}" class="inline-flex items-center px-6 py-2.5 bg-gradient-to-r from-violet-600 to-blue-600 text-white text-sm font-semibold rounded-full hover:shadow-lg transition-all duration-300">Log in</a>
@endauth
</div>
@endif
</div>
</div>
</nav>
<section class="relative min-h-screen flex items-center justify-center overflow-hidden pt-20">
<div class="absolute inset-0">
<img src="{{ asset('Asset.jpg') }}" alt="SISARAYA Background" class="w-full h-full object-cover">
<div class="absolute inset-0 gradient-overlay pattern-dots"></div>
</div>
<div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-32 text-center">
<h1 class="text-5xl sm:text-6xl lg:text-7xl font-display font-bold text-white mb-6 text-shadow-strong drop-shadow-2xl">SISARAYA</h1>
<p class="text-xl sm:text-2xl text-white mb-4 font-semibold text-shadow-strong drop-shadow-lg">Satu semangat, banyak ekspresi.</p>
<p class="text-lg text-white/95 max-w-3xl mx-auto mb-12 font-medium text-shadow-strong drop-shadow-lg">Kami adalah komunitas yang bekerja di berbagai bidang — dari Event Organizer, musik & band, hingga kewirausahaan dan media kreatif.</p>
<div class="flex flex-col sm:flex-row items-center justify-center gap-4">
@auth
<a href="{{ url('/dashboard') }}" class="inline-flex items-center px-8 py-4 bg-white text-violet-600 text-base font-bold rounded-full hover:bg-gray-50 transition-all duration-300 shadow-xl">Buka Dashboard</a>
@else
<a href="{{ route('login') }}" class="inline-flex items-center px-8 py-4 bg-white text-violet-600 text-base font-bold rounded-full hover:bg-gray-50 transition-all duration-300 shadow-xl">Masuk ke Platform</a>
@endauth
</div>
</div>
</section>
<section id="about" class="py-24 bg-white">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
<div class="text-center mb-16">
<h2 class="text-4xl sm:text-5xl font-display font-bold text-gray-900 mb-6">Tentang Kami</h2>
<div class="w-24 h-1 bg-gradient-to-r from-violet-600 via-blue-600 to-emerald-500 mx-auto rounded-full"></div>
</div>
<div class="grid md:grid-cols-2 gap-12 items-center">
<div>
<p class="text-lg text-gray-700 leading-relaxed mb-6">
<span class="font-bold text-violet-600">Sisaraya</span> adalah ruang kolaborasi bagi individu dan tim yang ingin menciptakan sesuatu yang bermakna. Kami mempertemukan kreativitas, bisnis, dan media dalam satu wadah — menghadirkan proyek, acara, dan karya yang melampaui batas kategori.
</p>
<p class="text-lg text-gray-700 leading-relaxed">
Kami percaya bahwa <span class="font-semibold text-blue-600">kolaborasi adalah bahan bakar utama</span> untuk tumbuh bersama. Lewat berbagai inisiatif kami, Sisaraya menjadi tempat di mana ide-ide segar bertemu dengan energi kolektif untuk mewujudkan hal-hal nyata.
</p>
</div>
<div class="relative">
<div class="aspect-square rounded-2xl overflow-hidden shadow-2xl">
<img src="{{ asset('Asset.jpg') }}" alt="Team Sisaraya" class="w-full h-full object-cover">
</div>
<div class="absolute -bottom-6 -right-6 w-64 h-64 bg-gradient-to-br from-violet-600/20 to-emerald-600/20 rounded-2xl -z-10 blur-3xl"></div>
</div>
</div>
</div>
</section>
<section class="py-24 bg-gray-50">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
<div class="text-center mb-16">
<h2 class="text-4xl sm:text-5xl font-display font-bold text-gray-900 mb-6">Bidang Kami</h2>
<p class="text-lg text-gray-600 max-w-2xl mx-auto">Empat pilar utama yang menjadi fondasi kreativitas dan inovasi kolektif kami</p>
</div>
<div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-8">
<div class="group relative bg-white rounded-2xl p-8 shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2">
<div class="absolute inset-0 bg-gradient-to-br from-violet-600 to-blue-600 rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
<div class="relative z-10">
<div class="w-16 h-16 bg-gradient-to-br from-violet-600 to-blue-600 rounded-xl flex items-center justify-center mb-6 group-hover:bg-white transition-colors duration-300">
<svg class="w-8 h-8 text-white group-hover:text-violet-600 transition-colors duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
</svg>
</div>
<h3 class="text-xl font-bold text-gray-900 group-hover:text-white mb-3 transition-colors duration-300">Event Organizer</h3>
<p class="text-gray-600 group-hover:text-white/90 transition-colors duration-300">Merancang dan menjalankan acara yang berkesan dan memorable.</p>
</div>
</div>
<div class="group relative bg-white rounded-2xl p-8 shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2">
<div class="absolute inset-0 bg-gradient-to-br from-blue-600 to-cyan-600 rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
<div class="relative z-10">
<div class="w-16 h-16 bg-gradient-to-br from-blue-600 to-cyan-600 rounded-xl flex items-center justify-center mb-6 group-hover:bg-white transition-colors duration-300">
<svg class="w-8 h-8 text-white group-hover:text-blue-600 transition-colors duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"/>
</svg>
</div>
<h3 class="text-xl font-bold text-gray-900 group-hover:text-white mb-3 transition-colors duration-300">Musik & Band</h3>
<p class="text-gray-600 group-hover:text-white/90 transition-colors duration-300">Menyalurkan energi dan ekspresi lewat performa dan produksi musik.</p>
</div>
</div>
<div class="group relative bg-white rounded-2xl p-8 shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2">
<div class="absolute inset-0 bg-gradient-to-br from-emerald-600 to-teal-600 rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
<div class="relative z-10">
<div class="w-16 h-16 bg-gradient-to-br from-emerald-600 to-teal-600 rounded-xl flex items-center justify-center mb-6 group-hover:bg-white transition-colors duration-300">
<svg class="w-8 h-8 text-white group-hover:text-emerald-600 transition-colors duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
</svg>
</div>
<h3 class="text-xl font-bold text-gray-900 group-hover:text-white mb-3 transition-colors duration-300">Kewirausahaan</h3>
<p class="text-gray-600 group-hover:text-white/90 transition-colors duration-300">Mengembangkan ide menjadi peluang bisnis yang berdampak.</p>
</div>
</div>
<div class="group relative bg-white rounded-2xl p-8 shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2">
<div class="absolute inset-0 bg-gradient-to-br from-purple-600 to-pink-600 rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
<div class="relative z-10">
<div class="w-16 h-16 bg-gradient-to-br from-purple-600 to-pink-600 rounded-xl flex items-center justify-center mb-6 group-hover:bg-white transition-colors duration-300">
<svg class="w-8 h-8 text-white group-hover:text-purple-600 transition-colors duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z"/>
</svg>
</div>
<h3 class="text-xl font-bold text-gray-900 group-hover:text-white mb-3 transition-colors duration-300">Media Kreatif</h3>
<p class="text-gray-600 group-hover:text-white/90 transition-colors duration-300">Membuat konten visual dan naratif yang menginspirasi.</p>
</div>
</div>
</div>
</div>
</section>
<section class="py-24 bg-white">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
<div class="max-w-3xl mx-auto text-center">
<h2 class="text-4xl sm:text-5xl font-display font-bold text-gray-900 mb-6">Hubungi Kami</h2>
<p class="text-lg text-gray-600 mb-12">Siap bergabung atau punya ide kolaborasi? Mari kita wujudkan bersama!</p>
<div class="bg-gradient-to-br from-violet-600 via-blue-600 to-emerald-500 rounded-2xl p-12 text-white shadow-2xl">
<div class="flex items-center justify-center space-x-4 mb-6">
<svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
</svg>
<div class="text-left">
<p class="text-sm text-white/80 mb-1">WhatsApp / Telepon</p>
<a href="tel:+6281356019609" class="text-2xl font-bold hover:text-white/90 transition-colors duration-200">+62 813-5601-9609</a>
</div>
</div>
<p class="text-white/90">Hubungi kami untuk informasi lebih lanjut tentang kolaborasi dan keanggotaan!</p>
</div>
</div>
</div>
</section>
<footer class="bg-gray-900 text-white py-12">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
<div class="flex items-center justify-center space-x-3 mb-4">
<img src="{{ asset('logo-no-bg.png') }}" alt="SISARAYA Logo" class="h-10 w-auto">
<span class="text-xl font-bold">SISARAYA</span>
</div>
<p class="text-gray-400 text-sm">&copy; {{ date('Y') }} SISARAYA. Satu semangat, banyak ekspresi.</p>
</div>
</footer>
</body>
</html>