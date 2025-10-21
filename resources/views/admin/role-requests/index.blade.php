@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
    
    {{-- Header --}}
    <div class="mb-8">
        <div class="flex items-center gap-4 mb-3">
            <div class="bg-gradient-to-r from-violet-600 to-blue-600 p-4 rounded-2xl shadow-lg">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                </svg>
            </div>
            <div>
                <h1 class="text-3xl font-bold bg-gradient-to-r from-violet-600 to-blue-600 bg-clip-text text-transparent">
                    Manajemen Request Role
                </h1>
                <p class="text-gray-600">Review dan approve/reject request perubahan role dari anggota</p>
            </div>
        </div>

        {{-- Stats --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
            <div class="bg-gradient-to-br from-yellow-50 to-amber-50 rounded-xl p-5 border-2 border-yellow-200 shadow-md">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold text-yellow-700 mb-1">Pending</p>
                        <p class="text-3xl font-bold text-yellow-600">{{ $pendingRequests->count() }}</p>
                    </div>
                    <div class="bg-yellow-200 p-3 rounded-xl">
                        <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-xl p-5 border-2 border-green-200 shadow-md">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold text-green-700 mb-1">Approved</p>
                        <p class="text-3xl font-bold text-green-600">{{ $processedRequests->where('status', 'approved')->count() }}</p>
                    </div>
                    <div class="bg-green-200 p-3 rounded-xl">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-red-50 to-pink-50 rounded-xl p-5 border-2 border-red-200 shadow-md">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold text-red-700 mb-1">Rejected</p>
                        <p class="text-3xl font-bold text-red-600">{{ $processedRequests->where('status', 'rejected')->count() }}</p>
                    </div>
                    <div class="bg-red-200 p-3 rounded-xl">
                        <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="mb-6 bg-gradient-to-r from-green-50 to-emerald-50 border-l-4 border-green-500 text-green-700 px-6 py-4 rounded-xl shadow-md">
            <div class="flex items-center gap-3">
                <svg class="h-6 w-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    {{-- Pending Requests Section --}}
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-4 flex items-center gap-2">
            <span class="bg-gradient-to-r from-yellow-500 to-amber-600 w-1 h-8 rounded-full"></span>
            Request Pending
            @if($pendingRequests->count() > 0)
                <span class="px-3 py-1 bg-yellow-100 text-yellow-700 text-sm font-bold rounded-full">
                    {{ $pendingRequests->count() }}
                </span>
            @endif
        </h2>

        <div class="space-y-4">
            @forelse($pendingRequests as $request)
                <div class="bg-gradient-to-br from-white to-yellow-50 rounded-2xl shadow-xl border-2 border-yellow-200 p-6 hover:shadow-2xl transition-all duration-300" 
                     x-data="{ showReviewForm: false, action: '' }">
                    
                    {{-- Request Header --}}
                    <div class="flex items-start justify-between mb-5">
                        <div class="flex items-start gap-4">
                            {{-- User Photo --}}
                            @if($request->user->photo_path)
                                <img src="{{ asset('storage/' . $request->user->photo_path) }}" 
                                     alt="{{ $request->user->name }}" 
                                     class="w-16 h-16 rounded-xl object-cover border-2 border-yellow-300 shadow-md">
                            @else
                                <div class="w-16 h-16 rounded-xl bg-gradient-to-br from-violet-500 to-blue-600 flex items-center justify-center text-white text-2xl font-bold shadow-md border-2 border-yellow-300">
                                    {{ strtoupper(substr($request->user->name, 0, 1)) }}
                                </div>
                            @endif

                            <div>
                                <h3 class="text-xl font-bold text-gray-900 mb-1">{{ $request->user->name }}</h3>
                                <p class="text-sm text-gray-600 mb-2">@if($request->user->username)<span class="text-gray-500">@</span>{{ $request->user->username }}@endif</p>
                                <div class="flex items-center gap-2 text-sm text-gray-500">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Diajukan {{ $request->created_at->diffForHumans() }}
                                </div>
                            </div>
                        </div>

                        <span class="px-4 py-2 bg-gradient-to-r from-yellow-400 to-amber-500 text-white text-sm font-bold rounded-full shadow-lg animate-pulse">
                            ⏳ PENDING
                        </span>
                    </div>

                    {{-- Role Changes --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-5">
                        {{-- Current Roles --}}
                        <div class="bg-white rounded-xl p-4 border-2 border-gray-200 shadow-sm">
                            <h4 class="text-sm font-bold text-gray-700 mb-3 flex items-center gap-2">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Role Sebelumnya
                            </h4>
                            <div class="flex flex-wrap gap-2">
                                @if($request->current_roles && count($request->current_roles) > 0)
                                    @foreach($request->current_roles as $role)
                                        <x-users.role-badge :role="$role" />
                                    @endforeach
                                @else
                                    <span class="text-sm text-gray-500 italic">Belum ada role</span>
                                @endif
                            </div>
                        </div>

                        {{-- Requested Roles --}}
                        <div class="bg-gradient-to-br from-violet-50 to-blue-50 rounded-xl p-4 border-2 border-violet-300 shadow-sm">
                            <h4 class="text-sm font-bold text-gray-700 mb-3 flex items-center gap-2">
                                <svg class="w-4 h-4 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                                Role yang Diminta
                            </h4>
                            <div class="flex flex-wrap gap-2">
                                @foreach($request->requested_roles as $role)
                                    <x-users.role-badge :role="$role" />
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- Reason --}}
                    <div class="bg-gray-50 rounded-xl p-4 mb-5 border-2 border-gray-200">
                        <h4 class="text-sm font-bold text-gray-700 mb-2 flex items-center gap-2">
                            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Alasan Request
                        </h4>
                        <p class="text-sm text-gray-700 leading-relaxed">{{ $request->reason }}</p>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="flex flex-wrap items-center gap-3">
                        <button @click="showReviewForm = true; action = 'approve'" 
                                class="flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-green-500 to-emerald-600 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl hover:scale-105 transition-all duration-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Approve
                        </button>

                        <button @click="showReviewForm = true; action = 'reject'" 
                                class="flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-red-500 to-pink-600 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl hover:scale-105 transition-all duration-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Reject
                        </button>
                    </div>

                    {{-- Review Form (Alpine.js Toggle) --}}
                    <div x-show="showReviewForm" 
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 transform scale-95"
                         x-transition:enter-end="opacity-100 transform scale-100"
                         class="mt-5 pt-5 border-t-2 border-gray-200">
                        
                        <h4 class="text-lg font-bold text-gray-900 mb-4" x-text="action === 'approve' ? 'Approve Request' : 'Reject Request'"></h4>
                        
                        <form :action="action === 'approve' ? '{{ route('admin.role-requests.approve', $request) }}' : '{{ route('admin.role-requests.reject', $request) }}'" 
                              method="POST">
                            @csrf
                            
                            <div class="mb-4">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Catatan <span x-show="action === 'reject'" class="text-red-500">*</span>
                                    <span class="text-gray-500 font-normal">(untuk user)</span>
                                </label>
                                <textarea name="review_note" 
                                          rows="4" 
                                          :required="action === 'reject'"
                                          class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-all"
                                          :placeholder="action === 'approve' ? 'Catatan opsional untuk user...' : 'Jelaskan alasan penolakan (wajib)...'"></textarea>
                            </div>

                            <div class="flex items-center gap-3">
                                <button type="submit" 
                                        :class="action === 'approve' ? 'bg-gradient-to-r from-green-500 to-emerald-600' : 'bg-gradient-to-r from-red-500 to-pink-600'"
                                        class="px-6 py-3 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl hover:scale-105 transition-all duration-300">
                                    <span x-text="action === 'approve' ? 'Konfirmasi Approve' : 'Konfirmasi Reject'"></span>
                                </button>

                                <button type="button" 
                                        @click="showReviewForm = false"
                                        class="px-6 py-3 border-2 border-gray-300 text-gray-700 font-medium rounded-xl hover:bg-gray-50 transition-all">
                                    Batal
                                </button>
                            </div>
                        </form>
                    </div>

                </div>
            @empty
                {{-- Empty State --}}
                <div class="bg-gradient-to-br from-white to-gray-50 rounded-2xl shadow-xl border-2 border-gray-200 p-12 text-center">
                    <div class="bg-gradient-to-br from-yellow-100 to-amber-100 w-20 h-20 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-10 h-10 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Tidak Ada Request Pending</h3>
                    <p class="text-gray-600">Semua request sudah diproses. Bagus! 🎉</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Processed Requests (Recent) --}}
    @if($processedRequests->count() > 0)
        <div>
            <h2 class="text-2xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                <span class="bg-gradient-to-r from-gray-500 to-gray-700 w-1 h-8 rounded-full"></span>
                Request Terproses (20 Terbaru)
            </h2>

            <div class="space-y-3">
                @foreach($processedRequests as $request)
                    <div class="bg-white rounded-xl shadow-md border-2 
                                @if($request->status === 'approved') border-green-200
                                @else border-red-200
                                @endif 
                                p-5 hover:shadow-lg transition-all">
                        
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-4 flex-1">
                                {{-- User Info --}}
                                <div class="flex items-center gap-3">
                                    @if($request->user->photo_path)
                                        <img src="{{ asset('storage/' . $request->user->photo_path) }}" 
                                             alt="{{ $request->user->name }}" 
                                             class="w-12 h-12 rounded-lg object-cover border-2 border-gray-200 shadow-sm">
                                    @else
                                        <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-violet-500 to-blue-600 flex items-center justify-center text-white text-lg font-bold shadow-sm">
                                            {{ strtoupper(substr($request->user->name, 0, 1)) }}
                                        </div>
                                    @endif

                                    <div>
                                        <p class="font-bold text-gray-900">{{ $request->user->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $request->reviewed_at->format('d M Y, H:i') }}</p>
                                    </div>
                                </div>

                                {{-- Roles --}}
                                <div class="flex items-center gap-2 flex-1">
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($request->requested_roles as $role)
                                            <x-users.role-badge :role="$role" />
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            {{-- Status Badge --}}
                            @if($request->status === 'approved')
                                <span class="px-3 py-1 bg-gradient-to-r from-green-500 to-emerald-600 text-white text-xs font-bold rounded-full shadow-md">
                                    ✓ APPROVED
                                </span>
                            @else
                                <span class="px-3 py-1 bg-gradient-to-r from-red-500 to-pink-600 text-white text-xs font-bold rounded-full shadow-md">
                                    ✗ REJECTED
                                </span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

</div>
@endsection
