<!-- Role Change Request Modal -->
<div x-data="{ 
    showModal: false,
    selectedRoles: {{ json_encode(old('requested_roles', [])) }},
    init() {
        // Listen for open modal event
        this.$watch('showModal', value => {
            if (value) {
                document.body.style.overflow = 'hidden';
            } else {
                document.body.style.overflow = '';
            }
        });
    }
}" 
@open-role-request-modal.window="showModal = true"
@keydown.escape.window="showModal = false"
class="relative z-50">

    <!-- Modal Backdrop -->
    <div x-show="showModal"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm"
         @click="showModal = false"
         style="display: none;">
    </div>

    <!-- Modal Content -->
    <div x-show="showModal"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform scale-95"
         x-transition:enter-end="opacity-100 transform scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 transform scale-100"
         x-transition:leave-end="opacity-0 transform scale-95"
         class="fixed inset-0 z-50 overflow-y-auto"
         style="display: none;">
        
        <div class="flex min-h-full items-center justify-center p-4">
            <div @click.away="showModal = false" class="bg-white rounded-2xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
                
                <!-- Modal Header -->
                <div class="sticky top-0 bg-gradient-to-r from-purple-600 to-pink-600 px-6 py-4 flex items-center justify-between rounded-t-2xl z-10">
                    <div class="flex items-center gap-3">
                        <div class="bg-white bg-opacity-20 p-2 rounded-lg">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-white">Request Perubahan Role</h3>
                            <p class="text-sm text-purple-100">Ajukan permintaan perubahan role kepada HR</p>
                        </div>
                    </div>
                    <button @click="showModal = false" class="text-white hover:bg-white hover:bg-opacity-20 p-2 rounded-lg transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="p-6">
                    
                    @if(session('success'))
                        <div class="mb-6 p-4 bg-green-100 border-2 border-green-200 rounded-xl flex items-center gap-3">
                            <svg class="w-6 h-6 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="text-green-700 font-semibold">{{ session('success') }}</span>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="mb-6 p-4 bg-red-100 border-2 border-red-200 rounded-xl">
                            <div class="flex items-center gap-3 mb-2">
                                <svg class="w-6 h-6 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span class="text-red-700 font-semibold">Terjadi kesalahan:</span>
                            </div>
                            <ul class="list-disc list-inside text-red-600 text-sm">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Pending Request Alert -->
                    @if($pendingRequest)
                        <div class="mb-6 bg-gradient-to-br from-yellow-50 to-orange-50 border-2 border-yellow-300 rounded-2xl p-6">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex items-start gap-3 flex-1">
                                    <div class="bg-yellow-500 p-3 rounded-xl">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="font-bold text-gray-900 mb-2">Request Pending</h4>
                                        <p class="text-sm text-gray-700 mb-3">
                                            Anda memiliki request yang sedang menunggu persetujuan dari HR.
                                        </p>
                                        <div class="bg-white rounded-xl p-4 border-2 border-yellow-200 space-y-2">
                                            <div class="flex flex-wrap gap-2">
                                                <span class="text-xs text-gray-600 font-semibold">Role yang diminta:</span>
                                                @foreach($pendingRequest->requested_roles as $role)
                                                    <span class="px-3 py-1 bg-gradient-to-r from-purple-500 to-pink-500 text-white rounded-full text-xs font-semibold">
                                                        {{ ucfirst($role) }}
                                                    </span>
                                                @endforeach
                                            </div>
                                            <div>
                                                <span class="text-xs text-gray-600 font-semibold">Alasan:</span>
                                                <p class="text-sm text-gray-700 mt-1">{{ $pendingRequest->reason }}</p>
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                Diajukan {{ $pendingRequest->created_at->diffForHumans() }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <form action="{{ route('role-requests.cancel', $pendingRequest) }}" method="POST" onsubmit="return confirm('Yakin ingin membatalkan request ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-red-600 hover:bg-red-100 rounded-lg transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <!-- Request Form -->
                        <form method="POST" action="{{ route('role-requests.store') }}" class="space-y-6">
                            @csrf

                            <!-- Current Roles Display -->
                            <div class="bg-gradient-to-br from-blue-50 to-cyan-50 rounded-xl p-4 border-2 border-blue-200">
                                <h4 class="font-bold text-gray-900 mb-3 flex items-center gap-2 text-sm">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Role Saat Ini
                                </h4>
                                <div class="flex flex-wrap gap-2">
                                    @if(method_exists($user, 'getRoleNames') && $user->getRoleNames()->count() > 0)
                                        @foreach($user->getRoleNames() as $role)
                                            <span class="px-3 py-1.5 bg-gradient-to-r from-blue-500 to-cyan-500 text-white rounded-full text-sm font-semibold shadow-md">
                                                {{ ucfirst($role) }}
                                            </span>
                                        @endforeach
                                    @else
                                        <span class="text-gray-500 text-sm italic">Belum memiliki role</span>
                                    @endif
                                </div>
                            </div>

                            <!-- Role Selection -->
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-3">
                                    Pilih Role yang Diminta <span class="text-red-500">*</span>
                                </label>
                                <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                                    @foreach($availableRoles as $role)
                                        <label class="relative cursor-pointer group">
                                            <input type="checkbox" name="requested_roles[]" value="{{ $role->name }}" 
                                                   x-model="selectedRoles"
                                                   class="sr-only peer">
                                            <div class="p-3 bg-white border-2 border-gray-200 rounded-xl transition-all duration-300 peer-checked:border-purple-500 peer-checked:bg-purple-50 hover:border-purple-300 hover:shadow-md">
                                                <div class="flex items-center justify-between">
                                                    <span class="font-semibold text-sm text-gray-700 peer-checked:text-purple-700">
                                                        {{ ucfirst($role->name) }}
                                                    </span>
                                                    <svg class="w-5 h-5 text-purple-600 opacity-0 peer-checked:opacity-100 transition-opacity" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                    </svg>
                                                </div>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                                <p class="text-xs text-gray-500 mt-2">
                                    💡 Guest tidak dapat digabung dengan role lainnya.
                                </p>
                            </div>

                            <!-- Reason -->
                            <div>
                                <label for="reason" class="block text-sm font-bold text-gray-700 mb-2">
                                    Alasan Permintaan <span class="text-red-500">*</span>
                                </label>
                                <textarea id="reason" name="reason" rows="4" required
                                          class="block w-full rounded-xl border-2 border-gray-200 focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 transition-all duration-300 px-4 py-3 text-gray-900 placeholder-gray-400"
                                          placeholder="Jelaskan mengapa Anda membutuhkan role tersebut. Minimal 10 karakter.">{{ old('reason') }}</textarea>
                                <p class="text-xs text-gray-500 mt-2">
                                    Jelaskan secara detail alasan Anda memerlukan role ini.
                                </p>
                            </div>

                            <!-- Submit Button -->
                            <div class="flex items-center justify-between gap-4 pt-4 border-t-2 border-gray-200">
                                <button type="button" 
                                        @click="showModal = false"
                                        class="px-6 py-3 bg-gray-200 text-gray-700 font-semibold rounded-xl hover:bg-gray-300 transition-colors">
                                    Batal
                                </button>
                                <button type="submit" class="px-6 py-3 bg-gradient-to-r from-purple-500 to-pink-600 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl hover:scale-105 transition-all duration-300 flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                    </svg>
                                    Ajukan Permintaan
                                </button>
                            </div>
                        </form>
                    @endif

                    <!-- Request History -->
                    @if($roleRequests->count() > 0)
                        <div class="mt-6 pt-6 border-t-2 border-gray-200">
                            <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Riwayat Permintaan
                            </h3>
                            <div class="space-y-3 max-h-96 overflow-y-auto">
                                @foreach($roleRequests as $request)
                                    <div class="bg-white border-2 {{ $request->status === 'pending' ? 'border-yellow-200' : ($request->status === 'approved' ? 'border-green-200' : 'border-red-200') }} rounded-xl p-4 shadow-sm">
                                        <div class="flex items-start justify-between gap-4">
                                            <div class="flex-1">
                                                <div class="flex items-center gap-2 mb-2">
                                                    @if($request->status === 'pending')
                                                        <span class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-semibold">Pending</span>
                                                    @elseif($request->status === 'approved')
                                                        <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold">Disetujui</span>
                                                    @else
                                                        <span class="px-2 py-1 bg-red-100 text-red-700 rounded-full text-xs font-semibold">Ditolak</span>
                                                    @endif
                                                    <span class="text-xs text-gray-500">{{ $request->created_at->format('d M Y') }}</span>
                                                </div>
                                                
                                                <div class="flex flex-wrap gap-1.5 mb-2">
                                                    @foreach($request->requested_roles as $role)
                                                        <span class="px-2 py-0.5 bg-gradient-to-r from-purple-500 to-pink-500 text-white rounded-full text-xs font-semibold">
                                                            {{ ucfirst($role) }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                                
                                                <p class="text-xs text-gray-700">
                                                    <span class="font-semibold">Alasan:</span> {{ Str::limit($request->reason, 80) }}
                                                </p>
                                                
                                                @if($request->status !== 'pending' && $request->review_note)
                                                    <div class="mt-2 p-2 bg-gray-50 rounded-lg border border-gray-200">
                                                        <p class="text-xs text-gray-600">
                                                            <span class="font-semibold">Catatan HR:</span> {{ $request->review_note }}
                                                        </p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>

<!-- Auto-open modal if there are errors -->
@if($errors->any())
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            window.dispatchEvent(new CustomEvent('open-role-request-modal'));
        });
    </script>
@endif
