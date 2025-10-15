{{-- Evaluation Section Component --}}
@props(['evaluable', 'type'])

@php
    $evaluations = $evaluable->evaluations()->with('researcher')->latest()->get();
    $canEdit = auth()->user()->hasRole('researcher');
    $typeName = $type === 'App\Models\Project' ? 'Proyek' : 'Event';
@endphp

<div class="mt-6 bg-white rounded-lg shadow-sm p-6" x-data="{ showForm: false, editingId: null }">
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-3">
            <svg class="h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <div>
                <h3 class="font-semibold text-lg text-gray-900">Evaluasi {{ $typeName }}</h3>
                <p class="text-xs text-gray-500">Catatan dari Researcher</p>
            </div>
        </div>
        
        @if($canEdit)
            <button @click="showForm = !showForm; editingId = null" 
                    class="text-sm px-3 py-1 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                <span x-show="!showForm">+ Tambah Evaluasi</span>
                <span x-show="showForm">✕ Tutup</span>
            </button>
        @endif
    </div>

    @if(!$canEdit && $evaluations->isEmpty())
        <div class="text-center py-8 text-gray-400">
            <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <p class="mt-2 text-sm">Belum ada evaluasi untuk {{ strtolower($typeName) }} ini</p>
        </div>
    @endif

    {{-- Form Create/Edit --}}
    @if($canEdit)
        <div x-show="showForm" x-collapse class="mb-4 p-4 bg-indigo-50 rounded border border-indigo-200">
            <form action="{{ route('evaluations.store') }}" method="POST">
                @csrf
                <input type="hidden" name="evaluable_type" value="{{ $type }}">
                <input type="hidden" name="evaluable_id" value="{{ $evaluable->id }}">
                
                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Catatan Evaluasi</label>
                        <textarea name="notes" required rows="4" 
                            class="w-full border border-gray-300 p-2 rounded focus:ring-2 focus:ring-indigo-500" 
                            placeholder="Tulis catatan evaluasi untuk {{ strtolower($typeName) }} ini..."></textarea>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select name="status" class="w-full border border-gray-300 p-2 rounded focus:ring-2 focus:ring-indigo-500">
                            <option value="draft">Draft (Hanya saya yang bisa lihat)</option>
                            <option value="published">Publish (Semua anggota bisa lihat)</option>
                        </select>
                    </div>
                </div>
                
                <div class="flex justify-end gap-2 mt-3">
                    <button type="button" @click="showForm = false" class="px-3 py-1 text-sm bg-gray-200 rounded hover:bg-gray-300">
                        Batal
                    </button>
                    <button type="submit" class="px-3 py-1 text-sm bg-indigo-600 text-white rounded hover:bg-indigo-700">
                        Simpan Evaluasi
                    </button>
                </div>
            </form>
        </div>
    @endif

    {{-- List Evaluations --}}
    <div class="space-y-4">
        @foreach($evaluations as $evaluation)
            @php
                $isOwner = auth()->id() === $evaluation->researcher_id;
                $canView = $evaluation->status === 'published' || $isOwner;
            @endphp
            
            @if($canView)
                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition" 
                     x-data="{ editing_{{ $evaluation->id }}: false }">
                    <div class="flex items-start justify-between mb-2">
                        <div class="flex items-center gap-2">
                            <div class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center">
                                <span class="text-sm font-medium text-indigo-700">
                                    {{ substr($evaluation->researcher->name, 0, 1) }}
                                </span>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $evaluation->researcher->name }}</p>
                                <p class="text-xs text-gray-500">{{ $evaluation->evaluated_at->diffForHumans() }}</p>
                            </div>
                        </div>
                        
                        <div class="flex items-center gap-2">
                            @if($evaluation->status === 'draft')
                                <span class="px-2 py-0.5 text-xs rounded bg-yellow-100 text-yellow-800">Draft</span>
                            @else
                                <span class="px-2 py-0.5 text-xs rounded bg-green-100 text-green-800">Published</span>
                            @endif
                            
                            @if($isOwner && $canEdit)
                                <button @click="editing_{{ $evaluation->id }} = !editing_{{ $evaluation->id }}" 
                                        class="text-gray-400 hover:text-indigo-600">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                    </svg>
                                </button>
                            @endif
                        </div>
                    </div>
                    
                    {{-- View Mode --}}
                    <div x-show="!editing_{{ $evaluation->id }}">
                        <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $evaluation->notes }}</p>
                    </div>
                    
                    {{-- Edit Mode --}}
                    @if($isOwner && $canEdit)
                        <div x-show="editing_{{ $evaluation->id }}" x-collapse>
                            <form action="{{ route('evaluations.update', $evaluation) }}" method="POST" class="mt-3">
                                @csrf
                                @method('PUT')
                                
                                <textarea name="notes" required rows="4" 
                                    class="w-full border border-gray-300 p-2 rounded text-sm focus:ring-2 focus:ring-indigo-500">{{ $evaluation->notes }}</textarea>
                                
                                <div class="mt-2">
                                    <select name="status" class="text-sm border border-gray-300 p-1.5 rounded focus:ring-2 focus:ring-indigo-500">
                                        <option value="draft" {{ $evaluation->status === 'draft' ? 'selected' : '' }}>Draft</option>
                                        <option value="published" {{ $evaluation->status === 'published' ? 'selected' : '' }}>Published</option>
                                    </select>
                                </div>
                                
                                <div class="flex justify-between mt-3">
                                    <button type="button" @click="editing_{{ $evaluation->id }} = false" 
                                            class="px-3 py-1 text-xs bg-gray-200 rounded hover:bg-gray-300">
                                        Batal
                                    </button>
                                    <div class="flex gap-2">
                                        <button type="button" 
                                                onclick="if(confirm('Hapus evaluasi ini?')) { document.getElementById('delete-form-{{ $evaluation->id }}').submit(); }"
                                                class="px-3 py-1 text-xs bg-red-100 text-red-700 rounded hover:bg-red-200">
                                            Hapus
                                        </button>
                                        <button type="submit" class="px-3 py-1 text-xs bg-indigo-600 text-white rounded hover:bg-indigo-700">
                                            Simpan Perubahan
                                        </button>
                                    </div>
                                </div>
                            </form>
                            
                            <form id="delete-form-{{ $evaluation->id }}" 
                                  action="{{ route('evaluations.destroy', $evaluation) }}" 
                                  method="POST" class="hidden">
                                @csrf
                                @method('DELETE')
                            </form>
                        </div>
                    @endif
                </div>
            @endif
        @endforeach
    </div>
</div>
