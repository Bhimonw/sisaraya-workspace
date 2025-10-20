{{-- Status & Priority Row --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
    {{-- Status --}}
    <div class="space-y-2">
        <label for="status" class="block text-sm font-semibold text-gray-900 flex items-center gap-1">
            <svg class="h-4 w-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Status Awal
        </label>
        <select name="status" 
                id="status"
                class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition-all duration-200 text-gray-900 appearance-none bg-white bg-no-repeat bg-right pr-10"
                style="background-image: url('data:image/svg+xml,%3Csvg xmlns=%27http://www.w3.org/2000/svg%27 fill=%27none%27 viewBox=%270 0 24 24%27 stroke=%27%236b7280%27%3E%3Cpath stroke-linecap=%27round%27 stroke-linejoin=%27round%27 stroke-width=%272%27 d=%27M19 9l-7 7-7-7%27/%3E%3C/svg%3E'); background-size: 1.5rem;">
            <option value="todo" selected>⏰ To Do - Belum dikerjakan</option>
            <option value="blackout">⚫ Blackout - Ditunda/dibatalkan</option>
        </select>
        <p class="text-xs text-gray-500 mt-1">Pilih "Blackout" untuk tiket yang sementara ditunda</p>
    </div>

    {{-- Priority --}}
    <div class="space-y-2">
        <label for="priority" class="block text-sm font-semibold text-gray-900 flex items-center gap-1">
            <svg class="h-4 w-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            Prioritas
        </label>
        <select name="priority" 
                id="priority"
                class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition-all duration-200 text-gray-900 appearance-none bg-white bg-no-repeat bg-right pr-10"
                style="background-image: url('data:image/svg+xml,%3Csvg xmlns=%27http://www.w3.org/2000/svg%27 fill=%27none%27 viewBox=%270 0 24 24%27 stroke=%27%236b7280%27%3E%3Cpath stroke-linecap=%27round%27 stroke-linejoin=%27round%27 stroke-width=%272%27 d=%27M19 9l-7 7-7-7%27/%3E%3C/svg%3E'); background-size: 1.5rem;">
            <option value="low">🟢 Rendah</option>
            <option value="medium" selected>🟡 Sedang</option>
            <option value="high">🟠 Tinggi</option>
            <option value="urgent">🔴 Mendesak</option>
        </select>
    </div>
</div>

{{-- Bobot & Deadline Row --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">

    {{-- Bobot --}}
    <div x-data="{ 
        weight: 5,
        getLabel() {
            if (this.weight <= 3) return { text: 'Ringan', color: 'text-green-600', bg: 'bg-green-50', border: 'border-green-200', emoji: '🪶' };
            if (this.weight <= 6) return { text: 'Sedang', color: 'text-yellow-600', bg: 'bg-yellow-50', border: 'border-yellow-200', emoji: '⚖️' };
            return { text: 'Berat', color: 'text-red-600', bg: 'bg-red-50', border: 'border-red-200', emoji: '🏋️' };
        }
    }" class="space-y-2">
        <label class="block text-sm font-semibold text-gray-900 flex items-center gap-1">
            <svg class="h-4 w-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/>
            </svg>
            Bobot
        </label>
        
        {{-- Weight Display --}}
        <div class="p-3 rounded-xl border-2 transition-all duration-200"
             :class="getLabel().bg + ' ' + getLabel().border">
            <div class="flex items-center justify-between">
                <span class="text-xs font-medium text-gray-600">Level:</span>
                <div class="flex items-center gap-1.5">
                    <span x-text="getLabel().emoji" class="text-lg"></span>
                    <span x-text="weight" 
                          class="text-xl font-bold"
                          :class="getLabel().color"></span>
                    <span class="text-xs font-bold px-2 py-0.5 rounded-full"
                          :class="getLabel().bg + ' ' + getLabel().color"
                          x-text="getLabel().text"></span>
                </div>
            </div>
        </div>
        
        {{-- Slider --}}
        <div class="flex items-center gap-3 px-1">
            <span class="text-xs font-medium text-gray-500">1</span>
            <input type="range" 
                   name="weight" 
                   min="1" 
                   max="10" 
                   value="5"
                   x-model="weight"
                   class="flex-1 h-2 bg-gray-200 rounded-full appearance-none cursor-pointer slider-modern">
            <span class="text-xs font-medium text-gray-500">10</span>
        </div>
    </div>

    {{-- Deadline --}}
    <div class="space-y-2">
        <label for="due_date" class="block text-sm font-semibold text-gray-900 flex items-center gap-1">
            <svg class="h-4 w-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            Deadline
        </label>
        <input type="date" 
               name="due_date" 
               id="due_date"
               class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition-all duration-200 text-gray-900">
    </div>
</div>
