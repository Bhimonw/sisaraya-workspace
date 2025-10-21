<!-- Modal untuk Edit Data -->
<div x-data="{ 
        showEditModal: false,
        editType: '',
        editId: null,
        editData: {},
        jenis: 'uang',
        jumlahUang: '',
        isSubmitting: false,
        formatRupiah(value) {
            let number = value.replace(/[^,\d]/g, '');
            let formatted = number.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            return formatted;
        },
        updateJumlahUang(event) {
            const input = event.target;
            const value = input.value;
            const numbers = value.replace(/[^0-9]/g, '');
            this.jumlahUang = numbers;
            if (numbers) {
                input.value = 'Rp ' + this.formatRupiah(numbers);
            } else {
                input.value = '';
            }
        },
        openEdit(type, id, data) {
            this.editType = type;
            this.editId = id;
            this.editData = {...data};
            if (type === 'modal') {
                this.jenis = data.jenis || 'uang';
                this.jumlahUang = data.jumlah_uang || '';
            }
            this.showEditModal = true;
        },
        resetEditForm() {
            this.editType = '';
            this.editId = null;
            this.editData = {};
            this.jenis = 'uang';
            this.jumlahUang = '';
            this.isSubmitting = false;
        }
    }" 
     @open-edit-modal.window="openEdit($event.detail.type, $event.detail.id, $event.detail.data)"
     @keydown.escape.window="showEditModal = false"
     x-show="showEditModal"
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto"
     style="display: none;">
    
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity" 
         @click="showEditModal = false"></div>

    <!-- Modal Container -->
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="relative bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-hidden"
             @click.stop>
            
            <!-- Modal Header -->
            <div class="bg-gradient-to-r from-blue-600 to-purple-600 px-6 py-5 flex items-center justify-between">
                <h3 class="text-xl font-bold text-white flex items-center gap-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    <span x-text="editType === 'skill' ? 'Edit Keahlian' : (editType === 'modal' ? 'Edit Modal' : 'Edit Link')"></span>
                </h3>
                <button @click="showEditModal = false; resetEditForm();" class="text-white/80 hover:text-white hover:bg-white/10 p-2 rounded-lg transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Modal Content -->
            <div class="overflow-y-auto max-h-[calc(90vh-180px)]">
                <form :action="`/member-data/${editType}/${editId}`" method="POST" class="p-6"
                      @submit="isSubmitting = true">
                    @csrf
                    @method('PATCH')

                    <!-- Edit Skill Form -->
                    <div x-show="editType === 'skill'" x-cloak>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Keahlian *</label>
                                <input type="text" name="nama_skill" x-model="editData.nama_skill" required
                                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Tingkat Keahlian *</label>
                                <select name="tingkat_keahlian" x-model="editData.tingkat_keahlian" required
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
                                <textarea name="deskripsi" x-model="editData.deskripsi" rows="3"
                                          class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Edit Modal Form -->
                    <div x-show="editType === 'modal'" x-cloak>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Jenis Modal *</label>
                                <div class="grid grid-cols-2 gap-3">
                                    <label class="flex items-center gap-3 p-4 border-2 rounded-lg cursor-pointer transition hover:bg-green-50"
                                           :class="editData.jenis === 'uang' ? 'border-green-500 bg-green-50' : 'border-gray-200'">
                                        <input type="radio" name="jenis" value="uang" x-model="editData.jenis" required class="text-green-600 focus:ring-green-500">
                                        <div>
                                            <div class="font-semibold text-gray-900">💵 Uang</div>
                                            <div class="text-xs text-gray-600">Modal dana tunai</div>
                                        </div>
                                    </label>
                                    <label class="flex items-center gap-3 p-4 border-2 rounded-lg cursor-pointer transition hover:bg-purple-50"
                                           :class="editData.jenis === 'alat' ? 'border-purple-500 bg-purple-50' : 'border-gray-200'">
                                        <input type="radio" name="jenis" value="alat" x-model="editData.jenis" required class="text-purple-600 focus:ring-purple-500">
                                        <div>
                                            <div class="font-semibold text-gray-900">🛠️ Alat</div>
                                            <div class="text-xs text-gray-600">Peralatan kerja</div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                            <div x-show="editData.jenis === 'alat'" x-cloak>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Alat/Barang *</label>
                                <input type="text" name="nama_item" x-model="editData.nama_item" :required="editData.jenis === 'alat'"
                                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition">
                            </div>
                            <div x-show="editData.jenis === 'uang'" x-cloak>
                                <input type="hidden" name="nama_item" value="Modal Uang Tunai">
                            </div>
                            <div x-show="editData.jenis === 'uang'" x-cloak>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Jumlah Uang</label>
                                <div class="relative">
                                    <input type="text" 
                                           :value="editData.jumlah_uang ? 'Rp ' + formatRupiah(String(editData.jumlah_uang)) : ''"
                                           @input="updateJumlahUang($event)"
                                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition font-semibold text-lg text-green-700">
                                    <input type="hidden" name="jumlah_uang" :value="jumlahUang || editData.jumlah_uang">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Deskripsi</label>
                                <textarea name="deskripsi" x-model="editData.deskripsi" rows="3"
                                          class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition"></textarea>
                            </div>
                            <div>
                                <label class="flex items-center gap-3 p-4 bg-blue-50 border border-blue-200 rounded-lg cursor-pointer hover:bg-blue-100 transition">
                                    <input type="checkbox" name="dapat_dipinjam" value="1" :checked="editData.dapat_dipinjam" class="text-blue-600 focus:ring-blue-500 w-5 h-5">
                                    <div>
                                        <div class="font-semibold text-gray-900">Dapat Dipinjam</div>
                                        <div class="text-xs text-gray-600">Item ini bisa dipinjam oleh anggota lain</div>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Edit Link Form -->
                    <div x-show="editType === 'link'" x-cloak>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Orang/Pemilik *</label>
                                <input type="text" name="nama" x-model="editData.nama" required
                                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Bidang</label>
                                <input type="text" name="bidang" x-model="editData.bidang"
                                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">URL</label>
                                <input type="url" name="url" x-model="editData.url"
                                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Kontak</label>
                                <input type="text" name="contact" x-model="editData.contact"
                                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition">
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="mt-6 flex gap-3">
                        <button type="button" @click="showEditModal = false; resetEditForm();"
                                :disabled="isSubmitting"
                                class="flex-1 px-6 py-3 border-2 border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition disabled:opacity-50 disabled:cursor-not-allowed">
                            Batal
                        </button>
                        <button type="submit"
                                :disabled="isSubmitting"
                                class="flex-1 px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-semibold rounded-lg hover:from-blue-700 hover:to-purple-700 transition shadow-lg disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                            <svg x-show="isSubmitting" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span x-text="isSubmitting ? 'Menyimpan...' : 'Simpan Perubahan'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
