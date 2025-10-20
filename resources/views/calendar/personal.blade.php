@extends('layouts.app')

@section('content')
<div class="py-6 sm:py-12" x-data="{ viewMode: 'all' }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Modern Gradient Header -->
        <div class="bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 rounded-3xl shadow-2xl p-8 mb-8">
            <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6">
                <div class="flex items-center gap-4">
                    <div class="bg-white/20 backdrop-blur-sm p-4 rounded-2xl">
                        <svg class="h-10 w-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-white mb-1">Kalender Pribadi</h1>
                        <p class="text-indigo-100 text-sm">Kelola jadwal dan kegiatan pribadi Anda</p>
                    </div>
                </div>
                <button onclick="openActivityModal()" 
                        class="bg-white text-indigo-600 hover:bg-indigo-50 px-6 py-3 rounded-xl font-bold shadow-lg hover:shadow-xl transform hover:scale-105 transition flex items-center gap-2">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Tambah Kegiatan
                </button>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mt-8">
                <!-- Total Kegiatan -->
                <div class="bg-white/10 backdrop-blur-md rounded-2xl p-5 border border-white/20">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-white/80 text-sm font-medium">Total Kegiatan</p>
                            <p class="text-white text-3xl font-bold mt-1" id="stat-total">0</p>
                        </div>
                        <div class="bg-white/20 p-3 rounded-xl">
                            <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Kegiatan Public -->
                <div class="bg-white/10 backdrop-blur-md rounded-2xl p-5 border border-white/20">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-white/80 text-sm font-medium flex items-center gap-2">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                Public
                            </p>
                            <p class="text-white text-3xl font-bold mt-1" id="stat-public">0</p>
                        </div>
                        <div class="bg-green-500/30 p-3 rounded-xl">
                            <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Kegiatan Private -->
                <div class="bg-white/10 backdrop-blur-md rounded-2xl p-5 border border-white/20">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-white/80 text-sm font-medium flex items-center gap-2">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                                Private
                            </p>
                            <p class="text-white text-3xl font-bold mt-1" id="stat-private">0</p>
                        </div>
                        <div class="bg-red-500/30 p-3 rounded-xl">
                            <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Upcoming -->
                <div class="bg-white/10 backdrop-blur-md rounded-2xl p-5 border border-white/20">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-white/80 text-sm font-medium flex items-center gap-2">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Mendatang
                            </p>
                            <p class="text-white text-3xl font-bold mt-1" id="stat-upcoming">0</p>
                        </div>
                        <div class="bg-yellow-500/30 p-3 rounded-xl">
                            <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- View Mode Filters -->
            <div class="flex flex-wrap gap-3 mt-6">
                <button @click="viewMode = 'all'; window.updateCalendarFilter('all')"
                        :class="viewMode === 'all' ? 'bg-white text-indigo-600 shadow-lg' : 'bg-white/20 text-white hover:bg-white/30'"
                        class="px-5 py-2.5 rounded-xl font-semibold transition transform hover:scale-105 flex items-center gap-2">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    Semua Kegiatan
                </button>
                <button @click="viewMode = 'own'; window.updateCalendarFilter('own')"
                        :class="viewMode === 'own' ? 'bg-white text-indigo-600 shadow-lg' : 'bg-white/20 text-white hover:bg-white/30'"
                        class="px-5 py-2.5 rounded-xl font-semibold transition transform hover:scale-105 flex items-center gap-2">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    Kegiatan Saya
                </button>
                <button @click="viewMode = 'public'; window.updateCalendarFilter('public')"
                        :class="viewMode === 'public' ? 'bg-white text-indigo-600 shadow-lg' : 'bg-white/20 text-white hover:bg-white/30'"
                        class="px-5 py-2.5 rounded-xl font-semibold transition transform hover:scale-105 flex items-center gap-2">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    Public Only
                </button>
            </div>
        </div>

        <!-- Modern Info Card -->
        <div class="mb-8 bg-gradient-to-r from-blue-50 to-indigo-50 border-2 border-blue-200 rounded-2xl shadow-lg overflow-hidden">
            <div class="p-6">
                <div class="flex items-start gap-4">
                    <div class="bg-blue-500 p-3 rounded-xl">
                        <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-bold text-blue-900 mb-3 flex items-center gap-2">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            Kalender ini menampilkan:
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm text-blue-800">
                            <div class="flex items-start gap-2">
                                <svg class="h-5 w-5 text-green-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <div>
                                    <strong>Tiket Saya</strong> - Tiket yang sudah Anda klaim (warna sesuai status)
                                </div>
                            </div>
                            <div class="flex items-start gap-2">
                                <svg class="h-5 w-5 text-orange-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                </svg>
                                <div>
                                    <strong>Tiket Tersedia</strong> - Tiket yang bisa Anda klaim (warna oranye)
                                </div>
                            </div>
                            <div class="flex items-start gap-2">
                                <svg class="h-5 w-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <div>
                                    <strong>Timeline Proyek</strong> - Periode proyek yang Anda ikuti (background)
                                </div>
                            </div>
                            <div class="flex items-start gap-2">
                                <svg class="h-5 w-5 text-purple-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                                </svg>
                                <div>
                                    <strong>Event Proyek</strong> - Event dari proyek yang Anda ikuti (ungu)
                                </div>
                            </div>
                            <div class="flex items-start gap-2">
                                <svg class="h-5 w-5 text-indigo-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                <div>
                                    <strong>Kegiatan Pribadi</strong> - Jadwal pribadi Anda (beragam warna)
                                </div>
                            </div>
                        </div>
                        <div class="mt-4 p-4 bg-white rounded-xl border-2 border-yellow-300">
                            <p class="text-sm text-gray-800 font-semibold flex items-center gap-2">
                                <svg class="h-5 w-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                                <strong>Privasi Kegiatan:</strong>
                            </p>
                            <p class="text-sm text-gray-700 mt-2">
                                • <strong>Private (default)</strong> - Hanya Anda yang bisa melihat detail kegiatan<br>
                                • <strong>Public</strong> - Anggota lain hanya melihat "Sibuk - [Nama Anda]" tanpa detail
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Calendar -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div id="personal-calendar" class="w-full"></div>
            </div>
        </div>
        
        <!-- Legend -->
        <div class="mt-6 bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Legenda</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Tiket Status -->
                <div>
                    <h4 class="text-sm font-medium text-gray-700 mb-2">Status Tiket Saya</h4>
                    <div class="space-y-2 text-sm">
                        <div class="flex items-center gap-2">
                            <div class="w-4 h-4 rounded bg-gray-500"></div>
                            <span>To Do</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-4 h-4 rounded bg-blue-500"></div>
                            <span>Doing</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-4 h-4 rounded bg-green-500"></div>
                            <span>Done</span>
                        </div>
                    </div>
                </div>

                <!-- Tiket Tersedia -->
                <div>
                    <h4 class="text-sm font-medium text-gray-700 mb-2">Tiket Tersedia</h4>
                    <div class="space-y-2 text-sm">
                        <div class="flex items-center gap-2">
                            <div class="w-4 h-4 rounded bg-orange-500"></div>
                            <span>Tiket yang bisa diklaim</span>
                        </div>
                    </div>
                </div>

                <!-- Event & Proyek -->
                <div>
                    <h4 class="text-sm font-medium text-gray-700 mb-2">Event & Timeline Proyek</h4>
                    <div class="space-y-2 text-sm">
                        <div class="flex items-center gap-2">
                            <div class="w-4 h-4 rounded bg-purple-500"></div>
                            <span>Event Proyek</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-4 h-4 rounded bg-gray-300 opacity-50"></div>
                            <span>Timeline Proyek</span>
                        </div>
                    </div>
                </div>

                <!-- Kegiatan Pribadi -->
                <div>
                    <h4 class="text-sm font-medium text-gray-700 mb-2">Jenis Kegiatan Pribadi</h4>
                    <div class="space-y-2 text-sm">
                        <div class="flex items-center gap-2">
                            <div class="w-4 h-4 rounded" style="background-color: #3b82f6;"></div>
                            <span>Pribadi</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-4 h-4 rounded" style="background-color: #10b981;"></div>
                            <span>Keluarga</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-4 h-4 rounded" style="background-color: #f59e0b;"></div>
                            <span>Pekerjaan Luar</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-4 h-4 rounded" style="background-color: #8b5cf6;"></div>
                            <span>Pendidikan</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-4 h-4 rounded" style="background-color: #ef4444;"></div>
                            <span>Kesehatan</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modern Modal Create/Edit Personal Activity --}}
