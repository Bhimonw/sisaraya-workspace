<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;

class CleanNonStandardRoles extends Command
{
    protected $signature = 'roles:clean-non-standard';
    protected $description = 'Remove non-standard roles (head, Anggota)';

    public function handle()
    {
        $this->info('Cleaning non-standard roles...');
        
        // Handle Anggota -> member migration
        $anggota = Role::where('name', 'Anggota')->first();
        if ($anggota) {
            $users = User::role('Anggota')->get();
            $this->info("Found {$users->count()} users with Anggota role");
            
            foreach ($users as $user) {
                if (!$user->hasRole('member')) {
                    $user->assignRole('member');
                }
                $user->removeRole('Anggota');
                $this->line("  ✓ Migrated {$user->username} from Anggota to member");
            }
            
            $anggota->delete();
            $this->info('✓ Anggota role deleted');
        }
        
        // Handle head role removal
        $head = Role::where('name', 'head')->first();
        if ($head) {
            $users = User::role('head')->get();
            $this->info("Found {$users->count()} users with head role");
            
            foreach ($users as $user) {
                $user->removeRole('head');
                $this->line("  ✓ Removed head role from {$user->username}");
            }
            
            $head->delete();
            $this->info('✓ Head role deleted');
        }
        
        $this->newLine();
        $this->info('✓ Cleanup complete!');
        
        // Show remaining roles
        $this->newLine();
        $this->info('Remaining roles:');
        $roles = Role::orderBy('name')->get();
        $this->table(['ID', 'Name'], $roles->map(fn($r) => [$r->id, $r->name]));
        
        return 0;
    }
}
