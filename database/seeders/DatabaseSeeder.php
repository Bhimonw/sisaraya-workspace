<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        echo "Starting database seeding...\n\n";
        
        // 1. Seed roles first
        $this->call(RolesSeeder::class);
        
        echo "\n";
        
        // 2. Seed Sisaraya members (14 users)
        $this->call(SisarayaMembersSeeder::class);
        
        echo "\nDatabase seeding completed successfully!\n";
    }
}