<div id="activityModal" x-cloak class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl shadow-2xl max-w-3xl w-full max-h-[90vh] overflow-y-auto transform transition-all">
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 p-6 rounded-t-3xl">
            <div class="flex justify-between items-center">
                <h2 id="modalTitle" class="text-2xl font-bold text-white flex items-center gap-3">
                    <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Tambah Kegiatan Pribadi
                </h2>
                <button onclick="closeActivityModal()" class="text-white/80 hover:text-white transition p-2 hover:bg-white/20 rounded-xl">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        <form id="activityForm" onsubmit="saveActivity(event)" class="p-6">
            <input type="hidden" id="activityId" name="activity_id">
            
            <div class="space-y-5">
                    <div>
                        <label class="block text-base font-bold text-gray-900 mb-2 flex items-center gap-2">
                            <svg class="h-5 w-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                            </svg>
                            Judul Kegiatan *
                        </label>
                        <input type="text" id="title" name="title" required 
                            class="w-full border-2 border-gray-300 rounded-xl px-4 py-3 text-base focus:ring-4 focus:ring-indigo-200 focus:border-indigo-500 transition"
                            placeholder="Misal: Konsultasi Dokter, Kuliah, Rapat, dll">
                    </div>

                    <div>
                        <label class="block text-base font-bold text-gray-900 mb-2 flex items-center gap-2">
                            <svg class="h-5 w-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Deskripsi
                        </label>
                        <textarea id="description" name="description" rows="3"
                            class="w-full border-2 border-gray-300 rounded-xl px-4 py-3 text-base focus:ring-4 focus:ring-indigo-200 focus:border-indigo-500 transition"
                            placeholder="Keterangan tambahan (opsional)"></textarea>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-base font-bold text-gray-900 mb-2 flex items-center gap-2">
                                <svg class="h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Waktu Mulai *
                            </label>
                            <input type="datetime-local" id="start_time" name="start_time" required
                                class="w-full border-2 border-gray-300 rounded-xl px-4 py-3 text-base focus:ring-4 focus:ring-green-200 focus:border-green-500 transition">
                        </div>
                        <div>
                            <label class="block text-base font-bold text-gray-900 mb-2 flex items-center gap-2">
                                <svg class="h-5 w-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Waktu Selesai *
                            </label>
                            <input type="datetime-local" id="end_time" name="end_time" required
                                class="w-full border-2 border-gray-300 rounded-xl px-4 py-3 text-base focus:ring-4 focus:ring-red-200 focus:border-red-500 transition">
                        </div>
                    </div>

                    <div>
                        <label class="block text-base font-bold text-gray-900 mb-2 flex items-center gap-2">
                            <svg class="h-5 w-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            Lokasi
                        </label>
                        <input type="text" id="location" name="location"
                            class="w-full border-2 border-gray-300 rounded-xl px-4 py-3 text-base focus:ring-4 focus:ring-purple-200 focus:border-purple-500 transition"
                            placeholder="Lokasi kegiatan (opsional)">
                    </div>

                    <div>
                        <label class="block text-base font-bold text-gray-900 mb-2 flex items-center gap-2">
                            <svg class="h-5 w-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                            </svg>
                            Kategori *
                        </label>
                        <select id="type" name="type" required
                            class="w-full border-2 border-gray-300 rounded-xl px-4 py-3 text-base focus:ring-4 focus:ring-orange-200 focus:border-orange-500 transition">
                            <option value="personal">Pribadi</option>
                            <option value="family">Keluarga</option>
                            <option value="work_external">Pekerjaan Luar</option>
                            <option value="study">Pendidikan</option>
                            <option value="health">Kesehatan</option>
                            <option value="other">Lainnya</option>
                        </select>
                    </div>

                    <!-- Privacy Setting - Default Private -->
                    <div class="bg-gradient-to-r from-yellow-50 to-orange-50 border-2 border-yellow-300 rounded-2xl p-5">
                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0">
                                <div class="bg-yellow-400 p-3 rounded-xl">
                                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-1">
                                <label class="text-base font-bold text-gray-900 mb-2 block flex items-center gap-2">
                                    <svg class="h-5 w-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                    Pengaturan Privasi
                                </label>
                                <div class="space-y-3">
                                    <label class="flex items-start gap-3 cursor-pointer group">
                                        <input type="radio" name="privacy" value="0" checked
                                            class="mt-1 w-5 h-5 text-indigo-600 border-2 border-gray-400 focus:ring-4 focus:ring-indigo-200">
                                        <div class="flex items-start gap-2">
                                            <svg class="h-5 w-5 text-indigo-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                            </svg>
                                            <div>
                                                <p class="font-bold text-gray-900 group-hover:text-indigo-600">Private (Rekomendasi)</p>
                                                <p class="text-sm text-gray-600">Hanya Anda yang bisa melihat detail kegiatan ini</p>
                                            </div>
                                        </div>
                                    </label>
                                    <label class="flex items-start gap-3 cursor-pointer group">
                                        <input type="radio" name="privacy" value="1"
                                            class="mt-1 w-5 h-5 text-green-600 border-2 border-gray-400 focus:ring-4 focus:ring-green-200">
                                        <div class="flex items-start gap-2">
                                            <svg class="h-5 w-5 text-green-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            <div>
                                                <p class="font-bold text-gray-900 group-hover:text-green-600">Public</p>
                                                <p class="text-sm text-gray-600">Anggota lain hanya melihat "Sibuk - [Nama Anda]" tanpa detail</p>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-3 mt-8 pt-6 border-t-2 border-gray-100">
                    <button type="submit" class="flex-1 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white px-6 py-4 rounded-xl font-bold shadow-lg hover:shadow-xl transform hover:scale-105 transition flex items-center justify-center gap-2">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                        </svg>
                        Simpan Kegiatan
                    </button>
                    <button type="button" onclick="closeActivityModal()" class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-800 px-6 py-4 rounded-xl font-bold hover:shadow-lg transform hover:scale-105 transition flex items-center justify-center gap-2">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Batal
                    </button>
                    <button type="button" id="deleteBtn" onclick="deleteActivity()" class="bg-red-600 hover:bg-red-700 text-white px-6 py-4 rounded-xl font-bold shadow-lg hover:shadow-xl transform hover:scale-105 transition hidden flex items-center gap-2">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Hapus
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- FullCalendar CSS from CDN --}}
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.5/main.min.css' rel='stylesheet' />

