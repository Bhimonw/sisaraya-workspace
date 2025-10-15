<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class SisarayaMembersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Seeder ini mengisi data 14 anggota Sisaraya berdasarkan Doc 6
     * Login menggunakan username (nama) dan password
     */
    public function run(): void
    {
        // Data anggota Sisaraya dari Doc 6
        $members = [
            [
                'name' => 'Bhimo',
                'username' => 'bhimo',
                'roles' => ['pm', 'sekretaris'], // Multiple roles
                'bio' => 'Project Manager & Sekretaris - Memimpin dan mengoordinasikan seluruh proyek, event, dan aktivitas komunitas. Bertanggung jawab pada dokumentasi resmi, surat-menyurat, serta timeline kegiatan.'
            ],
            [
                'name' => 'Bagas',
                'username' => 'bagas',
                'roles' => ['hr'],
                'bio' => 'Human Resource - Mengatur anggota komunitas, role tetap maupun guest, serta mengelola sistem perekrutan. Mengawasi voting anggota baru dan rotasi role utama.'
            ],
            [
                'name' => 'Dijah',
                'username' => 'dijah',
                'roles' => ['bendahara'],
                'bio' => 'Bendahara - Mengatur seluruh keuangan komunitas, membuat dan menyetujui RAB (umum atau proyek), serta menyimpan laporan transaksi ke Ruang Penyimpanan.'
            ],
            [
                'name' => 'Yahya',
                'username' => 'yahya',
                'roles' => ['pr'],
                'bio' => 'Head of Public Relation - Bertanggung jawab atas komunikasi eksternal, branding komunitas, serta menjembatani hubungan dengan mitra, sponsor, dan publik.'
            ],
            [
                'name' => 'Fadhil',
                'username' => 'fadhil',
                'roles' => ['pr'],
                'bio' => 'PR Staff - Mendukung Yahya dalam penyusunan rilis publik, korespondensi eksternal, dan pelaksanaan event publik.'
            ],
            [
                'name' => 'Robby',
                'username' => 'robby',
                'roles' => ['media'],
                'bio' => 'Main Designer - Memimpin seluruh tim desain visual dan menjaga identitas visual Sisaraya.'
            ],
            [
                'name' => 'Fauzan',
                'username' => 'fauzan',
                'roles' => ['media'],
                'bio' => 'Content Planner - Menyusun strategi konten dan bekerja sama dengan desain untuk publikasi di berbagai media.'
            ],
            [
                'name' => 'Aulia',
                'username' => 'aulia',
                'roles' => ['media'],
                'bio' => 'Social Media Specialist - Mengelola akun media sosial (IG, TikTok, X, YouTube), engagement, dan posting konten.'
            ],
            [
                'name' => 'Faris',
                'username' => 'faris',
                'roles' => ['media'],
                'bio' => 'Graphic Designer - Membuat dan menyiapkan materi visual untuk event dan publikasi.'
            ],
            [
                'name' => 'Ardhi',
                'username' => 'ardhi',
                'roles' => ['media'],
                'bio' => 'Media Editor - Mengelola konten multimedia seperti video, reels, atau konten promosi.'
            ],
            [
                'name' => 'Erge',
                'username' => 'erge',
                'roles' => ['media'],
                'bio' => 'Graphic Designer - Membantu proses revisi desain dan produksi konten.'
            ],
            [
                'name' => 'Gades',
                'username' => 'gades',
                'roles' => ['media'],
                'bio' => 'Graphic Designer - Desainer pendukung untuk event dan kampanye besar.'
            ],
            [
                'name' => 'Kafilah',
                'username' => 'kafilah',
                'roles' => ['kewirausahaan'],
                'bio' => 'Kewirausahaan - Mengelola unit bisnis komunitas, melaporkan progress usaha, mengunggah hasil penjualan dan laporan keuangan ke Ruang Penyimpanan.'
            ],
            [
                'name' => 'Agung',
                'username' => 'agung',
                'roles' => ['researcher'],
                'bio' => 'Researcher / Analyst - Melakukan riset dan analisis terhadap kegiatan, proyek, atau strategi komunitas. Memberikan laporan evaluasi kepada PM dan HR.'
            ],
        ];

        // Default password untuk semua anggota (bisa diubah setelah login pertama)
        $defaultPassword = Hash::make('password');

        $this->command->info('🌱 Seeding Sisaraya Members...');
        $this->command->newLine();

        foreach ($members as $memberData) {
            // Cek apakah user sudah ada (by username)
            $user = User::where('username', $memberData['username'])->first();

            if (!$user) {
                // Buat user baru
                $user = User::create([
                    'name' => $memberData['name'],
                    'username' => $memberData['username'],
                    'password' => $defaultPassword,
                    'bio' => $memberData['bio'],
                ]);

                $this->command->info("✅ Created user: {$memberData['name']} (@{$memberData['username']})");
            } else {
                $this->command->warn("⚠️  User already exists: {$memberData['name']} (@{$memberData['username']})");
            }

            // Assign roles
            foreach ($memberData['roles'] as $roleName) {
                $role = Role::where('name', $roleName)->first();
                
                if ($role) {
                    if (!$user->hasRole($roleName)) {
                        $user->assignRole($roleName);
                        $this->command->info("   └─ Assigned role: {$roleName}");
                    } else {
                        $this->command->comment("   └─ Already has role: {$roleName}");
                    }
                } else {
                    $this->command->error("   └─ Role not found: {$roleName}");
                }
            }

            $this->command->newLine();
        }

        $this->command->info('✨ Sisaraya Members seeding completed!');
        $this->command->newLine();
        $this->command->info('📝 Login credentials:');
        $this->command->info('   Username: [nama] (contoh: bhimo, bagas, dijah)');
        $this->command->info('   Password: password');
        $this->command->newLine();
        $this->command->info('💡 Tip: Ubah password setelah login pertama!');
    }
}
