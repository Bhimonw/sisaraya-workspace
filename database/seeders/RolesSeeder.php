<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        echo "🌱 Seeding Roles & Permissions...\n\n";

        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create roles
        $roles = [
            'pm' => 'Project Manager',
            'hr' => 'Human Resources',
            'sekretaris' => 'Sekretaris',
            'bendahara' => 'Bendahara',
            'media' => 'Media',
            'pr' => 'Public Relations',
            'researcher' => 'Researcher',
            'talent_manager' => 'Talent Manager',
            'talent' => 'Talent',
            'kewirausahaan' => 'Kewirausahaan',
            'guest' => 'Guest',
        ];

        foreach ($roles as $name => $label) {
            Role::firstOrCreate(
                ['name' => $name],
                ['guard_name' => 'web']
            );
            echo "✅ Created role: {$label} ({$name})\n";
        }

        echo "\n✨ Roles seeding completed!\n";
    }
}