<style>
/* Force calendar visibility and table display */
#personal-calendar {
    min-height: 600px;
    background: white;
    width: 100%;
    display: block !important;
    visibility: visible !important;
}

#personal-calendar * {
    visibility: visible !important;
}

/* Force all table elements to display */
.fc table { display: table !important; }
.fc thead { display: table-header-group !important; }
.fc tbody { display: table-row-group !important; }
.fc tr { display: table-row !important; }
.fc th, .fc td { display: table-cell !important; }

.fc-scrollgrid { 
    display: table !important; 
    width: 100% !important;
}

.fc-scrollgrid-section { display: table-row-group !important; }
.fc-scrollgrid-section > td { display: table-cell !important; }

.fc-daygrid-body {
    width: 100% !important;
    display: table-row-group !important;
}

.fc-daygrid-day {
    height: 80px !important;
    min-height: 80px !important;
    display: table-cell !important;
    border: 1px solid #e5e7eb !important;
}

.fc-daygrid-day-number {
    display: block !important;
    font-size: 1rem;
    padding: 4px;
    color: #374151;
}

.fc-col-header-cell {
    display: table-cell !important;
    padding: 10px;
    background: #f9fafb;
    border: 1px solid #e5e7eb;
}

.fc-view-harness {
    min-height: 500px;
    display: block !important;
}

