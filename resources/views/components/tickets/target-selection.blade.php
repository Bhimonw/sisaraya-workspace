{{-- Target Selection Component --}}
<div class="rounded-2xl border-2 border-gray-200 bg-gradient-to-br from-gray-50 to-white p-6 space-y-4">
    <div class="flex items-center gap-2 mb-2">
        <svg class="h-5 w-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
        </svg>
        <h4 class="text-base font-bold text-gray-900">Target Tiket</h4>
        <span class="text-xs text-gray-500 ml-auto">(Opsional)</span>
    </div>
    
    <div class="space-y-3">
        {{-- Option 1: Semua Orang --}}
        <label class="flex items-start p-4 rounded-xl border-2 cursor-pointer transition-all duration-200 hover:shadow-md"
               :class="targetType === 'all' ? 'border-indigo-500 bg-indigo-50' : 'border-gray-200 bg-white hover:border-indigo-200'">
            <input type="radio" name="target_type" value="all" checked
                   class="mt-1 text-indigo-600 focus:ring-indigo-500 focus:ring-offset-0"
                   x-model="targetType">
            <div class="ml-3 flex-1">
                <div class="flex items-center gap-2">
                    <span class="text-2xl">🌐</span>
                    <span class="text-sm font-bold text-gray-900">Semua Orang</span>
                </div>
                <p class="text-xs text-gray-600 mt-1">Tiket bisa diambil oleh siapa saja tanpa batasan role</p>
            </div>
        </label>

        {{-- Option 2: Role Tetap --}}
        <label class="flex items-start p-4 rounded-xl border-2 cursor-pointer transition-all duration-200 hover:shadow-md"
               :class="targetType === 'role' ? 'border-purple-500 bg-purple-50' : 'border-gray-200 bg-white hover:border-purple-200'">
            <input type="radio" name="target_type" value="role"
                   class="mt-1 text-purple-600 focus:ring-purple-500 focus:ring-offset-0"
                   x-model="targetType">
            <div class="ml-3 flex-1">
                <div class="flex items-center gap-2">
                    <span class="text-2xl">👥</span>
                    <span class="text-sm font-bold text-gray-900">Role Tetap</span>
                </div>
                <p class="text-xs text-gray-600 mt-1 mb-3">Targetkan ke semua user dengan role tertentu</p>
                <select name="target_role" 
                        id="target_role"
                        x-bind:disabled="targetType !== 'role'"
                        x-bind:required="targetType === 'role'"
                        class="w-full px-4 py-2.5 rounded-xl border-2 border-gray-200 focus:border-purple-500 focus:ring-4 focus:ring-purple-100 transition-all duration-200 text-sm disabled:bg-gray-100 disabled:cursor-not-allowed appearance-none bg-white bg-no-repeat bg-right pr-10"
                        style="background-image: url('data:image/svg+xml,%3Csvg xmlns=%27http://www.w3.org/2000/svg%27 fill=%27none%27 viewBox=%270 0 24 24%27 stroke=%27%236b7280%27%3E%3Cpath stroke-linecap=%27round%27 stroke-linejoin=%27round%27 stroke-width=%272%27 d=%27M19 9l-7 7-7-7%27/%3E%3C/svg%3E'); background-size: 1.5rem;">
                    <option value="">Pilih Role...</option>
                    @foreach(\App\Models\Ticket::getAvailableRoles() as $roleKey => $roleName)
                        <option value="{{ $roleKey }}">{{ $roleName }}</option>
                    @endforeach
                </select>
            </div>
        </label>

        {{-- Option 3: User Spesifik (Multiple) --}}
        <label class="flex items-start p-4 rounded-xl border-2 cursor-pointer transition-all duration-200 hover:shadow-md"
               :class="targetType === 'user' ? 'border-green-500 bg-green-50' : 'border-gray-200 bg-white hover:border-green-200'">
            <input type="radio" name="target_type" value="user"
                   class="mt-1 text-green-600 focus:ring-green-500 focus:ring-offset-0"
                   x-model="targetType">
            <div class="ml-3 flex-1">
                <div class="flex items-center gap-2">
                    <span class="text-2xl">👤</span>
                    <span class="text-sm font-bold text-gray-900">User Spesifik</span>
                    <span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full font-semibold">Multiple</span>
                </div>
                <p class="text-xs text-gray-600 mt-1 mb-3">Pilih beberapa user sekaligus (1 tiket per user)</p>
                
                {{-- Checkbox List Container --}}
                <div class="border-2 rounded-xl bg-white max-h-64 overflow-y-auto transition-all duration-200"
                     :class="targetType === 'user' ? 'border-green-200' : 'border-gray-200 opacity-50 pointer-events-none'">
                    <div class="p-3 space-y-1">
                        @foreach(\App\Models\User::orderBy('name')->get() as $user)
                            <label class="flex items-center gap-3 cursor-pointer hover:bg-green-50 px-3 py-2.5 rounded-lg transition-colors group">
                                <input type="checkbox" 
                                       name="target_user_id[]" 
                                       value="{{ $user->id }}"
                                       x-bind:disabled="targetType !== 'user'"
                                       class="w-4 h-4 rounded border-2 border-gray-300 text-green-600 focus:ring-green-500 focus:ring-offset-0 disabled:cursor-not-allowed transition-all">
                                <div class="flex items-center gap-2 flex-1">
                                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-green-400 to-green-600 text-white text-xs font-bold flex items-center justify-center">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <span class="text-sm font-medium text-gray-700 group-hover:text-gray-900">{{ $user->name }}</span>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>
                <p class="text-xs text-gray-500 mt-2 flex items-center gap-1">
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Centang satu atau beberapa user yang ingin ditargetkan
                </p>
            </div>
        </label>
    </div>
</div>
