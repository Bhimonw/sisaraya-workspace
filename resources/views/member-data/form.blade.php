<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Tambah Data Kepegawaian
            </h2>
            <a href="{{ route('member-data.index') }}" class="text-gray-600 hover:text-gray-800">
                ← Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('member-data.store') }}" class="space-y-6" x-data="{
                skills: [{ nama_skill: '', tingkat_keahlian: 'pemula', deskripsi: '' }],
                modals: [{ jenis: 'uang', nama_item: '', jumlah_uang: '', deskripsi: '', dapat_dipinjam: false }],
                links: [{ nama: '', bidang: '', url: '', contact: '' }]
            }">
                @csrf

                <!-- Skills Section -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">Keahlian / Skills</h3>
                        
                        <template x-for="(skill, index) in skills" :key="index">
                            <div class="border rounded-lg p-4 mb-4">
                                <div class="flex justify-between items-start mb-3">
                                    <span class="text-sm font-medium text-gray-700">Skill #<span x-text="index + 1"></span></span>
                                    <button type="button" @click="skills.splice(index, 1)" x-show="skills.length > 1" class="text-red-600 hover:text-red-800 text-sm">
                                        Hapus
                                    </button>
                                </div>
                                
                                <div class="space-y-3">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Nama Keahlian*</label>
                                        <input type="text" :name="'skills[' + index + '][nama_skill]'" x-model="skill.nama_skill" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Tingkat Keahlian*</label>
                                        <select :name="'skills[' + index + '][tingkat_keahlian]'" x-model="skill.tingkat_keahlian" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                            <option value="pemula">Pemula</option>
                                            <option value="menengah">Menengah</option>
                                            <option value="mahir">Mahir</option>
                                            <option value="expert">Expert</option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Deskripsi</label>
                                        <textarea :name="'skills[' + index + '][deskripsi]'" x-model="skill.deskripsi" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                                    </div>
                                </div>
                            </div>
                        </template>
                        
                        <button type="button" @click="skills.push({ nama_skill: '', tingkat_keahlian: 'pemula', deskripsi: '' })" class="w-full py-2 border-2 border-dashed border-gray-300 rounded-lg text-gray-600 hover:border-blue-400 hover:text-blue-600">
                            + Tambah Skill
                        </button>
                    </div>
                </div>

                <!-- Modal Section -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">Modal (Uang / Alat)</h3>
                        
                        <template x-for="(modal, index) in modals" :key="index">
                            <div class="border rounded-lg p-4 mb-4">
                                <div class="flex justify-between items-start mb-3">
                                    <span class="text-sm font-medium text-gray-700">Modal #<span x-text="index + 1"></span></span>
                                    <button type="button" @click="modals.splice(index, 1)" x-show="modals.length > 1" class="text-red-600 hover:text-red-800 text-sm">
                                        Hapus
                                    </button>
                                </div>
                                
                                <div class="space-y-3">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Jenis*</label>
                                        <select :name="'modals[' + index + '][jenis]'" x-model="modal.jenis" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                            <option value="uang">Uang</option>
                                            <option value="alat">Alat</option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Nama Item*</label>
                                        <input type="text" :name="'modals[' + index + '][nama_item]'" x-model="modal.nama_item" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    </div>
                                    
                                    <div x-show="modal.jenis === 'uang'">
                                        <label class="block text-sm font-medium text-gray-700">Jumlah Uang (Rp)</label>
                                        <input type="number" :name="'modals[' + index + '][jumlah_uang]'" x-model="modal.jumlah_uang" min="0" step="1000" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Deskripsi</label>
                                        <textarea :name="'modals[' + index + '][deskripsi]'" x-model="modal.deskripsi" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                                    </div>
                                    
                                    <div class="flex items-center">
                                        <input type="checkbox" :name="'modals[' + index + '][dapat_dipinjam]'" :id="'dapat_dipinjam_' + index" x-model="modal.dapat_dipinjam" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                        <label :for="'dapat_dipinjam_' + index" class="ml-2 block text-sm text-gray-900">Dapat dipinjam oleh anggota lain</label>
                                    </div>
                                </div>
                            </div>
                        </template>
                        
                        <button type="button" @click="modals.push({ jenis: 'uang', nama_item: '', jumlah_uang: '', deskripsi: '', dapat_dipinjam: false })" class="w-full py-2 border-2 border-dashed border-gray-300 rounded-lg text-gray-600 hover:border-blue-400 hover:text-blue-600">
                            + Tambah Modal
                        </button>
                    </div>
                </div>

                <!-- Links Section -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">Link & Kontak Eksternal</h3>
                        
                        <template x-for="(link, index) in links" :key="index">
                            <div class="border rounded-lg p-4 mb-4">
                                <div class="flex justify-between items-start mb-3">
                                    <span class="text-sm font-medium text-gray-700">Link #<span x-text="index + 1"></span></span>
                                    <button type="button" @click="links.splice(index, 1)" x-show="links.length > 1" class="text-red-600 hover:text-red-800 text-sm">
                                        Hapus
                                    </button>
                                </div>
                                
                                <div class="space-y-3">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Nama*</label>
                                        <input type="text" :name="'links[' + index + '][nama]'" x-model="link.nama" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Bidang</label>
                                        <input type="text" :name="'links[' + index + '][bidang]'" x-model="link.bidang" placeholder="e.g. Portfolio, LinkedIn, Instagram" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">URL</label>
                                        <input type="url" :name="'links[' + index + '][url]'" x-model="link.url" placeholder="https://..." class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Kontak</label>
                                        <input type="text" :name="'links[' + index + '][contact]'" x-model="link.contact" placeholder="e.g. @username, email, nomor telepon" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    </div>
                                </div>
                            </div>
                        </template>
                        
                        <button type="button" @click="links.push({ nama: '', bidang: '', url: '', contact: '' })" class="w-full py-2 border-2 border-dashed border-gray-300 rounded-lg text-gray-600 hover:border-blue-400 hover:text-blue-600">
                            + Tambah Link
                        </button>
                    </div>
                </div>

                <!-- Submit Section -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-blue-700">
                                        Data yang Anda kirim akan diterima oleh <strong>Sekretaris</strong> untuk dikelola dan digunakan dalam koordinasi kolektif.
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex justify-end gap-3">
                            <a href="{{ route('member-data.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                                Batal
                            </a>
                            <button type="submit" class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg font-medium">
                                Simpan & Kirim ke Sekretaris
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
