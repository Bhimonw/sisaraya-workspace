<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;

class CleanDuplicateRoles extends Command
{
    protected $signature = 'roles:clean-duplicates {--dry-run : Show what would be done without making changes}';
    protected $description = 'Remove duplicate roles with different capitalization';

    public function handle()
    {
        $dryRun = $this->option('dry-run');
        
        $this->info('Checking for duplicate roles...');
        
        // Get all roles
        $allRoles = Role::all();
        $this->info("Total roles found: {$allRoles->count()}");
        
        // Show all current roles
        $this->table(['ID', 'Name', 'Guard'], $allRoles->map(fn($r) => [$r->id, $r->name, $r->guard_name]));
        
        // Standard role names (lowercase)
        $standardRoles = [
            'member',
            'hr',
            'pm',
            'sekretaris',
            'bendahara',
            'media',
            'pr',
            'talent_manager',
            'researcher',
            'talent',
            'kewirausahaan',
            'guest',
        ];
        
        $this->newLine();
        $this->info('Standard roles (lowercase): ' . implode(', ', $standardRoles));
        $this->newLine();
        
        // Find duplicates (case-insensitive)
        $duplicates = [];
        foreach ($allRoles as $role) {
            $lowerName = strtolower($role->name);
            if (!isset($duplicates[$lowerName])) {
                $duplicates[$lowerName] = [];
            }
            $duplicates[$lowerName][] = $role;
        }
        
        // Process duplicates
        $deleted = 0;
        $renamed = 0;
        
        foreach ($duplicates as $lowerName => $roles) {
            if (count($roles) > 1) {
                $this->warn("Found duplicates for: {$lowerName}");
                
                // Keep the one with correct lowercase format
                $correctRole = null;
                $toDelete = [];
                
                foreach ($roles as $role) {
                    if ($role->name === $lowerName) {
                        $correctRole = $role;
                        $this->line("  ✓ Keep: {$role->name} (ID: {$role->id})");
                    } else {
                        $toDelete[] = $role;
                        $this->line("  ✗ Delete: {$role->name} (ID: {$role->id})");
                    }
                }
                
                // If no correct role exists, rename the first one
                if (!$correctRole && count($roles) > 0) {
                    $correctRole = $roles[0];
                    if (!$dryRun) {
                        $correctRole->name = $lowerName;
                        $correctRole->save();
                    }
                    $this->line("  ↻ Rename: {$roles[0]->name} → {$lowerName} (ID: {$roles[0]->id})");
                    $renamed++;
                    array_shift($toDelete); // Remove from delete list
                }
                
                // Delete duplicates
                foreach ($toDelete as $role) {
                    if (!$dryRun) {
                        // Move users from duplicate role to correct role
                        $users = \App\Models\User::role($role->name)->get();
                        foreach ($users as $user) {
                            if (!$user->hasRole($correctRole->name)) {
                                $user->assignRole($correctRole->name);
                            }
                            $user->removeRole($role->name);
                        }
                        
                        $role->delete();
                    }
                    $deleted++;
                }
            }
        }
        
        $this->newLine();
        if ($dryRun) {
            $this->info("Dry run complete. Would delete {$deleted} roles and rename {$renamed} roles.");
            $this->comment('Run without --dry-run to apply changes.');
        } else {
            $this->info("✓ Deleted {$deleted} duplicate roles and renamed {$renamed} roles.");
        }
        
        return 0;
    }
}
