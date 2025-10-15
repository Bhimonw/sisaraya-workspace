<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Upload {{ $type === 'confidential' ? 'Dokumen Rahasia' : 'Dokumen Umum' }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if($errors->any())
                        <div class="mb-4 rounded-lg bg-red-50 p-4 text-red-800">
                            <strong>Error:</strong>
                            <ul class="mt-2 list-disc list-inside">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('documents.store') }}" 
                          method="POST" 
                          enctype="multipart/form-data"
                          class="space-y-6">
                        @csrf
                        <input type="hidden" name="is_confidential" value="{{ $type === 'confidential' ? '1' : '0' }}">

                        <!-- File Upload -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                File Dokumen *
                            </label>
                            <input type="file" 
                                   name="file" 
                                   required
                                   accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.jpg,.jpeg,.png,.gif,.zip,.rar"
                                   class="block w-full text-sm text-gray-500
                                          file:mr-4 file:py-2 file:px-4
                                          file:rounded-lg file:border-0
                                          file:text-sm file:font-semibold
                                          file:bg-blue-50 file:text-blue-700
                                          hover:file:bg-blue-100
                                          cursor-pointer border border-gray-300 rounded-lg" />
                            <p class="mt-2 text-xs text-gray-500">
                                Format: PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, JPG, JPEG, PNG, GIF, ZIP, RAR<br>
                                Maksimal: 10MB
                            </p>
                        </div>

                        <!-- Description -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Deskripsi</label>
                            <textarea name="description" 
                                      rows="3"
                                      placeholder="Berikan deskripsi singkat tentang dokumen ini..."
                                      class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('description') }}</textarea>
                        </div>

                        <!-- Project (Optional) -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Proyek (Opsional)</label>
                            <select name="project_id"
                                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">-- Tidak terkait proyek --</option>
                                @foreach(App\Models\Project::orderBy('name')->get() as $project)
                                    <option value="{{ $project->id }}" {{ old('project_id') == $project->id ? 'selected' : '' }}>
                                        {{ $project->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        @if($type === 'confidential')
                            <div class="rounded-lg bg-red-50 p-4">
                                <div class="flex">
                                    <svg class="h-5 w-5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 1.944A11.954 11.954 0 012.166 5C2.056 5.649 2 6.319 2 7c0 5.225 3.34 9.67 8 11.317C14.66 16.67 18 12.225 18 7c0-.682-.057-1.35-.166-2.001A11.954 11.954 0 0110 1.944zM11 14a1 1 0 11-2 0 1 1 0 012 0zm0-7a1 1 0 10-2 0v3a1 1 0 102 0V7z" clip-rule="evenodd"/>
                                    </svg>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-red-800">Dokumen Rahasia</h3>
                                        <p class="mt-1 text-sm text-red-700">
                                            Dokumen ini hanya dapat diakses oleh Sekretaris dan HR. Pastikan file yang Anda upload bersifat rahasia dan sensitif.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Submit -->
                        <div class="flex gap-3">
                            <button type="submit" 
                                    class="rounded-lg bg-blue-600 px-6 py-2 text-white hover:bg-blue-700">
                                Upload Dokumen
                            </button>
                            <a href="{{ route('documents.index', ['type' => $type]) }}" 
                               class="rounded-lg bg-gray-300 px-6 py-2 text-gray-700 hover:bg-gray-400">
                                Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
