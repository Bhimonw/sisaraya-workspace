<!-- Modal untuk Tambah Data -->
<div x-data="{ 
        showModal: false, 
        activeTab: 'skills',
        jenis: 'uang',
        jumlahUang: '',
        formatRupiah(value) {
            // Hapus karakter non-digit
            let number = value.replace(/[^,\d]/g, '');
            
            // Format dengan titik sebagai pemisah ribuan
            let formatted = number.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            
            return formatted;
        },
        updateJumlahUang(event) {
            const input = event.target;
            const value = input.value;
            
            // Hapus format dan ambil hanya angka
            const numbers = value.replace(/[^0-9]/g, '');
            
            // Update nilai asli (untuk submit)
            this.jumlahUang = numbers;
            
            // Format untuk tampilan
            if (numbers) {
                input.value = 'Rp ' + this.formatRupiah(numbers);
            } else {
                input.value = '';
            }
        },
        disableInactiveTabs(event) {
            // Disable semua input di tab yang tidak aktif sebelum submit
            const form = event.target;
            const tabs = form.querySelectorAll('[x-show]');
            
            tabs.forEach(tab => {
                const isVisible = tab.style.display !== 'none';
                if (!isVisible) {
                    // Disable semua input/select/textarea di tab yang hidden
                    tab.querySelectorAll('input, select, textarea').forEach(field => {
                        field.disabled = true;
                    });
                }
            });
        }
    }" 
     @open-add-modal.window="showModal = true; activeTab = $event.detail || 'skills'; jenis = 'uang'; jumlahUang = ''"
     @keydown.escape.window="showModal = false"
     x-show="showModal"
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto"
     style="display: none;">
    
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity" 
         @click="showModal = false"></div>

    <!-- Modal Container -->
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="relative bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-hidden"
             @click.stop>
            
            <!-- Modal Header -->
            <div class="bg-gradient-to-r from-blue-600 to-purple-600 px-6 py-5 flex items-center justify-between">
                <h3 class="text-xl font-bold text-white flex items-center gap-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Tambah Data Anggota
                </h3>
                <button @click="showModal = false" class="text-white/80 hover:text-white hover:bg-white/10 p-2 rounded-lg transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Tab Navigation -->
            <div class="flex border-b border-gray-200 bg-gray-50 px-6">
                <button @click="activeTab = 'skills'" 
                        :class="activeTab === 'skills' ? 'border-blue-500 text-blue-600 bg-white' : 'border-transparent text-gray-600 hover:text-gray-900 hover:border-gray-300'"
                        class="flex items-center gap-2 px-4 py-3 border-b-2 font-semibold text-sm transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                    </svg>
                    Keahlian
                </button>
                <button @click="activeTab = 'modal'" 
                        :class="activeTab === 'modal' ? 'border-green-500 text-green-600 bg-white' : 'border-transparent text-gray-600 hover:text-gray-900 hover:border-gray-300'"
                        class="flex items-center gap-2 px-4 py-3 border-b-2 font-semibold text-sm transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Modal
                </button>
                <button @click="activeTab = 'links'" 
                        :class="activeTab === 'links' ? 'border-purple-500 text-purple-600 bg-white' : 'border-transparent text-gray-600 hover:text-gray-900 hover:border-gray-300'"
                        class="flex items-center gap-2 px-4 py-3 border-b-2 font-semibold text-sm transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                    </svg>
                    Link & Kontak
                </button>
            </div>

            <!-- Modal Content -->
            <div class="overflow-y-auto max-h-[calc(90vh-180px)]">
                <form method="POST" action="{{ route('member-data.store') }}" class="p-6" 
                      @submit="disableInactiveTabs($event)">
                    @csrf

                    <!-- Skills Tab -->
                    <div x-show="activeTab === 'skills'" x-cloak>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Keahlian *</label>
                                <input type="text" name="skills[0][nama_skill]" :required="activeTab === 'skills'"
                                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Tingkat Keahlian *</label>
                                <select name="skills[0][tingkat_keahlian]" :required="activeTab === 'skills'"
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                                    <option value="">Pilih Tingkat</option>
                                    <option value="pemula">🌱 Pemula</option>
                                    <option value="menengah">⭐ Menengah</option>
                                    <option value="mahir">🔥 Mahir</option>
                                    <option value="expert">💎 Expert</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Deskripsi</label>
                                <textarea name="skills[0][deskripsi]" rows="3"
                                          class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Tab -->
                    <div x-show="activeTab === 'modal'" x-cloak>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Jenis Modal *</label>
                                <div class="grid grid-cols-2 gap-3">
                                    <label class="flex items-center gap-3 p-4 border-2 rounded-lg cursor-pointer transition hover:bg-green-50"
                                           :class="jenis === 'uang' ? 'border-green-500 bg-green-50' : 'border-gray-200'">
                                        <input type="radio" name="modals[0][jenis]" value="uang" x-model="jenis" :required="activeTab === 'modal'" class="text-green-600 focus:ring-green-500">
                                        <div>
                                            <div class="font-semibold text-gray-900">💵 Uang</div>
                                            <div class="text-xs text-gray-600">Modal dana tunai</div>
                                        </div>
                                    </label>
                                    <label class="flex items-center gap-3 p-4 border-2 rounded-lg cursor-pointer transition hover:bg-purple-50"
                                           :class="jenis === 'alat' ? 'border-purple-500 bg-purple-50' : 'border-gray-200'">
                                        <input type="radio" name="modals[0][jenis]" value="alat" x-model="jenis" :required="activeTab === 'modal'" class="text-purple-600 focus:ring-purple-500">
                                        <div>
                                            <div class="font-semibold text-gray-900">🛠️ Alat</div>
                                            <div class="text-xs text-gray-600">Peralatan kerja</div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                            <div x-show="jenis === 'alat'" x-cloak>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Alat/Barang *</label>
                                <input type="text" name="modals[0][nama_item]" :required="activeTab === 'modal' && jenis === 'alat'" placeholder="Contoh: Kamera Canon, Laptop Dell"
                                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition">
                            </div>
                            <div x-show="jenis === 'uang'" x-cloak>
                                <!-- Hidden input untuk nama_item ketika jenis uang (diisi otomatis) -->
                                <input type="hidden" name="modals[0][nama_item]" value="Modal Uang Tunai">
                            </div>
                            <div x-show="jenis === 'uang'" x-cloak>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Jumlah Uang</label>
                                <div class="relative">
                                    <input type="text" 
                                           @input="updateJumlahUang($event)"
                                           placeholder="Rp 0"
                                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition font-semibold text-lg text-green-700">
                                    <!-- Hidden input untuk value asli -->
                                    <input type="hidden" name="modals[0][jumlah_uang]" :value="jumlahUang">
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Format akan otomatis menjadi: Rp 1.000.000</p>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Deskripsi</label>
                                <textarea name="modals[0][deskripsi]" rows="3"
                                          class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition"></textarea>
                            </div>
                            <div>
                                <label class="flex items-center gap-3 p-4 bg-blue-50 border border-blue-200 rounded-lg cursor-pointer hover:bg-blue-100 transition">
                                    <input type="checkbox" name="modals[0][dapat_dipinjam]" value="1" class="text-blue-600 focus:ring-blue-500 w-5 h-5">
                                    <div>
                                        <div class="font-semibold text-gray-900">Dapat Dipinjam</div>
                                        <div class="text-xs text-gray-600">Item ini bisa dipinjam oleh anggota lain</div>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Links Tab -->
                    <div x-show="activeTab === 'links'" x-cloak>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Orang/Pemilik *</label>
                                <input type="text" name="links[0][nama]" :required="activeTab === 'links'" placeholder="Contoh: John Doe, PT. Maju Jaya"
                                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Bidang</label>
                                <input type="text" name="links[0][bidang]" placeholder="Contoh: Portfolio, GitHub, LinkedIn"
                                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">URL</label>
                                <input type="url" name="links[0][url]" placeholder="https://..."
                                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Kontak</label>
                                <input type="text" name="links[0][contact]" placeholder="Email, nomor WA, atau kontak lain"
                                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition">
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="mt-6 flex gap-3">
                        <button type="button" @click="showModal = false"
                                class="flex-1 px-6 py-3 border-2 border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition">
                            Batal
                        </button>
                        <button type="submit"
                                class="flex-1 px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-semibold rounded-lg hover:from-blue-700 hover:to-purple-700 transition shadow-lg">
                            Simpan Data
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