.fc-scroller {
    overflow: visible !important;
}
</style>

{{-- FullCalendar JS from CDN --}}
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.5/main.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.5/locales/id.js'></script>

<script>
let calendar;
let currentActivity = null;

let currentViewMode = 'all';

document.addEventListener('DOMContentLoaded', function() {
    const calendarEl = document.getElementById('personal-calendar');
    
    if (calendarEl) {
        console.log('[INIT] Initializing Personal Calendar...');
        
        calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            height: 700,
            contentHeight: 650,
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            eventSources: [
                {
                    url: '/api/calendar/user/events', // Tickets & Events
                    color: '#3b82f6'
                },
                {
                    url: '/api/personal-activities',
                    extraParams: function() {
                        return {
                            view_mode: currentViewMode
                        };
                    },
                    color: '#10b981'
                }
            ],
            eventClick: function(info) {
                const eventId = info.event.id;
                const props = info.event.extendedProps;
                
                // Check if it's a personal activity (can be edited)
                if (eventId && eventId.toString().startsWith('personal-')) {
                    const activityId = eventId.replace('personal-', '');
                    editActivity(activityId, info.event);
                } else if (props.url) {
                    // Navigate to ticket/project URL
                    if (confirm('Ingin melihat detail ' + props.type + '?\n\n' + info.event.title)) {
                        window.location.href = props.url;
                    }
                } else {
                    // Show event details in alert
                    showEventDetails(info.event);
                }
            },
            dateClick: function(info) {
                // Quick add activity by clicking date
                openActivityModal(info.dateStr);
            },
            editable: false,
            selectable: true,
            locale: 'id',
            buttonText: {
                today: 'Hari Ini',
                month: 'Bulan',
                week: 'Minggu',
                day: 'Hari'
            },
            viewDidMount: function(info) {
                console.log('[VIEW] Personal calendar view mounted:', info.view.type);
            }
        });
        
        console.log('[RENDER] Rendering personal calendar...');
        calendar.render();
        
        // Force update size after render
        setTimeout(() => {
            if (calendar) {
                calendar.updateSize();
                console.log('[RESIZE] Personal calendar size updated');
            }
        }, 100);
        
        // Additional force render
        setTimeout(() => {
            if (calendar) {
                calendar.render();
                calendar.updateSize();
                console.log('[REFRESH] Personal calendar force re-rendered');
            }
        }, 1000);
        
        // Load stats
        loadStats();
        
        console.log('Personal calendar initialized');
    }
});

