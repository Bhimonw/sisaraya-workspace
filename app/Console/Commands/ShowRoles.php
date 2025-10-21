<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;

class ShowRoles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'roles:show';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Display all roles in the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $roles = Role::orderBy('name')->get(['id', 'name']);
        
        $this->info('📋 Roles in database:');
        $this->newLine();
        
        $rows = $roles->map(fn($role) => [
            $role->id,
            $role->name,
        ])->toArray();
        
        $this->table(['ID', 'Role Name'], $rows);
        
        $this->info("Total: {$roles->count()} roles");
        
        return 0;
    }
}
