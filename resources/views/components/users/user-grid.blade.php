{{-- 
    User Grid Component
    
    Menampilkan koleksi users dalam layout grid responsif menggunakan user-card.
    
    Props:
    - users (required): Collection dari User models
    
    Layout:
    - Mobile (< 768px): 1 kolom
    - Tablet (768px - 1024px): 2 kolom
    - Desktop (> 1024px): 3 kolom
--}}

@props(['users'])

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    
    @forelse($users as $user)
        {{-- Render User Card --}}
        <x-users.user-card :user="$user" />
        
    @empty
        {{-- Empty State --}}
        <div class="col-span-full">
            <div class="bg-white rounded-2xl shadow-md border-2 border-dashed border-gray-300 p-16 text-center">
                <div class="max-w-md mx-auto">
                    {{-- Icon --}}
                    <svg class="h-24 w-24 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    
                    {{-- Message --}}
                    <h3 class="text-xl font-bold text-gray-700 mb-2">Belum Ada Anggota</h3>
                    <p class="text-gray-500 mb-6">
                        Belum ada anggota terdaftar dalam sistem. Tambahkan anggota pertama sekarang.
                    </p>
                </div>
            </div>
        </div>
    @endforelse
    
</div>