// Update calendar filter
window.updateCalendarFilter = function(mode) {
    currentViewMode = mode;
    if (calendar) {
        calendar.refetchEvents();
    }
};

// Load statistics
function loadStats() {
    fetch('/api/personal-activities/stats', {
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById('stat-total').textContent = data.total || 0;
        document.getElementById('stat-public').textContent = data.public || 0;
        document.getElementById('stat-private').textContent = data.private || 0;
        document.getElementById('stat-upcoming').textContent = data.upcoming || 0;
    })
    .catch(error => {
        console.error('Error loading stats:', error);
    });
}

function openActivityModal(date = null) {
    document.getElementById('modalTitle').innerHTML = `
        <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
        </svg>
        Tambah Kegiatan Pribadi
    `;
    document.getElementById('activityForm').reset();
    document.getElementById('activityId').value = '';
    document.getElementById('deleteBtn').classList.add('hidden');
    
    // Set privacy to PRIVATE by default (value="0")
    const privateRadio = document.querySelector('input[name="privacy"][value="0"]');
    if (privateRadio) {
        privateRadio.checked = true;
    }
    
    if (date) {
        const dateObj = new Date(date);
        const localDate = new Date(dateObj.getTime() - dateObj.getTimezoneOffset() * 60000);
        const dateStr = localDate.toISOString().slice(0, 16);
        document.getElementById('start_time').value = dateStr;
        
        const endDate = new Date(localDate.getTime() + 3600000); // +1 hour
        document.getElementById('end_time').value = endDate.toISOString().slice(0, 16);
    }
    
    document.getElementById('activityModal').classList.remove('hidden');
}

