<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // define roles (lowercase normalized) and permissions
        $roles = [
            'hr', 'pm', 'sekretaris', 'media', 'pr', 'talent_manager', 'researcher', 'talent', 'guest', 'bendahara', 'kewirausahaan'
        ];

        $permissions = [
            'users.manage',
            'projects.create',
            'projects.update',
            'projects.view',
            'projects.manage_members',
            'tickets.create',
            'tickets.update_status',
            'tickets.view_all',
            'documents.upload',
            'documents.view_all',
            // finance permissions for Bendahara
            'finance.manage_rab',
            'finance.upload_documents',
            'finance.view_reports'
            ,
            // business / kewirausahaan permissions
            'business.create',
            'business.view',
            'business.manage_talent',
            'business.upload_reports'
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        foreach ($roles as $r) {
            Role::firstOrCreate(['name' => $r]);
        }

        // assign sensible defaults
        Role::where('name', 'hr')->first()?->givePermissionTo(['users.manage', 'projects.view', 'tickets.view_all', 'documents.view_all']);
        Role::where('name', 'pm')->first()?->givePermissionTo(['projects.create', 'projects.update', 'projects.view', 'projects.manage_members', 'tickets.create', 'tickets.update_status', 'documents.upload']);
        Role::where('name', 'sekretaris')->first()?->givePermissionTo(['documents.upload', 'documents.view_all']);
        Role::where('name', 'media')->first()?->givePermissionTo(['documents.upload', 'tickets.update_status']);
        Role::where('name', 'pr')->first()?->givePermissionTo(['documents.view_all', 'tickets.update_status']);
        Role::where('name', 'talent_manager')->first()?->givePermissionTo(['tickets.create', 'tickets.update_status']);
    Role::where('name', 'researcher')->first()?->givePermissionTo(['documents.upload']);
    Role::where('name', 'talent')->first()?->givePermissionTo(['tickets.view_all']);
    Role::where('name', 'guest')->first()?->givePermissionTo([]);
    // bendahara defaults
    Role::where('name', 'bendahara')->first()?->givePermissionTo(['finance.manage_rab', 'finance.upload_documents', 'finance.view_reports', 'documents.upload', 'documents.view_all']);
        // kewirausahaan defaults
        Role::where('name', 'kewirausahaan')->first()?->givePermissionTo(['business.create', 'business.view', 'business.manage_talent', 'business.upload_reports', 'documents.upload']);

        // Backwards-compatibility for existing seeded roles (uppercased)
        // create uppercase aliases if missing
    $legacy = ['HR' => 'hr','PM' => 'pm','Sekretaris' => 'sekretaris','Anggota' => 'talent','Guest' => 'guest'];
    // note: if a legacy uppercase role 'Bendahara' is used elsewhere, create alias
    $legacy['Bendahara'] = 'bendahara';
        foreach ($legacy as $old => $new) {
            Role::firstOrCreate(['name' => $old]);
            // also ensure users assigned to legacy roles can be migrated later
        }
    }
}
