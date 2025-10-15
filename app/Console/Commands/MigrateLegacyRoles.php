<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;
use App\Models\User;

class MigrateLegacyRoles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'roles:migrate-legacy {--dry-run} {--remove-legacy : Remove legacy role assignment from users after mapping}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Map legacy role names (uppercase) to normalized roles and assign them to users. Use --dry-run to preview.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $mapping = [
            'HR' => 'hr',
            'PM' => 'pm',
            'Sekretaris' => 'sekretaris',
            'Anggota' => 'talent',
            'Guest' => 'guest',
            'Bendahara' => 'bendahara'
        ];

        $this->info('Starting legacy role migration' . ($this->option('dry-run') ? ' (dry-run)' : ''));

        foreach ($mapping as $old => $new) {
            $this->line("Checking legacy role: $old -> $new");

            $role = Role::where('name', $old)->first();
            $newRole = Role::where('name', $new)->first();

            if (! $role) {
                $this->line(" - legacy role '$old' not found, skipping");
                continue;
            }

            if (! $newRole) {
                $this->line(" - normalized role '$new' not found, creating it");
                if (! $this->option('dry-run')) {
                    $newRole = Role::create(['name' => $new]);
                }
            }

            // find users with legacy role
            $users = User::role($old)->get();
            $this->line(" - found {$users->count()} users with role '$old'");

            foreach ($users as $user) {
                $this->line("   - user: {$user->email} ({$user->id})");
                if (! $this->option('dry-run')) {
                    $user->assignRole($new);
                    $this->line("     -> assigned role '$new'");

                    if ($this->option('remove-legacy')) {
                        // remove legacy role assignment from user
                        if ($user->hasRole($old)) {
                            $user->removeRole($old);
                            $this->line("     -> removed legacy role '$old' from user");
                        } else {
                            $this->line("     -> user did not have legacy role '$old' assigned");
                        }
                    }
                } else {
                    $this->line("     (dry-run) would assign role '$new'");
                    if ($this->option('remove-legacy')) {
                        $this->line("     (dry-run) would remove legacy role '$old' from user");
                    }
                }
            }
        }

        $this->info('Legacy role migration complete');
        return 0;
    }
}