function closeActivityModal() {
    document.getElementById('activityModal').classList.add('hidden');
    currentActivity = null;
}

function editActivity(activityId, event) {
    const props = event.extendedProps;
    
    // Check if user owns this activity
    if (!props.isOwn) {
        showEventDetails(event);
        return;
    }
    
    document.getElementById('modalTitle').innerHTML = `
        <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
        </svg>
        Edit Kegiatan Pribadi
    `;
    document.getElementById('activityId').value = activityId;
    document.getElementById('title').value = event.title;
    document.getElementById('description').value = props.description || '';
    document.getElementById('location').value = props.location || '';
    document.getElementById('type').value = props.type;
    
    // Set privacy radio buttons
    const privacyValue = props.isPublic ? '1' : '0';
    const privacyRadio = document.querySelector(`input[name="privacy"][value="${privacyValue}"]`);
    if (privacyRadio) {
        privacyRadio.checked = true;
    }
    
    // Set datetime
    const start = new Date(event.start);
    const end = new Date(event.end);
    document.getElementById('start_time').value = new Date(start.getTime() - start.getTimezoneOffset() * 60000).toISOString().slice(0, 16);
    document.getElementById('end_time').value = new Date(end.getTime() - end.getTimezoneOffset() * 60000).toISOString().slice(0, 16);
    
    document.getElementById('deleteBtn').classList.remove('hidden');
    document.getElementById('activityModal').classList.remove('hidden');
}

function saveActivity(event) {
    event.preventDefault();
    
    const activityId = document.getElementById('activityId').value;
    
    // Get privacy setting from radio buttons
    const privacyRadio = document.querySelector('input[name="privacy"]:checked');
    const isPublic = privacyRadio ? parseInt(privacyRadio.value) : 0;
    
    const formData = {
        title: document.getElementById('title').value,
        description: document.getElementById('description').value,
        start_time: document.getElementById('start_time').value,
        end_time: document.getElementById('end_time').value,
        location: document.getElementById('location').value,
        type: document.getElementById('type').value,
        is_public: isPublic,
    };
    
    const url = activityId ? `/personal-activities/${activityId}` : '/personal-activities';
    const method = activityId ? 'PUT' : 'POST';
    
    fetch(url, {
        method: method,
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify(formData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            closeActivityModal();
            calendar.refetchEvents();
            loadStats(); // Reload stats
        } else {
            alert('Error: ' + (data.message || 'Gagal menyimpan'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat menyimpan');
    });
}

function deleteActivity() {
    const activityId = document.getElementById('activityId').value;
    
    if (!activityId || !confirm('Yakin ingin menghapus kegiatan ini?\n\nTindakan ini tidak dapat dibatalkan.')) {
        return;
    }
    
    fetch(`/personal-activities/${activityId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            closeActivityModal();
            calendar.refetchEvents();
            loadStats(); // Reload stats
        } else {
            alert('Error: ' + (data.message || 'Gagal menghapus'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat menghapus');
    });
}

function showEventDetails(event) {
    const props = event.extendedProps;
    let details = '' + event.title + '\n\n';
    
    if (props.type) {
        details += 'Jenis: ' + props.type + '\n';
    }
    if (props.status) {
        details += 'Status: ' + props.status + '\n';
    }
    if (props.project_name) {
        details += 'Proyek: ' + props.project_name + '\n';
    }
    if (props.target_role) {
        details += 'Target: ' + props.target_role + '\n';
    }
    if (props.location) {
        details += 'Lokasi: ' + props.location + '\n';
    }
    if (event.start) {
        const start = new Date(event.start);
        details += 'Mulai: ' + start.toLocaleString('id-ID') + '\n';
    }
    if (event.end && !props.type?.includes('Proyek')) {
        const end = new Date(event.end);
        details += 'Selesai: ' + end.toLocaleString('id-ID') + '\n';
    }
    if (props.description) {
        details += '\nDeskripsi:\n' + props.description;
    }
    
    if (props.url) {
        details += '\n\nKlik OK untuk melihat detail lengkap';
        if (confirm(details)) {
            window.location.href = props.url;
        }
    } else {
        alert(details);
    }
}
</script>
@endsection
