<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;

class CleanupLegacyRoles extends Command
{
    protected $signature = 'roles:cleanup-legacy {--force : Actually delete roles even if users are assigned}';

    protected $description = 'Remove legacy uppercase role records (e.g., HR, PM, Anggota) if safe. Use --force to delete even if users assigned.';

    public function handle(): int
    {
        $legacyNames = ['HR','PM','Sekretaris','Anggota','Guest','Bendahara'];

        foreach ($legacyNames as $name) {
            $role = Role::where('name', $name)->first();
            if (! $role) {
                $this->line("Role '$name' not found, skipping.");
                continue;
            }

            $usersCount = $role->users()->count();
            $this->line("Legacy role '$name' found with {$usersCount} users.");

            if ($usersCount > 0 && ! $this->option('force')) {
                $this->line(" - skipping deletion because users exist. Use --force to override.");
                continue;
            }

            if ($this->option('force')) {
                $role->delete();
                $this->line(" - deleted role '$name'.");
            } else {
                $this->line(" - dry-run: would delete role '$name'.");
            }
        }

        $this->info('Cleanup complete.');
        return 0;
    }
}
